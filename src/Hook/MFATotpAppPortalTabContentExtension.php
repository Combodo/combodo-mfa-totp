<?php

namespace Combodo\iTop\MFATotp\Hook;

use Combodo\iTop\MFABase\Service\MFAPortalService;
use Combodo\iTop\MFABase\Service\MFAUserSettingsService;
use Combodo\iTop\MFATotp\Helper\MFATOTPHelper;
use Combodo\iTop\MFATotp\Service\MFATOTPService;
use Combodo\iTop\MFATotp\Service\OTPService;
use Combodo\iTop\Portal\Hook\iUserProfileTabContentExtension;
use Combodo\iTop\Portal\Twig\PortalBlockExtension;
use Combodo\iTop\Portal\Twig\PortalTwigContext;
use Dict;
use MFAUserSettingsTOTPApp;
use UserRights;

if (interface_exists(iUserProfileTabContentExtension::class)) {

	class MFATotpAppPortalTabContentExtension implements iUserProfileTabContentExtension
	{
		private $oMFAUserSettings;

		/**
		 * @inheritDoc
		 */
		public function IsActive(): bool
		{
			return MFAPortalService::GetInstance()->IsUserSettingsConfigurationRequired(MFAUserSettingsTOTPApp::class);
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

		/**
		 * Handle actions based on posted vars
		 */
		public function HandlePortalForm(array &$aData): void
		{
			$sUserId = UserRights::GetUserId();
			/** @var MFAUserSettingsTOTPApp $oUserSettings */
			$this->oMFAUserSettings = MFAUserSettingsService::GetInstance()->GetMFAUserSettings($sUserId, MFAUserSettingsTOTPApp::Class);

			$sRet = MFATOTPService::GetInstance()->ValidateCode($this->oMFAUserSettings);
			switch ($sRet) {
				case MFATOTPService::NO_CODE:
					if ($this->oMFAUserSettings->Get('validated') === 'yes') {
						$aData['sMessage'] = Dict::S('MFATOTP:App:Validated');
					}
					break;

				case MFATOTPService::WRONG_CODE:
					$aData['sError'] = Dict::S('MFATOTP:App:NotValidated');
					break;

				case MFATOTPService::CODE_OK:
					$aData['sMessage'] = Dict::S('MFATOTP:App:Validated');
					$this->oMFAUserSettings->Set('validated', 'yes');
					$this->oMFAUserSettings->AllowWrite();
					$this->oMFAUserSettings->DBUpdate();
					break;
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

			$oTOTPService = new OTPService($this->oMFAUserSettings);

			$aData = [];
			$aData['sAction'] = MFAPortalService::GetInstance()->GetSelectedAction();
			$aData['sClass'] = MFAUserSettingsTOTPApp::class;
			$aData['sQRCodeSVG'] = $oTOTPService->GetQRCodeSVG();
			$aData['sLabel'] = $oTOTPService->sLabel;
			$aData['sIssuer'] = $oTOTPService->sIssuer;
			$aData['sSecret'] = $this->oMFAUserSettings->Get('secret');

			$sPath = MFATOTPHelper::MODULE_NAME.'/templates/portal/MFATotpAppPortalConfig.html.twig';
			$oPortalTwigContext->AddBlockExtension('html', new PortalBlockExtension($sPath, $aData));
			$sPath = MFATOTPHelper::MODULE_NAME.'/templates/portal/MFATotpAppPortalConfig.js.twig';
			$oPortalTwigContext->AddBlockExtension('script', new PortalBlockExtension($sPath, $aData));

			return $oPortalTwigContext;
		}
	}

}
