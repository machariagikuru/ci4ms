<?php

return [
    // Exceptions
    'invalidModel' => '{0} must be loaded before the model is used.',
    'userNotFound' => 'Under the specified criterion, ID = {0, number}, no user could be found.',
    'noUserEntity' => 'no user entity provided for validation.',
    'tooManyCredentials' => 'too many credentials provided.',
    'invalidFields' => '"{0}" is not a valid field for users.',
    'unsetPasswordLength' => 'passwordLength is not set.',
    'unknownError' => 'unknown error occurred.',
    'notLoggedIn' => 'not logged in.',
    'notEnoughPrivilege' => 'you do not have enough privilege to access this resource.',

    // Registration
    'registerDisabled' => 'The registration of new users has been disabled.',
    'registerSuccess' => 'Registration successful! Please check your email to activate your account.',
    'emailActivationuccess' => 'Email activation successful! You can now log in.',
    'registerCLI' => 'A new user has been created.: {0}, #{1}',

    // Activation
    'activationNoUser' => 'The activation code provided is not valid.',
    'activationSubject' => 'Thank you for registering! Activate your account now.',
    'activationSuccess' => 'Thank you for activating your account! You can now log in.',
    'activationResend' => 'Resend activation message one more time.', // translate
    'notActivated' => 'This user account has not been activated yet.',
    'errorSendingActivation' => 'Activation message could not be sent.: {0}',

    // Login
    'badAttempt' => 'Unable to log you in. Please check your credentials.',
    'loginBlock' => '<b>Your login has been suspended.</b>. <br> please contact the administrator.',
    'loginSuccess' => 'The login was successful.',
    'invalidPassword' => 'Unable to log in. Please check your login details.',

    // Forgotten Passwords
    'forgotDisabled' => 'Resseting password option has been disabled.', // translate
    'forgotNoUser' => 'No user could be found with that email address.',
    'forgotSubject' => 'forgot password instructions',
    'resetSuccess' => 'Your password has been successfully reset. You can now log in with your new password.',
    'forgotEmailSent' => 'An email with password reset instructions has been sent to: {0}',
    'errorEmailSent' => 'Unable to send email with password reset instructions to: {0}', // translate

    // Passwords
    'errorPasswordLength' => 'Password must be at least {0, number} characters long.',
    'suggestPasswordLength' => 'For better security, use a password with at least {0, number} characters.',
    'errorPasswordCommon' => '',
    'suggestPasswordCommon' => 'The password you have chosen is too common. Please choose a more complex password.',
    'errorPasswordPersonal' => 'Your password cannot contain your personal information',
    'suggestPasswordPersonal' => 'Do not use your name, username, or email in your password.',
    'errorPasswordTooSimilar' => 'Your password is too similar to your username.',
    'suggestPasswordTooSimilar' => 'Choose a password that is not similar to your username.',
    'errorPasswordPwned' => 'The password "{0}" has appeared in a data breach and should not be used. Please choose a different password.',
    'suggestPasswordPwned' => '{0} should never be used as a password. If you are using it anywhere, change it immediately.',
    'errorPasswordEmpty' => 'Password cannot be empty.',
    'passwordChangeSuccess' => 'Your password has been successfully changed.',
    'userDoesNotExist' => 'No user found with the specified criteria.',
    'resetTokenExpired' => 'Password reset token has expired.',

    // Groups
    'groupNotFound' => 'Group not found: {0}.',

    // Permissions
    'permissionNotFound' => 'Permission not found: {0}',

    // Banned
    'userIsBanned' => 'This user account has been banned.',

    // Too many requests
    'tooManyRequests' => 'Too many requests. Please wait {0, number} seconds.',

    // Login views
    'home' => 'Home',
    'current' => 'Current',
    'forgotPassword' => 'Forgot Password?',
    'enterEmailForInstructions' => 'Enter registered email for email reset instructions.',
    'email' => 'Email',
    'emailAddress' => 'Email Adress',
    'sendInstructions' => 'Send Instructions',
    'loginAction' => 'Login',
    'rememberMe' => 'Remember Me',
    'needAnAccount' => 'Create a new account?',
    'forgotYourPassword' => 'Forgot your password?',
    'password' => 'password',
    'repeatPassword' => 'repeat password',
    'emailOrUsername' => 'Email or Username',
    'username' => 'Username',
    'register' => 'Register',
    'signIn' => 'sign in',
    'alreadyRegistered' => 'Already registered?',
    'weNeverShare' => 'We will never share your email address with anyone.',
    'resetYourPassword' => 'Reset Your Password',
    'enterCodeEmailPassword' => 'Enter the code sent to your email address',
    'token' => 'Token',
    'newPassword' => 'New Password',
    'newPasswordRepeat' => 'New Password (repeat)',
    'resetPassword' => 'Reset Password',
    'badCaptcha' => 'The CAPTCHA verification failed. Please try again.',
    'generatePasswordMessage' => 'generate a strong password for me',
    'forgotPasswordMessage' => 'forgot your password?',
    'loginMessage' => 'login to your account',
    'membershipPasswordReset' => 'Membership Password Reset',
    'passwordResetMessage' => 'Your membership password has been reset. Your password reset request is valid until {reset_expires}. Please click <a href="{reset_link}"><b>here</b></a> to set your new password.',
];
