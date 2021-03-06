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

use Kristuff\Miniweb\Auth\Model\UserModel;
use Kristuff\Miniweb\Mvc\TaskResponse;
use Kristuff\Miniweb\Mvc\Application;
use Kristuff\Miniweb\Mail\Mailer;

/**
 * Class UserRecoveryModel
 *
 * Handles all the stuff that is related to the password-reset process.
 *
 * 1) UserRecoveryModel::verifyPasswordRecoveryRequest() => Verifies the recovery request (userName or email / captcha)
 *                                                          and sends email if successful 
 * 2) UserRecoveryModel::verifyPasswordResetLink()       => Verifies the password reset link sent by email
 * 3) UserRecoveryModel::handlePasswordReset()           => Handles the password reset form submission
 */
class UserRecoveryModel extends UserModel
{
    /** 
     * Gets whether the receovery process is enabled or not
     * 
     * @access public
     * @static
     *
     * @return bool         True if the the recovery process is enabled, otherwise false.
     */
    public static function isRecoveryEnabled()
    {
        return self::config('AUTH_PASSWORD_RESET_ENABLED') === true; 
    }

    /**
     * Create and output the recovery captcha 
     *
     * @access public
     * @static
     *
     * @return mixed 
     */
    public static function outputCaptcha()
    {
        \Kristuff\Miniweb\Security\CaptchaModel::captcha()->create('recovery_captcha');
        \Kristuff\Miniweb\Security\CaptchaModel::captcha()->output();
    }
    
    /**
     * Handles the password recorey request 
     *
     * @access public
     * @static
     *
     * @return 
     */
    public static function verifyPasswordRecoveryRequest($userNameOrEmail, $captcha)
    {
        // the return response (default is valid)
        $response = TaskResponse::create(200);
  
        // check captcha
        if ($response->assertTrue(\Kristuff\Miniweb\Security\CaptchaModel::captcha()->validate($captcha, 'recovery_captcha'), 400, self::text('ERROR_INVALID_CAPTCHA'))){
        
            // user name or email empty?
            if ($response->assertFalse(empty($userNameOrEmail), 400, self::text('LOGIN_RECOVERY_ERROR_NAME_EMAIL_EMPTY'))){

                // get the user
                $user = self::getUserByUserNameOrEmail($userNameOrEmail);
                
                // check if that username exists
                // we need to check is the user exists, but we do not tell end user if so 
                // it could be an attacker who is testing for existing email/name 
                if ($user !== false) {

                    // generate integer-timestamp (to see when exactly the user (or an attacker) requested the password reset mail)
                    // generate random hash for email password reset verification (40 char string)
                    // expire in one hour (3600 secs)
                    $tempTimestamp = time() + 3600 ;
                    $userPasswordResetHash = sha1(uniqid(mt_rand(), true)); //TODO

                    // set token (= a random hash string and a timestamp) into database ...
                    if ($response->assertTrue(
                        self::savePasswordResetToken($user->userId, $userPasswordResetHash, $tempTimestamp), 400, 
                        self::text('LOGIN_RECOVERY_ERROR_WRITE_TOKEN_FAIL'))){
                    
                        // ... and send a mail to the user, containing a link with username and token hash string
                        $emailSent = self::sendPasswordResetMail($user->userName, $userPasswordResetHash, $user->userEmail);
        
                        // register error if mail has not been  sent
                        $response->assertTrue($emailSent, 500, self::text('LOGIN_RECOVERY_MAIL_SENDING_ERROR'));
                    }
                }
            }
        }

        // If no errors, set a basic message. Request handled, that's all
        // Do not tell to a possible attacker if user or email exits or not in database 
        if (count($response->errors()) === 0){
            $response->setMessage(self::text('LOGIN_RECOVERY_SUCCESSFUL_HANDLING'));
        }
        
        // return response
        return $response;
    }
       
    /**
     * Set the new password
     * Please note: At this point the user has already pre-verified via verifyPasswordReset() (within one hour),
     * so we don't need to check again for the 60min-limit here. In this method we authenticate
     * via username & password-reset-hash from (hidden) form fields.
     *
     * @access public
     * @static
     * @param  string       $userName
     * @param  string       $passwordResetHash
     * @param  string       $newPassword
     * @param  string       $repeatNewPassword
     *
     * @return 
     */
    public static function handlePasswordReset($userName, $passwordResetHash, $newPassword, $repeatNewPassword)
    {
        // the return response
        $response = TaskResponse::create();
  
        // validate the password
        if (self::validatePasswordReset($response, $userName, $passwordResetHash, $newPassword, $repeatNewPassword)){

            // crypt the password (with the PHP 5.5+'s password_hash() function, result is a 60 character hash string)
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

            // write the passwordhash to database, reset userPasswordResetHash
            $saved = self::saveNewPasswordByNameAndResetToken($userName, $passwordHash, $passwordResetHash);
        
            // set response message
            if ($response->assertTrue($saved, 500, self::text('USER_PASSWORD_CHANGE_FAILED'))){
                $response->setMessage(self::text('USER_PASSWORD_CHANGE_SUCCESSFUL'));
            }
        }

        // return response
        return $response;
    }

