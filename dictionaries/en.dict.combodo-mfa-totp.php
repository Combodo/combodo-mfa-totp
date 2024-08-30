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

	'MFATOTP:UI:Redirection:Title' => 'Redirecting to home page...',

	'MFATOTP:App:UI:Validation:Title' => 'Authenticator app',
	'MFATOTP:App:UI:Code' => 'Code',
	'MFATOTP:App:UI:Config:Title' => 'Configuration for authenticator app',
	'MFATOTP:App:UI:Configure' => 'Configuration',
	'MFATOTP:App:UI:ScanQRCode' => 'Scan the following QR Code',
	'MFATOTP:App:UI:EnterValues' => 'Or manually enter the following values',
	'MFATOTP:App:UI:Secret' => 'Secret',
	'MFATOTP:App:UI:Issuer' => 'Issuer',
	'MFATOTP:App:UI:Label' => 'Username',

	'MFATOTP:App:UI:Validated' => 'Authenticator app Validated',
	'MFATOTP:App:UI:NotValidated' => 'Authenticator app NOT validated',

	'MFATOTP:App:UI:CodeValidationTitle' => 'Enter validation code for: %1$s - %2$s',
	'MFATOTP:App:UI:CodeValidation:explain' => 'Validate the configuration by typing the code received from the authenticator app',

	'MFATOTP:Mail:UI:EnterCode' => 'Enter code received by email',

	'Class:MFAUserSettingsTOTPApp' => 'MFA TOTP by application',
	'Class:MFAUserSettingsTOTPMail' => 'MFA TOTP by mail',
));
