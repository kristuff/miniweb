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

/**
 * Texts used in the application.
 */
return array(

    /* commons errors */
    'ERROR_INVALID_REQUEST'                     => "Invalid request",
    'ERROR_PARAM_NULL_OR_EMPTY'                 => "The parametter %s was empty.",
    'ERROR_INVALID_AUTHENTFICATION'             => "Invalid authentification",
    'ERROR_INVALID_PERMISSIONS'                 => "Invalid permissions",
    'ERROR_INVALID_TOKEN'                       => "Invalid token",
    'ERROR_INVALID_CAPTCHA'                     => "The entered captcha security characters were wrong.",
    'ERROR_UNKNOWN'                             => "Unknown error occurred!",
  
    /* login error */
    "LOGIN_ERROR_ACCOUNT_DELETED"               => "Your account has been deleted.",
	"LOGIN_ERROR_ACCOUNT_SUSPENDED"             => "Account suspended for %s hours left",
    "LOGIN_ERROR_ACCOUNT_NOT_ACTIVATED"         => "Your account is not activated yet. Please click on the confirm link in the mail.",
    "LOGIN_ERROR_FAILED_3_TIMES"                => "Login failed 3 or more times already. Please wait 30 seconds to try again.",
	"LOGIN_ERROR_NAME_OR_PASSWORD_EMPTY"        => "Username or password field was empty.",
	"LOGIN_ERROR_NAME_OR_PASSWORD_WRONG"        => "The username or password is incorrect. Please try again.",
    "LOGIN_ERROR_PASSWORD_WRONG_3_TIMES"        => "You have typed in a wrong password 3 or more times already. Please wait 30 seconds to try again.",
   
    /* login cookie */
    "LOGIN_COOKIE_ERROR_INVALID"                => "Your remember-me-cookie is invalid.",
	"LOGIN_COOKIE_SUCCESSFUL"                   => "You were successfully logged in via the remember-me-cookie.",
   
    /* login recevery */
    'LOGIN_RECOVERY_TITLE'                      => "Request a password reset",
    'LOGIN_RECOVERY_TEXT'                       => "Enter your username or email and you'll get a mail with instructions:", 
    'LOGIN_RECOVERY_BUTTON'                     => "Send me a password-reset mail", 
	'LOGIN_RECOVERY_ERROR_NAME_EMAIL_EMPTY'     => "Username or email field was empty.",
    'LOGIN_RECOVERY_ERROR_WRITE_TOKEN_FAIL'     => "Could not write token to database.",
    'LOGIN_RECOVERY_MAIL_SENDING_ERROR'         => "Password reset mail could not be sent due to: ",
    'LOGIN_RECOVERY_SUCCESSFUL_HANDLING'        => "Your request has been saved. If we find a such email or name in our database, a password reset mail will be send. Please check your inbox. ",
    'LOGIN_RECOVERY_NAME_HASH_NOT_FOUND'        => "Username/Verification code combination does not exist.",
    'LOGIN_RECOVERY_MAIL_LINK_VALIDATED'        => "Password reset validation link is valid. Please change the password now.",
	'LOGIN_RECOVERY_MAIL_LINK_EXPIRED'          => "Your reset link has expired. Please use the reset link within one hour.",
  
    /* user errors */
    'USER_ID_ERROR_EMPTY'                       => "User id field was empty.",
    'USER_ID_ERROR_BAD_FORMAT'                  => "User id field was incorrect.",
   
    /* user name */
    'USER_NAME_FIELD'                           => 'User name:',
    'USER_NAME_EDIT_TITLE'                      => 'Change my user name',
    'USER_NAME_ERROR_EMPTY'                     => "Username field was empty.",
    'USER_NAME_ERROR_BAD_PATTERN'               => "Username does not fit the name pattern: only a-Z and numbers are allowed, 2 to 64 characters.",
    'USER_NAME_ERROR_ALREADY_TAKEN'             => "Sorry, that username is already taken. Please choose another one.",
    'USER_NAME_ERROR_NEW_SAME_AS_OLD_ONE'       => "Sorry, that username is the same as your current one. Please choose another one.",
    'USER_NAME_CHANGE_SUCCESSFUL'               => 'Your user name has been changed successfully.',

    /* user email */
    'USER_EMAIL_FIELD'                          => "User email:",
    'USER_EMAIL_EDIT_TITLE'                     => "Change my email address",
    'USER_EMAIL_PLACEHOLDER'                    => "Enter you email address:",
    'USER_EMAIL_ERROR_ALREADY_TAKEN'            => "Sorry, that email is already in use. Please choose another one.",
    'USER_EMAIL_ERROR_EMPTY'                    => "Email field was empty.",
    'USER_EMAIL_ERROR_REPEAT_WRONG'             => "Email and email repeat are not the same",
    'USER_EMAIL_ERROR_BAD_PATTERN'              => "Sorry, your chosen email does not fit into the email naming pattern.",
    'USER_EMAIL_ERROR_NEW_SAME_AS_OLD_ONE'      => "Sorry, that email address is the same as your current one. Please choose another one.",
    'USER_EMAIL_CHANGE_SUCCESSFUL'              => 'Your email address has been changed successfully.',

    /* user password */
    'USER_PASSWORD_ERROR_EMPTY'                 => "Password field was empty.",
    'USER_PASSWORD_ERROR_REPEAT_WRONG'          => "Password and password repeat are not the same.",
	'USER_PASSWORD_ERROR_TOO_SHORT'             => "Password has a minimum length of 6 characters.",

    'USER_PASSWORD_CHANGE_SUCCESSFUL'           => "Password successfully changed.",
    'USER_PASSWORD_CHANGE_FAILED'               => "Sorry, your password changing failed.",
    'USER_PASSWORD_CHANGE_NEW_SAME_AS_CURRENT'  => "New password is the same as the current password.",
    'USER_PASSWORD_CHANGE_CURRENT_INCORRECT'    => "Current password entered was incorrect.",
    'USER_PASSWORD_CHANGE_INVALID_TOKEN'        => "No or invalid password reset token.",
    'USER_PASSWORD_CHANGE_ERROR_CURRENT_WRONG'  => "Current password entered was incorrect.",
    
    'USER_PASSWORD_EDIT_TITLE'                  => "Change my password",
    'USER_PASSWORD_EDIT_CURRENT'                => 'Enter current password:',
    'USER_PASSWORD_EDIT_NEW'                    => 'New password (min. 6 characters):',
    'USER_PASSWORD_EDIT_REPEAT'                 => 'repeat new password:',
  
     /* user new account */
    'USER_NEW_ACCOUNT_ERROR_CREATION_FAILED'    => "Sorry, your registration failed. Please go back and try again.",
    'USER_NEW_ACCOUNT_ERROR_DEFAULT_SETTINGS'    => "Internal error: unable to insert defaults settings data",
    'USER_NEW_ACCOUNT_SUCCESSFULLY_CREATED'     => "Your account has been created successfully and we have sent you an email. Please click the VERIFICATION LINK within that mail.",
    'USER_NEW_ACCOUNT_MAIL_SENDING_ERROR'       => "Verification mail could not be sent due to: ",
    'USER_NEW_ACCOUNT_MAIL_SENDING_SUCCESSFUL'  => "A verification mail has been sent successfully.",
    'USER_NEW_ACCOUNT_ACTIVATION_SUCCESSFUL'    => "Activation was successful! You can now log in.",
	'USER_NEW_ACCOUNT_ACTIVATION_FAILED'        => "Sorry, no such id/verification code combination here! It might be possible that your mail provider (Yahoo? Hotmail?) automatically visits links in emails for anti-scam scanning, so this activation link might been clicked without your action. Please try to log in on the main page.",
    
     /* user avatar */
    'USER_AVATAR_EDIT_TITLE'                    => "Change my avatar",
	'USER_AVATAR_UPLOAD_FAILED'                 => "Something went wrong with the image upload.",
    'USER_AVATAR_UPLOAD_SUCCESSFUL'             => "Avatar upload was successful.",
	'USER_AVATAR_UPLOAD_ERROR_WRONG_TYPE'       => "Only JPEG and PNG files are supported.",
    'USER_AVATAR_UPLOAD_ERROR_TOO_SMALL'        => "Avatar source file's width/height is too small. Needs to be 100x100 pixel minimum.",
	'USER_AVATAR_UPLOAD_ERROR_TOO_BIG'          => "Avatar source file is too big. 1 Megabyte is the maximum.",
	'USER_AVATAR_DELETE_ERROR_NO_FILE'          => "You don't have a custom avatar.",
	'USER_AVATAR_DELETE_FAILED'                 => "Something went wrong while deleting your avatar.",
	'USER_AVATAR_DELETE_SUCCESSFUL'             => "You successfully deleted your avatar.",
    'USER_AVATAR_ERROR_PATH_MISSING'            => "Avatar path does not exist",
    'USER_AVATAR_ERROR_PATH_PERMISSIONS'        => "Avatar path is not writable(invalid permissions)",

    /* user account admin */
    'USER_ACCOUNT_ERROR_DELETE_SUSPEND_OWN'             => 'You can not delete or suspend your own account.',
    'USER_ACCOUNT_SUSPENSION_DELETION_STATUS_CHANGED'   => "The user's suspension / deletion status has been edited.",
    'USER_ACCOUNT_SUCCESSFULLY_KICKED'                  => "The selected user has been successfully kicked out of the system.",
    'USER_ACCOUNT_SUCCESSFULLY_DELETED'                 => "The user's account has been successfully deleted.",
    'USER_ACCOUNT_ERROR_DELETETION_FAILED'              => "The user's account deletion failed!",
    'USER_ACCOUNT_SUCCESSFULLY_CREATED'                 => "The user's account has been successfully created.",
    'USER_ACCOUNT_SUSPENSION_ERROR_DAYS'                => "The suspension days was invalid.",
	
    /* user invitation */
    'USER_INVITATION_VALIDATION_SUCCESSFUL'     => 'Please define your user name and password to complete your registration',

    'USER_INVITATION_EMAIL_SUBJECT'             => 'Invitation received from %s',
    'USER_INVITATION_EMAIL_CONTENT_TITLE'       => 'Welcome !',
    'USER_INVITATION_EMAIL_CONTENT_PART_1'      => 'You receive this email from %s. An account has been created for you.',
    'USER_INVITATION_EMAIL_CONTENT_PART_2'      => 'You need to click on link bellow to complete your registration and activate your account. You will be ask to  
                                                    define you user name and a password. ',
    'USER_INVITATION_EMAIL_CONTENT_PART_3'      => 'Don\'t wait ! This link will expire on %s.', 
    'USER_INVITATION_EMAIL_SENT_SUCCESSFULLY'   => 'An invitation mail has been sent successfully. The user will need to complete its registration before to log in.', 
    "USER_INVITATION_PROCESS_COMPLETE"          => 'An account has been created successfully (temporary user name: %s) and we have sent an email at %s. 
                                                   <br>This account will not be active until the user completes its registration.' ,

    
   
);