    /**
     * Verifies the password reset request via the verification hash token (that's only valid for one hour)
     *
     * @static
     * @param  string   $userName           Username
     * @param  string   $verificationCode   Hash token
     *
     * @return 
     */
    public static function verifyPasswordResetLink($userName, $verificationCode)
    {
        // the return response
        $response = TaskResponse::create();

        // validate input
        $validateInput = !empty($userName) && !empty($verificationCode);
        if ($response->assertTrue($validateInput, 400, self::text('LOGIN_RECOVERY_NAME_HASH_NOT_FOUND'))){

            //get users by name and reset pass token
            $users = self::database()->select()
                                 ->from('user')
                                 ->column('userPasswordResetHash')
                                 ->column('userPasswordResetTimestamp')
                                 ->whereEqual('userName', $userName)
                                 ->whereEqual('userPasswordResetHash', $verificationCode)
                                 ->whereEqual('userProvider','DEFAULT')
                                 ->getAll('obj'); 

            // if this user with exactly this verification hash code does NOT exist
            if ($response->assertFalse(count($users) == 0, 400, self::text('LOGIN_RECOVERY_NAME_HASH_NOT_FOUND'))){
            
                // Check for timeout (password reset link is valid for one hour)
                $currentDate = new \DateTime('NOW');
                $expireDate = new \DateTime();
                $expireDate->setTimestamp((int) $users[0]->userPasswordResetTimestamp);
                $expired = $currentDate > $expireDate;

                // set response message
                if ($response->assertFalse($expired, 400, self::text('LOGIN_RECOVERY_MAIL_LINK_EXPIRED'))){
                    $response->setMessage(self::text('LOGIN_RECOVERY_MAIL_LINK_VALIDATED'));
                }
            }
        }

        // return response
        return $response;
    }

    /**
     * Set password reset token in database (for DEFAULT user accounts)
     *
     * @access protected
     * @static
     * @param int       $userId                 The user id
     * @param string    $passwordResetHash  The password reset hash
     * @param int       $tempTimestamp          Temporary timestamp
     *
     * @return bool success status
     */
    protected static function savePasswordResetToken($userId, $passwordResetHash, $tempTimestamp)
    {
       $query = self::database()->update('user')
                                ->setValue('userPasswordResetHash', $passwordResetHash)
                                ->setValue('userPasswordResetTimestamp', $tempTimestamp)
                                ->whereEqual('userId', $userId);

        // check if exactly one row was successfully changed
        return $query->execute() && $query->rowCount() == 1;
    }

    /**
     * Send the password reset mail
     *
     * @access protected
     * @static
     * @param string $userName              username
     * @param string $passwordResetHash     password reset hash
     * @param string $userEmail             user email
     *
     * @return bool success status
     */
    protected static function sendPasswordResetMail($userName, $passwordResetHash, $userEmail)
    {
        // create email body
        $body = self::config('AUTH_PASSWORD_RESET_MAIL_CONTENT') . ' ' . Application::getUrl() .
                self::config('AUTH_PASSWORD_RESET_VERIFY_URL') . '/' . urlencode($userName) . '/' . urlencode($passwordResetHash);

        // create instance of Mail class, try sending and check
        $mail = new Mailer();
        $sent = $mail->sendMail($userEmail, 
            self::config('AUTH_PASSWORD_RESET_MAIL_FROM_EMAIL'), 
            self::config('AUTH_PASSWORD_RESET_MAIL_FROM_NAME'), 
            self::config('AUTH_PASSWORD_RESET_MAIL_SUBJECT'), 
            $body, false);

        // return response
        return $sent;
    }

    /**
     * Validate the password change or submission
     *
     * @access protected
     * @static
     * @param  string   $userName
     * @param  string   $userPasswordResetHash
     * @param  string   $newPassword
     * @param  string   $repeatNewPassword
     *
     * @return bool|array
     */
    protected static function validatePasswordReset(TaskResponse $response, $userName, $userPasswordResetHash, $newPassword, $repeatNewPassword)
    {
        // empty user name or hash?
        $response->assertFalse(empty($userName), 400, self::text('USER_NAME_ERROR_EMPTY'));
        $response->assertFalse(empty($userPasswordResetHash), 400, self::text('USER_PASSWORD_CHANGE_INVALID_TOKEN'));

        if ($response->success()){
            return self::validateUserPassword($response, $newPassword, $repeatNewPassword);
        }

        return false;
    }
    
    /**
     * Writes the new password to the database
     *
     * @access protected
     * @static
     * @param string    $userName           username
     * @param string    $passwordHash
     * @param string    $passwordResetHash
     *
     * @return bool     True if the passwordwas succesfully saved, otherwise false.
     */
    protected static function saveNewPasswordByNameAndResetToken($userName, $passwordHash, $passwordResetHash)
    {
       $query = self::database()->update('user')
                                ->setValue('userPasswordHash', $passwordHash)
                                ->setValue('userPasswordResetHash', null)
                                ->setValue('userPasswordResetTimestamp', null)
                                ->whereEqual('userName', $userName)
                                ->whereEqual('userPasswordResetHash', $passwordResetHash);

        // check if exactly one row was successfully changed
        return $query->execute() && $query->rowCount() == 1;
    }     
}