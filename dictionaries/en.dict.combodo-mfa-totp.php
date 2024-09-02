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

	'MFATOTP:Redirection:Title' => 'Redirecting to home page...',
	'MFATOTP:Validated' => 'Authenticator app Validated',
	'MFATOTP:NotValidated' => 'Authenticator app NOT validated',

	'MFATOTP:EnterCode' => 'Code',
	'MFATOTP:Issuer' => 'Issuer',

	'MFATOTP:App:Validation:Title' => 'Authenticator app',
	'MFATOTP:App:Config:Title' => 'Configuration for authenticator app',
	'MFATOTP:App:Configure' => 'Configuration',
	'MFATOTP:App:ScanQRCode' => 'Scan the following QR Code',
	'MFATOTP:App:EnterValues' => 'Or manually enter the following values',
	'MFATOTP:App:Secret' => 'Secret',
	'MFATOTP:App:Label' => 'Username',
	'MFATOTP:App:CodeValidation:Title' => 'Enter validation code for: %1$s - %2$s',
	'MFATOTP:App:CodeValidation:explain' => 'Validate the configuration by typing the code received from the authenticator app',

	'MFATOTP:Mail:EnterCode' => 'Enter code received by email',
	'MFATOTP:Mail:Config:Title' => 'Configure MFA by email',
	'MFATOTP:Mail:Label' => 'Email',
	'MFATOTP:Mail:CodeValidation:Title' => 'Enter code received by email at %1$s',
	'MFATOTP:Mail:ResendEmail:Link' => 'To receive another code at %1$s: click here',
	'MFATOTP:Mail:Validation:Title' => 'MFA by email',


	'Class:MFAUserSettingsTOTPApp' => 'MFA TOTP by application',
	'Class:MFAUserSettingsTOTPMail' => 'MFA TOTP by mail',
));
