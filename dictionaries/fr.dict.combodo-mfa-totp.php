<?php
/**
 * Localized data
 *
 * @copyright   Copyright (C) 2013 XXXXX
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

Dict::Add('FR FR', 'French', 'Français', array(
	'MFA:login:switch:label:MFAUserSettingsTOTPApp' => 'Utiliser une app d\'authentification',
	'MFA:login:switch:label:MFAUserSettingsTOTPMail' => 'Envoyer un code par email',
	'combodo-mfa-totp/Operation:MFATOTPMailConfig/Title' => 'Configurer MFA par email',
	'combodo-mfa-totp/Operation:MFATOTPAppConfig/Title' => 'Configurer une app d\'authentification',
	'MFA:MFAUserSettingsTOTPApp:Description' => 'Mot de passe unique temporaire via une app externe',
	'MFA:MFAUserSettingsTOTPMail:Description' => 'Envoyer un code par email valable 10 minutes',

	'MFATOTP:Redirection:Title' => 'Retour à la page d\'accueil...',
	'MFATOTP:EnterCode' => 'Code',
	'MFATOTP:Issuer' => 'Émetteur',

	'MFATOTP:App:Validated' => 'App validée',
	'MFATOTP:App:NotValidated' => 'Code invalide',
	'MFATOTP:App:Config:Title' => 'App d\'authentification',
	'MFATOTP:App:Config:Description' => 'Scannez le QR code et validez la configuration en entrant le code fourni par votre app externe',
	'MFATOTP:App:Configure' => 'QR code',
	'MFATOTP:App:Config:CannotScan' => 'Impossible de scanner ?',
	'MFATOTP:App:Config:SwitchToData' => 'Basculer vers les données',
	'MFATOTP:App:ScanQRCode' => 'Scannez le QR code suivant',
	'MFATOTP:App:EnterValues' => 'Impossible de scanner? Entrez les valeurs suivantes manuellement',
	'MFATOTP:App:Secret' => 'Secret',
	'MFATOTP:App:Secret:Copy' => 'Copier le secret dans le presse-papier',
	'MFATOTP:App:Secret:Copy:Done' => 'Secret copié dans le presse-papier',
	'MFATOTP:App:Issuer:Copy' => 'Copier l\'émetteur dans le presse-papier',
	'MFATOTP:App:Issuer:Copy:Done' => 'Émetteur copié dans le presse-papier',
	'MFATOTP:App:Username:Copy' => 'Copier le nom d\'utilisateur dans le presse-papier',
	'MFATOTP:App:Username:Copy:Done' => 'Nom d\'utilisateur copié dans le presse-papier',
	'MFATOTP:App:Label' => 'Nom d\'utilisateur',
	'MFATOTP:App:Configuration:Error' => 'Échec de la configuration MFA',
	'MFATOTP:App:Validation:Error' => 'Échec de la validation MFA',
	'MFATOTP:App:CodeValidation:Title' => 'Valider le code de votre app d\'authentification',
	'MFATOTP:App:CodeValidation:explain' => 'Valider le code fourni par l\'app',

	'MFATOTP:Mail:Validation:Title' => 'Authentification multifacteur',
	'MFATOTP:Mail:Config:Title' => 'Configurer le MFA par email',
	'MFATOTP:Mail:Settings:Step1:SetEmail' => 'Étape 1: définir l\'email MFA',
	'MFATOTP:Mail:Settings:Step1:ChangeEmailOptional' => 'Étape 1: Changer l\'email MFA (optionnel)',
	'MFATOTP:Mail:Settings:email:label' => 'Email MFA',
	'MFATOTP:Mail:Settings:email:label+' => 'L\'email utilisé pour envoyer le code MFA, par défaut l\'email de l\'utilisateur',
	'MFATOTP:Mail:SetEmail:Button' => 'Définir l\'email MFA',
	'MFATOTP:Mail:ChangeEmail:Button' => 'Changer l\'email MFA',
	'MFATOTP:Mail:Settings:Saved:Done' => 'L\'email %1$s a été enregistré avec succès. Un code a été envoyé par email, veuillez le ressaisir pour valider cette modification',
	'MFATOTP:Mail:Validated' => 'Email validé',
	'MFATOTP:Mail:NotValidated' => 'Code invalide',

	'MFATOTP:Mail:EnterCode' => 'Entrez le code reçu par email',
	'MFATOTP:Mail:CodeValidation:Title' => 'Entrez le code reçu par email à %1$s',
	'MFATOTP:Mail:ResendEmail:Link' => 'Renvoyer le code par email',
	'MFATOTP:Mail:ResendEmail:Button' => 'Envoyer le code par email',
	'MFATOTP:Mail:ResendEmail:Button+' => 'Pour valider la configuration, cliquez ici pour recevoir un code par email',
	'MFATOTP:Mail:ResendEmail:Done' => 'Email envoyé',
	'MFATOTP:Mail:Validation' => 'Étape 2: Activer le MFA par email',
	'MFATOTP:Mail:CodeValidation:explain' => 'Valider le code reçu par email',

	'MFATOTP:Mail:EmailSubject' => '%1$s - Code de connexion. Expire le %3$s',
	'MFATOTP:Mail:EmailBody' => '<body><p>Le code pour se connecter à %1$s en tant que %2$s est </p><strong>%4$s</strong><p></p><p>Ce code est valide jusqu\'à %3$s</p><p>Si vous n\'essayez pas de vous connecter à %1$s, veuillez contacter votre administrateur.</p>',

	'MFATOTP:Error:SendMailFailed' => 'Erreur: échec de l\'envoi d\'email',
	'MFATOTP:Error:ConfigurationFailed' => 'Erreur: échec de la configuration de l\'app MFA',
	'MFATOTP:Error:SaveSettingsFailed' => 'Erreur: échec de l\'enregistrement des réglages MFA (probablement un email invalide)',

	'Class:MFAUserSettingsTOTP' => 'Authentification multifacteur basée sur TOTP',
	'Class:MFAUserSettingsTOTP/Attribute:secret' => 'Secret',
	'Class:MFAUserSettingsTOTP/Attribute:code_validity' => 'Validité du code',
	'Class:MFAUserSettingsTOTP/Attribute:epoch' => 'Date de début pour le calcul du code',
	'Class:MFAUserSettingsTOTP/Attribute:epoch+' => 'En secondes depuis le 1er janvier 1970',

	'Class:MFAUserSettingsTOTPApp' => 'Authentification multifacteur basée sur TOTP par app',

	'Class:MFAUserSettingsTOTPMail' => 'Email',
	'Class:MFAUserSettingsTOTPMail/Attribute:email' => 'Email',
));
