<?php

/** 
 *        _      _            _
 *  _ __ (_)_ _ (_)_ __ _____| |__
 * | '  \| | ' \| \ V  V / -_) '_ \
 * |_|_|_|_|_||_|_|\_/\_/\___|_.__/
 *
 * This file is part of Kristuff\MiniWeb.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @version    0.9.2
 * @copyright  2017-2020 Kristuff
 */

namespace Kristuff\Miniweb\Auth\Model;

use Kristuff\Miniweb\Mail\Mailer;
use Kristuff\Miniweb\Mvc\TaskResponse;
use Kristuff\Miniweb\Mvc\Application;
use Kristuff\Miniweb\Auth\Model\UserRegistrationModel;
use Kristuff\Miniweb\Auth\Model\UserLoginModel;

/** 
 * Class UserInvitationModel
 *
 * Handles the user invitation process
 * Use parts of registration model 
 */
class UserInvitationModel extends UserRegistrationModel
{
    /** 
     * Gets whether the invitation process is enabled or not
     * 
     * @access public
     * @static
     *
     * @return bool         True if the the invitation process is enabled, otherwise false.
     */
    public static function isInvitationEnabled()
    {
        return self::config('AUTH_INVITATION_ENABLED') === true; 
    }

    /** 
     * Invite new user
     *
     * Creates a new account and send an invitation email to a user with a link to complete its registration (= define name and password).
     * This action expects the token named 'usersToken' given by UserAdminModel::getUserAdminDatas() to be passed as argument. 
     * This action need ADMIN permissions. The possible response codes are: 
     * - 200 (success) 
     * - 403 (no admin) 
     * - 405 (invalid userEmail or invalid token) 
     * - 500 (Houston we..)
     *
     * @access public
     * @static
     * @param string        $userEmail          The user's email address.
     * @param string        $token              The token value
     * @param string        $tokenKey           The token key
     *
     * @return TaskResponse
     */
    public static function inviteNewUser(?string $userEmail = null, 
                                         ?string $token = null, 
                                         ?string $tokenKey = null)
	{
        // the return response
        $response = TaskResponse::create();
        
        // Check token and admin permissions
        if (self::validateToken($response, $token, $tokenKey) && 
            UserLoginModel::validateAdminPermissions($response)){

            // clean the input and create temp userName
		    $userEmail = strip_tags($userEmail);
            $userName = 'user' . uniqid();

            // validate name and email
            // stop registration flow if anything breaks the input check rules
		    if (self::validateUserNamePattern($response, $userName) &&
                self::validateUserNameNoConflict($response, $userName) &&
                self::validateUserEmailPattern($response, $userEmail, $userEmail) &&
                self::validateUserEmailNoConflict($response, $userEmail)){

                // generate random hash for email verification (40 char string)
		        $userActivationHash = sha1(uniqid(mt_rand(), true));

		        // write user data to database WITHOUT PASSWORD
		        if ($response->assertTrue(self::writeNewUser($userEmail, $userName, null, $userActivationHash), 500, 
                                          self::text('USER_NEW_ACCOUNT_ERROR_CREATION_FAILED'))){

		            // get user_id of the user that has been created, to keep things clean we DON'T use lastInsertId() here
		            $userId = self::getUserIdByUsername($userName);
                    if ($response->assertTrue($userId !== false, 500, self::text('UNKNOWN_ERROR'))){

                        // send verification email
                        $mailSent = self::sendInvitationEmail($userId, $userEmail, $userActivationHash);
		                if ($response->assertTrue($mailSent, 500, self::text('USER_NEW_ACCOUNT_MAIL_SENDING_ERROR'))) {

                            // set success message
                            $response->setMessage(self::text('USER_INVITATION_EMAIL_SENT_SUCCESSFULLY'));
                        }
                    }
                }
		    }
        }

        // return response
        return $response;
	}
    
    /**
	 * Verify invited user
     * 
     * Checks the id/verification code combination
	 *
     * @access public
     * @static
	 * @param  int              $userId                     The user's id
	 * @param  string           $userActivationHash         The user's mail verification hash string
	 *
	 * @return TaskResponse
	 */
	public static function verifyInvitedUser($userId, $userActivationHash)
	{
        $count = (int) self::database()->select()
                                 ->count('userId')
                                 ->from('user')
                                 ->whereEqual('userId', (int) $userId)
                                 ->whereEqual('userActivationHash', $userActivationHash)
                                 ->getColumn();
       
        // create response        
        $response = TaskResponse::create();

        if ($response->assertTrue($count === 1, 500, self::text('USER_NEW_ACCOUNT_ACTIVATION_FAILED') . $count)){
            $response->setMessage(self::text('USER_INVITATION_VALIDATION_SUCCESSFUL'));
        }

        // return response
        return $response;
	}

