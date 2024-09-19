<?php
/**
 * Localized data
 *
 * @copyright   Copyright (C) 2013 XXXXX
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

Dict::Add('EN US', 'English', 'English', array(
	'combodo-mfa-totp/Operation:MFATOTPMailConfig/Title' => 'Configure MFA by email',
	'combodo-mfa-totp/Operation:MFATOTPAppConfig/Title' => 'Configure authenticator app',

	'MFA:login:switch:label:MFAUserSettingsTOTPApp' => 'Use authenticator app',
	'MFA:login:switch:label:MFAUserSettingsTOTPMail' => 'Send verification code by email',

	'MFATOTP:Redirection:Title' => 'Redirecting to home page...',
	'MFATOTP:Validated' => 'Authenticator app Validated',
	'MFATOTP:NotValidated' => 'Authenticator app NOT validated',

	'MFATOTP:EnterCode' => 'Code',
	'MFATOTP:Issuer' => 'Issuer',

	'MFATOTP:App:Validation:Title' => 'Multi-Factor Authentication',
	'MFATOTP:App:Config:Title' => 'Configuration for authenticator app',
	'MFATOTP:App:Configure' => 'Configuration',
	'MFATOTP:App:ScanQRCode' => 'Scan the following QR Code',
	'MFATOTP:App:EnterValues' => 'Or manually enter the following values',
	'MFATOTP:App:Secret' => 'Secret',
	'MFATOTP:App:Secret:Copy' => 'Copy secret to clipboard',
	'MFATOTP:App:Secret:Copy:Done' => 'Secret copied to clipboard',
	'MFATOTP:App:Label' => 'Username',
	'MFATOTP:App:CodeValidation:Title' => 'Enter validation code from your authenticator app for: %1$s - %2$s',
	'MFATOTP:App:CodeValidation:explain' => 'Validate the configuration by typing the code received from the authenticator app',

	'MFATOTP:Mail:EnterCode' => 'Enter code received by email',
	'MFATOTP:Mail:Config:Title' => 'Configure MFA by email',
	'MFATOTP:Mail:Label' => 'Email',
	'MFATOTP:Mail:CodeValidation:Title' => 'Enter code received by email at %1$s',
	'MFATOTP:Mail:ResendEmail:Link' => 'Resend code by email at %1$s',
	'MFATOTP:Mail:ResendEmail:Button' => 'Send validation code',
	'MFATOTP:Mail:ResendEmail:Button+' => 'In order to validate the configuration click here to receive a code by email',
	'MFATOTP:Mail:ResendEmail:Done' => 'Email sent',
	'MFATOTP:Mail:Validation:Title' => 'Multi-Factor Authentication',
	'MFATOTP:Mail:Settings:Title' => 'Email settings',
	'MFATOTP:Mail:Settings:email:label' => 'User email',
	'MFATOTP:Mail:Settings:Saved:Done' => 'MFA email has been saved successfully with email %1$s',
	'MFATOTP:Mail:Validation' => 'Validation',
	'MFATOTP:Mail:CodeValidation:explain' => 'Enter code received by email',

	'MFATOTP:Mail:EmailSubject' => '%1$s - Code to connect. Expires at %3$s',
	'MFATOTP:Mail:EmailBody' => '<body><p>The code to connect to %1$s as %2$s is</p><strong>%4$s</strong><p></p><p>This code is valid until %3$s</p><p>If you are not trying to connect to %1$s, please contact your administrator.</p>',

	'MFATOTP:Error:SendMailFailed' => 'Error: Sending MFA email for code has failed.',
	'MFATOTP:Error:SaveSettingsFailed' => 'Error: Saving MFA email has failed. (probably a wrong email format)',

	'Class:MFAUserSettingsTOTPApp' => 'MFA TOTP by application',
	'Class:MFAUserSettingsTOTPMail' => 'MFA TOTP by mail',
));
