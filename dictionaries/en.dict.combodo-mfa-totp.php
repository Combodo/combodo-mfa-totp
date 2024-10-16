<?php
/**
 * Localized data
 *
 * @copyright   Copyright (C) 2013 XXXXX
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

Dict::Add('EN US', 'English', 'English', array(
	'MFA:login:switch:label:MFAUserSettingsTOTPApp' => 'Use authenticator app',
	'MFA:login:switch:label:MFAUserSettingsTOTPMail' => 'Send verification code by email',
	'combodo-mfa-totp/Operation:MFATOTPMailConfig/Title' => 'Configure MFA by email',
	'combodo-mfa-totp/Operation:MFATOTPAppConfig/Title' => 'Configure authenticator app',
	'MFA:MFAUserSettingsTOTPApp:Description' => 'Time based One-time password using an external application',
	'MFA:MFAUserSettingsTOTPMail:Description' => 'Send a code by email valid for 10 minutes',

	'MFATOTP:Redirection:Title' => 'Redirecting to home page...',

	'MFATOTP:EnterCode' => 'Code',
	'MFATOTP:Issuer' => 'Issuer',

	'MFATOTP:App:Validated' => 'Authenticator app Validated',
	'MFATOTP:App:NotValidated' => 'Authenticator app NOT validated',
	'MFATOTP:App:Validation:Title' => 'Multi-Factor Authentication',
	'MFATOTP:App:Config:Title' => 'Configure your TOTP by application',
	'MFATOTP:App:Config:Description' => 'Scan the QR code and validate the configuration by entering the code provided by your external application',
	'MFATOTP:App:Configure' => 'QR code',
	'MFATOTP:App:ScanQRCode' => 'Scan the following QR Code',
	'MFATOTP:App:EnterValues' => 'Unable to scan? Enter the following secret',
	'MFATOTP:App:Secret' => 'Secret',
	'MFATOTP:App:Secret:Copy' => 'Copy secret to clipboard',
	'MFATOTP:App:Secret:Copy:Done' => 'Secret copied to clipboard',
	'MFATOTP:App:Issuer:Copy' => 'Copy issuer to clipboard',
	'MFATOTP:App:Issuer:Copy:Done' => 'Issuer copied to clipboard',
	'MFATOTP:App:Username:Copy' => 'Copy username to clipboard',
	'MFATOTP:App:Username:Copy:Done' => 'Username copied to clipboard',
	'MFATOTP:App:Label' => 'Username',
	'MFATOTP:App:Configuration:Error' => 'MFA Configuration failed',
	'MFATOTP:App:Validation:Error' => 'MFA Validation failed',
	'MFATOTP:App:CodeValidation:Title' => 'Validation code',
	'MFATOTP:App:CodeValidation:explain' => 'Validate the code from the app',

	'MFATOTP:Mail:Validated' => 'Email Validated',
	'MFATOTP:Mail:NotValidated' => 'Email NOT validated',
	'MFATOTP:Mail:EnterCode' => 'Enter code received by email',
	'MFATOTP:Mail:Config:Title' => 'Configure MFA by email',
	'MFATOTP:Mail:Label' => 'Email',
	'MFATOTP:Mail:CodeValidation:Title' => 'Enter code received by email at %1$s',
	'MFATOTP:Mail:ResendEmail:Link' => 'Resend code by email at %1$s',
	'MFATOTP:Mail:ResendEmail:Button' => 'Send code by email at %1$s',
	'MFATOTP:Mail:ResendEmail:Button+' => 'In order to validate the configuration click here to receive a code by email',
	'MFATOTP:Mail:ResendEmail:Done' => 'Email sent',
	'MFATOTP:Mail:Validation:Title' => 'Multi-Factor Authentication',
	'MFATOTP:Mail:Settings:Title' => 'Email settings',
	'MFATOTP:Mail:Settings:email:label' => 'User email',
	'MFATOTP:Mail:Settings:Saved:Done' => 'MFA email %1$s has been saved successfully. A code has been sent by email, please re-enter the code received to validate this modification',
	'MFATOTP:Mail:Validation' => 'Validation code',
	'MFATOTP:Mail:CodeValidation:explain' => 'Validate the code received by email',
	'MFATOTP:Mail:ChangeEmail:Button' => 'Submit',

	'MFATOTP:Mail:EmailSubject' => '%1$s - Code to connect. Expires at %3$s',
	'MFATOTP:Mail:EmailBody' => '<body><p>The code to connect to %1$s as %2$s is</p><strong>%4$s</strong><p></p><p>This code is valid until %3$s</p><p>If you are not trying to connect to %1$s, please contact your administrator.</p>',

	'MFATOTP:Error:SendMailFailed' => 'Error: Sending MFA email for code has failed.',
	'MFATOTP:Error:ConfigurationFailed' => 'Error: Configuring MFA application has failed.',
	'MFATOTP:Error:SaveSettingsFailed' => 'Error: Saving MFA email has failed. (probably a wrong email format)',

	'Class:MFAUserSettingsTOTP' => 'TOTP based Multi-Factor Authentication',
	'Class:MFAUserSettingsTOTP/Attribute:secret' => 'Secret',
	'Class:MFAUserSettingsTOTP/Attribute:code_validity' => 'Code validity',
	'Class:MFAUserSettingsTOTP/Attribute:epoch' => 'Base date for code computation (in s from 01-01-1970)',

	'Class:MFAUserSettingsTOTPApp' => 'TOTP by application',

	'Class:MFAUserSettingsTOTPMail' => 'email',
	'Class:MFAUserSettingsTOTPMail/Attribute:email' => 'Email',
));
