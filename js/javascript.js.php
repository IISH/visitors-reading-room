<?php
require_once "../classes/start.inc.php";

//
if ( !isset($settings) ) {
	$settings = array();
}
?>
// source: http://stackoverflow.com/questions/2400935/browser-detection-in-javascript
navigator.browser_detection= (function(){
	var ua= navigator.userAgent, tem,
			M= ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
	if(/trident/i.test(M[1])){
		tem=  /\brv[ :]+(\d+)/g.exec(ua) || [];
		return 'IE '+(tem[1] || '');
	}
	if(M[1]=== 'Chrome'){
		tem= ua.match(/\b(OPR|Edge)\/(\d+)/);
		if(tem!= null) return tem.slice(1).join(' ').replace('OPR', 'Opera');
	}
	M= M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
	if((tem= ua.match(/version\/(\d+)/i))!= null) M.splice(1, 1, tem[1]);
	return M.join(' ');
})();

function setSubmitType(submitType) {
	document.getElementById("submit_type").value = submitType;
}

function setSubmitTypeAndSubmitForm(submitType) {
	document.getElementById("submit_type").value = submitType;
	document.getElementById("frmBsrs").submit();

	return true;
}

function submitForm() {
	document.getElementById("frmBsrs").submit();
	return true;
}

function confirmCancelRegistration(text, code) {
	confirmValue = confirm(text);

	if ( confirmValue ) {
		setSubmitType(code);
	}

	return confirmValue;
}

function confirmCancelRegistrationAdmin(text, code) {
	// check if document is chagned
	if ( document.getElementById("ischanged").value != '' ) {
		// document is changed, ask if you want to cancel
		confirmValue = confirm(text);
		if ( confirmValue ) {
			// yes, set submit type
			setSubmitType(code);
		}
	} else {
		// nothing has changed, auto-allow cancel
		confirmValue = true;
		// set submit type
		setSubmitType(code);
	}

	return confirmValue;
}

function decreaseTimer() {
	//
	var fldTImer = document.getElementById('fldTimer');

	//
	var fldValue = fldTImer.value-1;

	//
	if ( fldValue < 0 ) {
		fldValue = 0;
	}

	//
	fldTImer.value = fldValue;

	//
	if ( fldValue > 0 ) {
		setTimeout(decreaseTimer, 1000);
	} else {
		setSubmitTypeAndSubmitForm('clear:registration');
		submitForm();
	}
}

function disableEmailFieldIfDontHaveEmailCheckedViaHref( changeIsChanged, id, subdir ) {
	document.getElementById("fldDontHaveEmail").checked = !document.getElementById("fldDontHaveEmail").checked;
	disableEmailFieldIfDontHaveEmailChecked(changeIsChanged, id, subdir)
	return false;
}

function disableEmailFieldIfDontHaveEmailChecked(changeIsChanged, id, subdir) {
	// if button pressed or input field changed
	if ( changeIsChanged == 1 ) {
		setIsChanged();
	}

	if ( document.getElementById("fldDontHaveEmail").checked ) {
		document.getElementById("fldEmail").readOnly = true;
		document.getElementById("fldEmail").style.backgroundColor = 'lightgrey';
		document.getElementById("fldEmail").style.borderColor = "#73A0C9";
	} else {
		document.getElementById("fldEmail").readOnly = false;
		document.getElementById("fldEmail").style.backgroundColor = 'white';
	}

	checkEmailAlreadyUsedInCurrentYear(id, subdir);

	return false;
}

function ifNoTmpDutchAddressHideTmpAddressFieldsViaHref() {
	document.getElementById("fldCountryTmp").checked = !document.getElementById("fldCountryTmp").checked;
	ifNoTmpDutchAddressHideTmpAddressFields();

	return false;
}

function ifNoTmpDutchAddressHideTmpAddressFields() {
	if ( document.getElementById("fldCountryTmp").checked ) {
		document.getElementById("fldAddressTmp").style.visibility = 'visible';
		document.getElementById("fldCityTmp").style.visibility = 'visible';
	} else {
		document.getElementById("fldAddressTmp").style.visibility = 'hidden';
		document.getElementById("fldCityTmp").style.visibility = 'hidden';
	}

	return false;
}

function checkUncheckResearchGoal( id ) {
	document.getElementById("fldResearchGoal"+id).checked = !document.getElementById("fldResearchGoal"+id).checked;
	return false;
}

function checkResearchGoalIfNotEmpty( id ) {
	if ( document.getElementById("fldResearchGoalText"+id).value != null && document.getElementById("fldResearchGoalText"+id).value != '' ) {
		document.getElementById("fldResearchGoal"+id).checked = true;
	}

	return false;
}

