<?php

namespace Combodo\iTop\MFATotp\Hook;

use Combodo\iTop\MFABase\Helper\MFABaseException;
use Combodo\iTop\MFABase\Service\MFAPortalService;
use Combodo\iTop\MFABase\Service\MFAUserSettingsService;
use Combodo\iTop\MFATotp\Helper\MFATOTPHelper;
use Combodo\iTop\MFATotp\Service\MFATOTPService;
use Combodo\iTop\MFATotp\Service\OTPService;
use Combodo\iTop\Portal\Hook\iPortalTabContentExtension;
use Combodo\iTop\Portal\Twig\PortalBlockExtension;
use Combodo\iTop\Portal\Twig\PortalTwigContext;
use Dict;
use MFAUserSettingsTOTPMail;
use UserRights;
use utils;

class MFATotpMailPortalTabContentExtension implements iPortalTabContentExtension
{
	private $oMFAUserSettings;

	/**
	 * @inheritDoc
	 */
	public function IsActive(): bool
	{
		return MFAPortalService::GetInstance()->IsUserSettingsConfigurationRequired(MFAUserSettingsTOTPMail::class);
	}

	/**
	 * @inheritDoc
	 */
	public function GetTabCode(): string
	{
		return 'MyAccount-Tab-MFA';
	}

	/**
	 * @inheritDoc
	 */
	public function GetSectionRank(): float
	{
		return 0;
	}

	public function GetTarget(): string
	{
		return 'p_user_profile_brick';
	}

	/**
	 * Handle actions based on posted vars
	 */
	public function HandlePortalForm(array &$aData): void
	{
		$sUserId = UserRights::GetUserId();
		/** @var \MFAUserSettingsTOTPMail $oMFAUserSettings */
		$this->oMFAUserSettings = MFAUserSettingsService::GetInstance()->GetMFAUserSettings($sUserId, MFAUserSettingsTOTPMail::class);
		$aData['oMFAUserSettings'] = $this->oMFAUserSettings;
		try {
			$sRet = MFATOTPService::GetInstance()->ValidateCode($this->oMFAUserSettings);
			switch ($sRet) {
				case MFATOTPService::NO_CODE:
					if ($this->oMFAUserSettings->Get('validated') === 'yes') {
						$aData['sMessage'] = Dict::S('MFATOTP:Validated');
					} else {
						$aData['sError'] = Dict::S('MFATOTP:NotValidated');
					}
					break;

				case MFATOTPService::WRONG_CODE:
					$aData['sError'] = Dict::S('MFATOTP:NotValidated');
					break;

				case MFATOTPService::CODE_OK:
					$aData['sMessage'] = Dict::S('MFATOTP:Validated');
					$this->oMFAUserSettings->Set('validated', 'yes');
					// Only one validation allowed
					MFATOTPService::GetInstance()->RegenerateSecret($this->oMFAUserSettings);
					$this->oMFAUserSettings->AllowWrite();
					$this->oMFAUserSettings->DBUpdate();
					break;
			}
		} catch (MFABaseException $e) {
			$aData['sError'] = Dict::S('MFATOTP:Error:SendMailFailed');
		}
	}

	/**
	 * List twigs and variables for the tab content per block
	 *
	 * @return PortalTwigContext
	 */
	public function GetPortalTabContentTwigs(): PortalTwigContext
	{
		$oPortalTwigContext = new PortalTwigContext();

		$aData = [];

		$aData['oMFAUserSettings'] = $this->oMFAUserSettings;
		$aData['sModuleId'] = MFATOTPHelper::MODULE_NAME;
		$aData['sAjaxURL'] = utils::GetAbsoluteUrlExecPage();
		$aData['sAction'] = MFAPortalService::GetInstance()->GetSelectedAction();
		$aData['sClass'] = MFAUserSettingsTOTPMail::class;

		$sPath = MFATOTPHelper::MODULE_NAME.'/templates/portal/MFATotpMailView.html.twig';
		$oPortalTwigContext->AddBlockExtension('html', new PortalBlockExtension($sPath, $aData));
		$sPath = MFATOTPHelper::MODULE_NAME.'/templates/portal/MFATotpMailView.js.twig';
		$oPortalTwigContext->AddBlockExtension('script', new PortalBlockExtension($sPath, $aData));

		return $oPortalTwigContext;
	}
}