    /**
	 * Complete registration
     * 
     * Complete the registration of an invited user. 
	 *
     * @access public
     * @static
	 * @param  int              $userId                 The user's id.
     * @param  string           $userName               The user's name.
	 * @param  string           $userPassword           The user's password.
	 * @param  string           $userPasswordRepeat     The repeated user's password.
	 * @param  string           $userActivationHash     The user's mail verification hash string
	 *
     * @return TaskResponse
	 */
	public static function completeRegistration($userId, $userName, $userPassword, $userPasswordRepeat, $userActivationHash)
	{
        // the return response
        $response = TaskResponse::create();
        
        // clean the input
		$userName = strip_tags($userName);

		// input checks (id name and password)
		if (self::validateUserId($response, $userId) && 
            self::validateUserNamePattern($response, $userName) &&
            self::validateUserNameNoConflict($response, $userName) &&
            self::validateUserPassword($response, $userPassword, $userPasswordRepeat)){
                
            // crypt the password with the PHP 5.5's password_hash() function, results in a 60 character hash string.
		    // @see php.net/manual/en/function.password-hash.php for more, especially for potential options
		    $userPasswordHash = password_hash($userPassword, PASSWORD_DEFAULT);
            
            // try to update            
            $updated = self::updateAndActivateInvitedUser($userId, $userName, $userPasswordHash, $userActivationHash);

            // set feedback message
            if ($response->assertTrue($updated, 405, self::text('USER_NEW_ACCOUNT_ACTIVATION_FAILED'))) {
                $response->setMessage(self::text('USER_NEW_ACCOUNT_ACTIVATION_SUCCESSFUL'));
            }
        }

        // return response
        return $response;
	}

    /**
	 * Complete registration by setting the password and user name in database
     *
     * @access protected
     * @static
	 * @param  int              $userId                 The user's id.
     * @param  string           $userName               The user's name.
	 * @param  string           $userPasswordHash       The hashed user's password.
	 * @param  string           $userActivationHash     The user's mail verification hash string
	 *
	 * @return bool             True if the user profile has been successfully updated, otherwise False.
	 */
	protected static function updateAndActivateInvitedUser($userId, $userName, $userPasswordHash,  $userActivationHash)
	{
        $userDirectory = \Kristuff\Miniweb\Security\Token::getNewToken(16);
        $query = self::database()->update('user')
                                 ->setValue('userName', $userName)
                                 ->setValue('userPasswordHash', $userPasswordHash)
                                 ->setValue('userActivationHash', null)
                                 ->setValue('userDataDirectory', $userDirectory)
                                 ->setValue('userActivated', 1)
                                 ->whereEqual('userId', (int) $userId)
                                 ->whereEqual('userActivationHash', $userActivationHash);

        return $query->execute() && $query->rowCount() === 1;
	}
  
    /**
	 * Sends an invitation email with link to complete the registration.
     *
     * @access protected
     * @static
	 * @param  int              $userId                 The user's id.
     * @param  string           $userEmail              The user's email address.
	 * @param  string           $userActivationHash     The user's mail verification hash string
	 *
	 * @return bool             True if mail has been sent successfully, otherwise False.
	 */
	protected static function sendInvitationEmail($userId, $userEmail, $userActivationHash)
	{
        $mailSubject      = sprintf(self::text('USER_INVITATION_EMAIL_SUBJECT'), self::config('APP_NAME'));
		$mailTitle        = self::text('USER_INVITATION_EMAIL_CONTENT_TITLE');
		$mailContentpart1 = sprintf(self::text('USER_INVITATION_EMAIL_CONTENT_PART_1'), self::config('APP_NAME') . ' on ' . Application::getUrl());
		$mailContentpart2 = self::text('USER_INVITATION_EMAIL_CONTENT_PART_2');
		$mailContentpart3 = self::text('USER_INVITATION_EMAIL_CONTENT_PART_3');
    	$mailContentpart4 = 'Regards,<br>' . self::config('AUTH_SIGNUP_EMAIL_VERIFICATION_FROM_NAME');
    
        $mailLinkUrl      = Application::getUrl() . self::config('AUTH_INVITATION_EMAIL_VERIFICATION_URL') . 
                          '/' . urlencode($userId) . 
                          '/' . urlencode($userActivationHash);
        $mailLinkText    =  'Create my account now'; //TODO local
        $mailAppName     =  self::config('APP_NAME') ;
        $mailCopyright   =  "Copyright ". (date("Y"))." ".self::config('APP_COPYRIGHT');

        $content = file_get_contents(self::config('VIEW_PATH').'auth/complete.email.html');
        $content = str_replace('{{mailTitle}}', $mailTitle, $content);
        $content = str_replace('{{mailSubject}}', $mailSubject, $content);
        $content = str_replace('{{mailContentpart1}}', $mailContentpart1, $content);
        $content = str_replace('{{mailContentpart2}}', $mailContentpart2, $content);
        $content = str_replace('{{mailContentpart3}}', $mailContentpart3, $content);
        $content = str_replace('{{mailContentpart4}}', $mailContentpart4, $content);
        $content = str_replace('{{mailAppName}}',      $mailAppName,      $content);
        $content = str_replace('{{mailCopyright}}',    $mailCopyright,    $content);
        
        $content = str_replace('{{mailLinkUrl}}',      $mailLinkUrl,    $content);
        $content = str_replace('{{mailLinkText}}',     $mailLinkText,   $content);

		$mail = new Mailer();
		$mailSent = $mail->sendMail($userEmail, 
                                    self::config('AUTH_SIGNUP_EMAIL_VERIFICATION_FROM_EMAIL'),
			                        self::config('AUTH_SIGNUP_EMAIL_VERIFICATION_FROM_NAME'), 
                                    $mailSubject, 
                                    $content, 
                                    true);
        
        return $mailSent ? true : false;
	}  
}