function checkUncheckCheckbox( id ) {
	document.getElementById("fldCheckbox"+id).checked = !document.getElementById("fldCheckbox"+id).checked;
	return false;
}

function checkEmailAlreadyUsedInCurrentYear(id, subdir) {
	emailField = document.getElementById("fldEmail");

	if ( emailField.value != '' ) {
		url = subdir + 'check_email.php?email=' + emailField.value + '&id=' + id;

		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (xhttp.readyState == 4 && xhttp.status == 200) {

				if ( xhttp.responseText == '<?php echo date("Y"); ?>' ) {
					// not okay

					// show error message
					document.getElementById("error_existing_email").style.visibility = 'visible';
					document.getElementById("fldEmail").style.borderColor = "red";

					//
					errorMessage = "<?php echo Translations::get('error_email_exists'); ?>";
					errorMessage = errorMessage.replace('{email}', emailField.value);

					//
					errorMessageAdmin = "<?php echo Translations::get('error_email_exists_admin'); ?>";
					errorMessageAdmin = errorMessageAdmin.replace('{email}', emailField.value);
					if ( id != 0 ) {
						// admin error message
						document.getElementById("error_existing_email").innerHTML = "<br>" + errorMessageAdmin;
						alert(errorMessageAdmin.replace('<br>', "\n"));
					} else {
						// visitor error message
						document.getElementById("error_existing_email").innerHTML = "<br>" + errorMessage;
						alert(errorMessage.replace('<br>', "\n"));
					}

					//
					emailField.value = '';
					emailField.focus();
					document.getElementById("fldFirstname").style.borderColor = "#73A0C9";
				} else if ( document.getElementById("isDirty").value == '0' && xhttp.responseText != '0' && xhttp.responseText <= '<?php echo date("Y"); ?>' ) {
					setSubmitTypeAndSubmitForm('data:load_from_database');
				} else {
					// okay, reset and hide
					hideErrorMessages();
					resetColors();
					document.getElementById("fldFirstname").focus();
				}
			}
		};
		xhttp.open("GET", url, true);
		xhttp.send();
	} else {
		// no check, reset and hide
		hideErrorMessages();
		resetColors();
		emailField.focus();
	}
}

function hideErrorMessages() {
	document.getElementById("error_existing_email").style.visibility = 'hidden';
	document.getElementById("error_existing_email").innerHTML = "";
}

function resetColors() {
	document.getElementById("fldEmail").style.borderColor = "#73A0C9";
	document.getElementById("fldFirstname").style.borderColor = "#73A0C9";
	document.getElementById("fldLastname").style.borderColor = "#73A0C9";
}

function checkField(fieldname) {
	if ( fieldname.value == '' ) {
		fieldname.style.borderColor = "red";
	} else {
		fieldname.style.borderColor = "#73A0C9";
	}
}

function checkFieldCheckbox(fieldname) {
	if ( fieldname.checked ) {
		fieldname.style.outline = "1px solid white";
	} else {
		fieldname.style.outline = "1px solid red";
	}
}

// TODOEXPLAIN
function open_page(url) {
window.open(url, '_top');
return false;
}

// TODOEXPLAIN
function open_page_blank(url) {
    window.open(url, '_blank');
    return false;
}

// TODOEXPLAIN
function doc_submit(pressedbutton) {
	document.getElementById("pressedbutton").value = pressedbutton;
	document.getElementById("frmBsrs").submit();

	return true;
}

// TODOEXPLAIN
function doc_delete(pressedbutton) {
	input_box=confirm('Please confirm delete');
	if (input_box==true) {

		document.getElementById("pressedbutton").value = pressedbutton;
		document.getElementById("FORM_is_deleted").value = '1';
		document.getElementById("frmBsrs").submit();

		return true;
	} else {
		return false;
	}
}

function setIsChanged() {
	document.getElementById("ischanged").value = 'yes';
}

function unprotectData() {
	document.getElementById("fldUnprotect").innerHTML = 'State: unprotected until ...<br><input type="button" name="btnProtect" value="Protect" onclick="protectData();">';
}

function protectData() {
	document.getElementById("fldUnprotect").innerHTML = 'State: Protected<br><input type="button" name="btnUnprotect" value="Unprotect" onclick="unprotectData();">';

}

function checkIfChanged() {
	if ( document.getElementById("ischanged").value != '' ) {
		return confirm("The modified data is not saved.\nProceed without saving?");
	}

	return true;
}
