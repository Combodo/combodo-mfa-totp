<?php
/**
 * @copyright   Copyright (C) 2010-2024 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

namespace Combodo\iTop\MFATotp\Service;

use Combodo\iTop\Application\Helper\Session;
use Combodo\iTop\MFABase\Helper\MFABaseException;
use Combodo\iTop\MFABase\Helper\MFABaseLog;
use Combodo\iTop\MFATotp\Helper\MFATOTPHelper;
use DateInterval;
use DateTime;
use Dict;
use EMail;
use Exception;
use LoginTwigContext;
use MetaModel;
use MFAUserSettingsTOTPMail;
use User;
use utils;

class MFATOTPMailService
{
	private static MFATOTPMailService $oInstance;
	private Email $oEmail;

	protected function __construct()
	{
		MFABaseLog::Enable();
		$this->oEmail = new EMail();
	}

	final public static function GetInstance(): MFATOTPMailService
	{
		if (!isset(static::$oInstance)) {
			static::$oInstance = new static();
		}

		return static::$oInstance;
	}

	public function GetEmail(): Email {
		return $this->oEmail;
	}

	public function SetEmail(Email $oEmail): void {
		$this->oEmail = $oEmail;
	}

	/**
	 * @param \MFAUserSettingsTOTPMail $oMFAUserSettings
	 *
	 * @return void
	 * @throws \Combodo\iTop\MFABase\Helper\MFABaseException
	 */
	public function SendCodeByEmail(MFAUserSettingsTOTPMail $oMFAUserSettings): void
	{
		try {
			// Update UserSettings
			$oMFAUserSettings->Set('epoch', time());
			MFATOTPService::GetInstance()->RegenerateSecret($oMFAUserSettings);
			$oMFAUserSettings->AllowWrite();
			$oMFAUserSettings->DBUpdate();

			$oTOTPService = new OTPService($oMFAUserSettings);
			$sCode = $oTOTPService->GetCode();
			$sEmail = $oMFAUserSettings->Get('email');
			$sUser = $oMFAUserSettings->Get('user_id_friendlyname');
			$iCodeValidity = $oMFAUserSettings->Get('code_validity');
			$oExpirationTime = new DateTime('now');
			$oExpirationTime->add(new DateInterval("PT{$iCodeValidity}S"));
			$sExpiration = $oExpirationTime->format("H:i");

			// Send the mail
			MFABaseLog::Debug("Send MFA code by email", MFABaseLog::CHANNEL_DEFAULT, [
				'user_id' => $sUser,
				'email' => $sEmail,
				'code' => $sCode,
				'expiration' => $sExpiration,
			]);

			$this->GetEmail()->SetRecipientTO($sEmail);
			$sFrom = MetaModel::GetConfig()->Get('email_default_sender_address');
			$this->GetEmail()->SetRecipientFrom($sFrom);
			$this->GetEmail()->SetSubject(Dict::Format('MFATOTP:Mail:EmailSubject', $oTOTPService->sIssuer, $sUser, $sExpiration));
			$this->GetEmail()->SetBody(Dict::Format('MFATOTP:Mail:EmailBody', $oTOTPService->sIssuer, $sUser, $sExpiration, $sCode));
			$iRes = $this->GetEmail()->Send($aIssues, true);
			switch ($iRes) {
				//case EMAIL_SEND_PENDING:
				case EMAIL_SEND_OK:
					break;

				case EMAIL_SEND_ERROR:
				default:
					MFABaseLog::Error('Failed to send the email with MFA code for '.$sUser.': '.implode(', ', $aIssues));
					throw new MFABaseException(Dict::S('MFATOTP:Error:SendMailFailed'));
			}
		} catch (MFABaseException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new MFABaseException(__FUNCTION__.' failed', 0, $e);
		}
	}

	/**
	 * @param \MFAUserSettingsTOTPMail $oMFAUserSettings
	 *
	 * @return void
	 * @throws \Combodo\iTop\MFABase\Helper\MFABaseException
	 */
	public function OnBeforeCreate(MFAUserSettingsTOTPMail $oMFAUserSettings): void
	{
		try {
			// Initialize email with the user's contact email
			$sUserId = $oMFAUserSettings->Get('user_id');
			$oUser = MetaModel::GetObject(User::class, $sUserId, true, true);
			$sEmail = $oUser->Get('email');
			if (utils::IsNullOrEmptyString($sEmail)) {
				$sEmail = 'please.replace@combodo.com';
			}
			$oMFAUserSettings->Set('email', $sEmail);
			$oMFAUserSettings->Set('code_validity', 600);
		} catch (MFABaseException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new MFABaseException(__FUNCTION__.' failed', 0, $e);
		}
	}

	/**
	 * @param \MFAUserSettingsTOTPMail $oMFAUserSettings
	 *
	 * @return void
	 * @throws \Combodo\iTop\MFABase\Helper\MFABaseException
	 */
	public function OnBeforeUpdate(MFAUserSettingsTOTPMail $oMFAUserSettings): void
	{
		try {
			// If email changed invalidate settings (need verification again)
			if (array_key_exists('email', $oMFAUserSettings->ListChanges())) {
				$oMFAUserSettings->Set('validated', 'no');
			}
		} catch (MFABaseException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new MFABaseException(__FUNCTION__.' failed', 0, $e);
		}
	}

	/**
	 * @param \MFAUserSettingsTOTPMail $oMFAUserSettings
	 *
	 * @return \LoginTwigContext
	 * @throws \Combodo\iTop\MFABase\Helper\MFABaseException
	 */
	public function GetTwigContextForConfiguration(MFAUserSettingsTOTPMail $oMFAUserSettings): LoginTwigContext
	{
		$aData = [];
		try {
			$oTOTPService = new OTPService($oMFAUserSettings);

			$aData['sTitle'] = Dict::S('Login:MFA:Validation:Title');
			$aData['sLabel'] = $oTOTPService->sLabel;
			$aData['sIssuer'] = $oTOTPService->sIssuer;
			$aData['sTransactionId'] = utils::GetNewTransactionId();

			$oLoginContext = $this->ValidateCode($oMFAUserSettings, $aData);
			if (!is_null($oLoginContext)) {
				return $oLoginContext;
			}

			$this->SendCodeByEmail($oMFAUserSettings);
		} catch (MFABaseException $e) {
			$aData['sError'] = $e->getMessage();
		} catch (Exception $e) {
			throw new MFABaseException(__FUNCTION__.' failed', 0, $e);
		}

		try {
			$oLoginContext = new LoginTwigContext();
			$oLoginContext->SetLoaderPath(MODULESROOT.MFATOTPHelper::MODULE_NAME.'/templates/login');
			$oLoginContext->AddBlockExtension('mfa_configuration', new \LoginBlockExtension('MFATOTPMailValidate.html.twig', $aData));
			$oLoginContext->AddBlockExtension('mfa_title', new \LoginBlockExtension('MFATOTPTitle.html.twig', $aData));
			$oLoginContext->AddBlockExtension('script', new \LoginBlockExtension('MFATOTPMailValidate.ready.js.twig', $aData));
			$oLoginContext->AddJsFile(MFATOTPHelper::GetJSFile());

			return $oLoginContext;
		} catch (Exception $e) {
			throw new MFABaseException(__FUNCTION__.' failed', 0, $e);
		}
	}

	/**
	 * @param \MFAUserSettingsTOTPMail $oMFAUserSettings
	 *
	 * @return \LoginTwigContext
	 * @throws \Combodo\iTop\MFABase\Helper\MFABaseException
	 */
	public function GetTwigContextForLoginValidation(MFAUserSettingsTOTPMail $oMFAUserSettings): LoginTwigContext
	{
		try {
			return $this->GetTwigContextForConfiguration($oMFAUserSettings);
		} catch (MFABaseException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new MFABaseException(__FUNCTION__.' failed', 0, $e);
		}
	}

	/**
	 * @param \MFAUserSettingsTOTPMail $oMFAUserSettings
	 * @param array $aData
	 *
	 * @return \LoginTwigContext|null
	 * @throws \Combodo\iTop\MFABase\Helper\MFABaseException
	 */
	private function ValidateCode(MFAUserSettingsTOTPMail $oMFAUserSettings, array &$aData): ?LoginTwigContext
	{
		try {
			$sRet = MFATOTPService::GetInstance()->ValidateCode($oMFAUserSettings);
			switch ($sRet) {
				case MFATOTPService::WRONG_CODE:
					$aData['sError'] = Dict::S('MFATOTP:Mail:NotValidated');
					break;

				case MFATOTPService::CODE_OK:
					$oMFAUserSettings->Set('validated', 'yes');
					$oMFAUserSettings->Set('is_default', 'yes');
					// Only one validation allowed (one time password)
					MFATOTPService::GetInstance()->RegenerateSecret($oMFAUserSettings);
					$oMFAUserSettings->AllowWrite();
					$oMFAUserSettings->DBUpdate();

					Session::Set('mfa_configuration_validated', 'true');
					$aData['sURL'] = utils::GetAbsoluteUrlAppRoot();
					$aData['sTitle'] = Dict::S('MFATOTP:Redirection:Title');
					$oLoginContext = new LoginTwigContext();
					$oLoginContext->SetLoaderPath(MODULESROOT.MFATOTPHelper::MODULE_NAME.'/templates/login');
					$oLoginContext->AddBlockExtension('mfa_title', new \LoginBlockExtension('MFATOTPTitle.html.twig', $aData));
					$oLoginContext->AddBlockExtension('script', new \LoginBlockExtension('MFATOTPRedirect.ready.js.twig', $aData));

					return $oLoginContext;
			}

			return null;
		} catch (MFABaseException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new MFABaseException(__FUNCTION__.' failed', 0, $e);
		}
	}

	/**
	 *
	 * @param \MFAUserSettingsTOTPMail $oMFAUserSettings
	 * @param $sEmail
	 *
	 * @return \MFAUserSettingsTOTPMail
	 * @throws \Combodo\iTop\MFABase\Helper\MFABaseException
	 */
	public function ChangeEmailAddress(MFAUserSettingsTOTPMail $oMFAUserSettings, $sEmail): MFAUserSettingsTOTPMail
	{
		try {
			$oMFAUserSettings->Set('email', $sEmail);
			$oMFAUserSettings->AllowWrite();
			$oMFAUserSettings->DBUpdate();
		} catch (Exception $e) {
			throw new MFABaseException(__FUNCTION__.' failed', 0, $e);
		}

		return $oMFAUserSettings;
	}
}
