<?php
/**
 * Localized data
 *
 * @copyright   Copyright (C) 2024 Combodo SAS
 * @license     http://opensource.org/licenses/AGPL-3.0
 * @author      Lars Kaltefleiter <lars.kaltefleiter@itomig.de>
 */

Dict::Add('DE DE', 'German', 'Deutsch', array(
	'MFA:login:switch:label:MFAUserSettingsTOTPApp' => 'Authenticator-App verwenden',
	'MFA:login:switch:label:MFAUserSettingsTOTPMail' => 'Bestätigungscode per E-Mail senden',
	'combodo-mfa-totp/Operation:MFATOTPMailConfig/Title' => 'Multi-Faktor-Authentifizierung per E-Mail konfigurieren',
	'combodo-mfa-totp/Operation:MFATOTPAppConfig/Title' => 'Authenticator-App konfigurieren',
	'MFA:MFAUserSettingsTOTPApp:Description' => 'Zeitbasierte Einmalpasswörter  mit einer Authenticator-App',
	'MFA:MFAUserSettingsTOTPMail:Description' => 'Sendet einen Code per E-Mail, der 10 Minuten gültig ist',

	'MFATOTP:Redirection:Title' => 'Weiterleitung zur Startseite...',

	'MFATOTP:EnterCode' => 'Code',
	'MFATOTP:Issuer' => 'Issuer',

	'MFATOTP:App:Validated' => 'Gültige App',
	'MFATOTP:App:NotValidated' => 'Falscher Code',
	'MFATOTP:App:Config:Title' => 'TOTP mit der App konfigurieren',
	'MFATOTP:App:Config:Description' => 'Scannen Sie den QR-Code und überprüfen Sie die Konfiguration, indem Sie den von Ihrer Authenticator-App bereitgestellten Code eingeben',
	'MFATOTP:App:Configure' => 'QR-Code',
	'MFATOTP:App:Config:CannotScan' => 'Kann nicht gescannt werden?',
	'MFATOTP:App:Config:SwitchToData' => 'Zu den Daten wechseln',
	'MFATOTP:App:ScanQRCode' => 'Scannen Sie den folgenden QR-Code',
	'MFATOTP:App:EnterValues' => 'Kann nicht gescannt werden? Geben Sie die folgenden Werte manuell ein',
	'MFATOTP:App:Secret' => 'Secret',
	'MFATOTP:App:Secret:Copy' => 'Secret in die Zwischenablage kopieren',
	'MFATOTP:App:Secret:Copy:Done' => 'Secret wurde in die Zwischenablage kopiert',
	'MFATOTP:App:Issuer:Copy' => 'Issuer in die Zwischenablage kopieren',
	'MFATOTP:App:Issuer:Copy:Done' => 'Issuer wurde in die Zwischenablage kopiert',
	'MFATOTP:App:Username:Copy' => 'Benutzername in die Zwischenablage kopieren',
	'MFATOTP:App:Username:Copy:Done' => 'Benutzername wurde in die Zwischenablage kopiert',
	'MFATOTP:App:Label' => 'Benutzername',
	'MFATOTP:App:Configuration:Error' => 'MFA-Konfiguration fehlgeschlagen',
	'MFATOTP:App:Validation:Error' => 'MFA-Überprüfung fehlgeschlagen',
	'MFATOTP:App:CodeValidation:Title' => 'Code von Ihrer Authenticator-App überprüfen',
	'MFATOTP:App:CodeValidation:explain' => 'Code von der App überprüfen',

	'MFATOTP:Mail:Validation:Title' => 'Multi-Faktor-Authentifizierung',
	'MFATOTP:Mail:Config:Title' => 'Multi-Faktor-Authentifizierung per E-Mail konfigurieren',
	'MFATOTP:Mail:Settings:Step1:SetEmail' => 'Schritt 1: MFA-E-Mail festlegen',
	'MFATOTP:Mail:Settings:Step1:ChangeEmailOptional' => 'Schritt 1: MFA-E-Mail ändern (optional)',
	'MFATOTP:Mail:Settings:email:label' => 'MFA-E-Mail',
	'MFATOTP:Mail:Settings:email:label+' => 'E-Mail-Adresse, die für den Versand des MFA-Codes verwendet wird, standardmäßig die E-Mail-Adresse des Benutzers',
	'MFATOTP:Mail:SetEmail:Button' => 'MFA-E-Mail festlegen',
	'MFATOTP:Mail:ChangeEmail:Button' => 'MFA-E-Mail ändern',
	'MFATOTP:Mail:Settings:Saved:Done' => 'MFA-E-Mail (%1$s) wurde erfolgreich gespeichert',
	'MFATOTP:Mail:Validated' => 'E-Mail überprüft',
	'MFATOTP:Mail:NotValidated' => 'Falscher Code',

	'MFATOTP:Mail:EnterCode' => 'Code eingeben, den Sie per E-Mail erhalten haben',
	'MFATOTP:Mail:CodeValidation:Title' => 'Code eingeben, den Sie per E-Mail an %1$s erhalten haben',
	'MFATOTP:Mail:ResendEmail:Link' => 'E-Mail erneut senden',
	'MFATOTP:Mail:ResendEmail:Button' => 'E-Mail senden',
	'MFATOTP:Mail:ResendEmail:Button+' => 'Um die Konfiguration zu überprüfen, klicken Sie hier, um einen Code per E-Mail zu erhalten',
	'MFATOTP:Mail:ResendEmail:Done' => 'E-Mail gesendet',
	'MFATOTP:Mail:Settings:Title' => 'E-Mail-Einstellungen',
	'MFATOTP:Mail:Validation' => 'Schritt 2: Multi-Faktor-Authentifizierung per E-Mail aktivieren',
	'MFATOTP:Mail:CodeValidation:explain' => 'Code per E-Mail überprüfen',

	'MFATOTP:Mail:EmailSubject' => '%1$s - Anmeldecode. Gültig bis %3$s',
    'MFATOTP:Mail:EmailBody' => '<body><p>Der Code, um sich bei %1$s als %2$s anzumelden, lautet</p><strong>%4$s</strong><p></p><p>Dieser Code ist gültig bis %3$s</p><p>Falls Sie nicht versuchen, sich bei %1$s anzumelden, wenden Sie sich bitte an Ihren Administrator.</p>',

    'MFATOTP:Error:SendMailFailed' => 'Fehler: Das Senden des MFA-E-Mails für den Code ist fehlgeschlagen. Bitte überprüfen Sie die SMTP-Konfiguration.',
    'MFATOTP:Error:ConfigurationFailed' => 'Fehler: Die Konfiguration der MFA-App ist fehlgeschlagen.',
    'MFATOTP:Error:SaveSettingsFailed' => 'Fehler: Das Speichern der MFA-Einstellungen ist fehlgeschlagen (wahrscheinlich eine ungültige E-Mail)',

	'Class:MFAUserSettingsTOTP' => 'TOTP basierte Multi-Faktor-Authentifizierung',
    'Class:MFAUserSettingsTOTP/Attribute:secret' => 'Secret',
    'Class:MFAUserSettingsTOTP/Attribute:code_validity' => 'Code-Gültigkeit',
    'Class:MFAUserSettingsTOTP/Attribute:epoch' => 'Startdatum für die Berechnung des Codes',
    'Class:MFAUserSettingsTOTP/Attribute:epoch+' => 'In Sekunden seit dem 1. Januar 1970',

	'Class:MFAUserSettingsTOTPApp' => 'TOTP via App',

	'Class:MFAUserSettingsTOTPMail' => 'TOTP via Email',
	'Class:MFAUserSettingsTOTPMail/Attribute:email' => 'Email',
));
