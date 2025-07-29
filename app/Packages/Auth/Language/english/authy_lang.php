<?php

/* E-mail Messages */

// Account verification
$lang['authy_email_verification_subject'] = 'Account Verification';
$lang['authy_email_verification_code'] = 'Your verification code is: ';
$lang['authy_email_verification_message'] = 'Click on the button below to verify your account';
$lang['authy_email_verification_text'] = " You can also click on (or copy and paste) the following link\n\n";
$lang['authy_email_verification_success_subject'] = 'Account Verification Success';
$lang['authy_email_verification_success_message'] = 'Account has been verified successfully. Welcome ';

// Password reset
$lang['authy_email_reset_subject'] = 'Reset Password';
$lang['authy_email_reset_text'] = "To reset your password click on (or copy and paste in your browser address bar) the link below:\n\n";

// New Password Request OR
// Password reset success
$lang['authy_email_reset_success_subject'] = 'Successful Pasword Reset';
$lang['authy_email_reset_success_new_password'] = 'Your password has reset successfully';
$lang['authy_email_reset_success_message'] = 'Your new password is: ';

/* Error Messages */

// Account creation errors
$lang['authy_error_user_id_exists'] = 'User Id already exists on the system. Contact System Admin for support.';
$lang['authy_error_email_exists'] = 'Email address already exists on the system. If you forgot your password, you can click the link below.';
$lang['authy_error_username_exists'] = "Account already exists on the system with that username.  Please enter a different username, or if you forgot your password, please click the link below.";
$lang['authy_error_email_invalid'] = 'Invalid e-mail address';
$lang['authy_error_password_invalid'] = 'Invalid password';
$lang['authy_error_username_invalid'] = 'Invalid Username';
$lang['authy_error_username_required'] = 'Username required';
$lang['authy_error_totp_code_required'] = 'Authentication Code required';
$lang['authy_error_totp_code_invalid'] = 'Invalid Authentication Code';

// Account update errors
$lang['authy_error_update_email_exists'] = 'Email address already exists on the system.  Please enter a different email address.';
$lang['authy_error_update_username_exists'] = "Username already exists on the system.  Please enter a different username.";

// Access errors
$lang['authy_error_no_access'] = 'Sorry, you do not have access to the resource you requested.';
$lang['authy_error_login_failed_email'] = 'E-mail Address and Password do not match.';
$lang['authy_error_login_failed_name'] = 'Username and Password do not match.';
$lang['authy_error_login_failed_all'] = 'E-mail, Username or Password do not match.';
$lang['authy_error_login_attempts_exceeded'] = 'You have exceeded your login attempts, your account has now been locked.';
$lang['authy_error_recaptcha_not_correct'] = 'Sorry, the reCAPTCHA text entered was incorrect.';

// Misc. errors
$lang['authy_error_no_user'] = 'User does not exist';
$lang['authy_error_account_not_verified'] = 'Your account has not been verified. Please check your e-mail and verify your account.';
$lang['authy_error_no_group'] = 'Group does not exist';
$lang['authy_error_no_subgroup'] = 'Subgroup does not exist';
$lang['authy_error_self_pm'] = 'It is not possible to send a Message to yourself.';
$lang['authy_error_no_pm'] = 'Private Message not found';

/* Info messages */
$lang['authy_info_already_member'] = 'User is already member of group';
$lang['authy_info_already_subgroup'] = 'Subgroup is already member of group';
$lang['authy_info_group_exists'] = 'Group name already exists';
$lang['authy_info_perm_exists'] = 'Permission name already exists';
