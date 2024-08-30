// @copyright   Copyright (C) 2010-2024 Combodo SARL
// @license     http://opensource.org/licenses/AGPL-3.0

function CheckLoginCode() {
	var sCode = $("#totp_code").val();
	if (sCode.length === 6) {
		$("#totp_form").trigger("submit");
	}
}