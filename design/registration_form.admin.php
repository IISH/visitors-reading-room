<form id="frmBsrs" name="frmBsrs" method="POST" onchange="setIsChanged();">

<input type="hidden" name="ischanged" id="ischanged" value="">
<input type="hidden" id="submit_type" name="submit_type">
<input type="hidden" id="isDirty" name="isDirty">

<h2>{fldFirstLastname} ({lblYear})</h2>

<div class="error {error_visibility}">{error}</div>

<table class="registrationForm">

	<tr>
		<td>{page_registrationform_lbl_id}</td>
		<td><span class="smaller">{fldId}</span></td>
	</tr>

	<tr>
		<td>{page_registrationform_lbl_email} {requiredsign}</td>
		<td colspan="2"><input class="admin {fldEmail_fieldstyle}" type="text" name="fldEmail" id="fldEmail" value="{fldEmail}" onblur="checkEmailAlreadyUsedInCurrentYear({id}, '../');checkField(this);"> <span id="error_existing_email" class="error"></span><br>
			<input type="checkbox" name="fldDontHaveEmail" id="fldDontHaveEmail" {fldDontHaveEmail} onclick="disableEmailFieldIfDontHaveEmailChecked(1,{id},'../');"> <a class="hrefCheckbox hrefSingleCheckbox" href="#" onclick="return disableEmailFieldIfDontHaveEmailCheckedViaHref(1,{id},'../');">{page_registrationform_lbl_dont_have_email}</a></td>
		<!--
	<td rowspan="3">
		<span id="fldUnprotect" name="fldUnprotect">State: Protectedaaaaa<br><input type="button" name="btnUnprotect" value="Unprotect" onclick="unprotectData();"></span>
		</td>
		// -->
	</tr>

	<tr>
		<td>{page_registrationform_lbl_firstname} {requiredsign}</td>
		<td><input class="admin {fldFirstname_fieldstyle}" type="text" name="fldFirstname" id="fldFirstname" value="{fldFirstname}" onblur="checkField(this);"></td>
	</tr>

	<tr>
		<td>{page_registrationform_lbl_lastname} {requiredsign}</td>
		<td><input class="admin {fldLastname_fieldstyle}" type="text" name="fldLastname" id="fldLastname" value="{fldLastname}" onblur="checkField(this);"></td>
	</tr>

	<tr>
		<td>{page_registrationform_lbl_country} {requiredsign}</td>
		<td>
			<select class="admin {fldCountry_fieldstyle}" name="fldCountry" id="fldCountry" onchange="checkField(this);">
				<option value="">{page_registrationform_lbl_choose_country}</option>
				{countryList}
			</select>
		</td>
		<td><input type="checkbox" name="fldCountryTmp" id="fldCountryTmp" onClick="ifNoTmpDutchAddressHideTmpAddressFields();" {fldCountryTmp}> <a class="hrefCheckbox hrefSingleCheckbox" href="#" onclick="return ifNoTmpDutchAddressHideTmpAddressFieldsViaHref();">{page_registrationform_lbl_tmp_address}</a></td>
	</tr>

	<tr>
		<td>{page_registrationform_lbl_address}</td>
		<td><textarea class="admin" name="fldAddress" id="fldAddress" rows="2">{fldAddress}</textarea></td>
		<td><textarea class="admin" name="fldAddressTmp" id="fldAddressTmp" rows="2">{fldAddressTmp}</textarea></td>
	</tr>

	<tr>
		<td>{page_registrationform_lbl_city}</td>
		<td><input class="admin" type="text" name="fldCity" id="fldCity" value="{fldCity}"></td>
		<td><input class="admin" type="text" name="fldCityTmp" id="fldCityTmp" value="{fldCityTmp}"></td>
	</tr>

	<!-- SUBJECT -->
	<tr>
		<td colspan="3">{page_registrationform_lbl_remarks_intern}</td>
	</tr>
	<tr>
		<td colspan="3"><textarea class="remarks_intern" name="fldRemarksIntern" id="fldRemarksIntern" rows="2">{fldRemarksIntern}</textarea></td>
	</tr>

	<!-- SUBJECT -->
	<tr>
		<td colspan="3">{page_registrationform_lbl_subject}</td>
	</tr>
	<tr>
		<td colspan="3"><textarea class="research_subject" name="fldResearchSubject" id="fldResearchSubject" rows="2">{fldResearchSubject}</textarea></td>
	</tr>

	<!-- RESEARCH GOALS -->
	<tr>
		<td colspan="3">{page_registrationform_lbl_goal}</td>
	</tr>
	<tr>
		<td colspan="3">{goalsList}</td>
	</tr>
	<tr>
		<td colspan="3"><div class="extrainfo">{page_registrationform_lbl_copy_of_work}</div></td>
	</tr>

	<!-- SEPARATOR -->
	<tr>
		<td colspan="3"><hr></td>
	</tr>

	<!-- NEWSLETTER -->
	<tr>
		<td colspan="3">
			<div class="indentedCheckbox">
				<input type="checkbox" name="fldNewsletter" id="fldNewsletter" {fldNewsletterChecked}>
				<label for="fldNewsletter">{page_registrationform_lbl_newsletter}</label>
			</div>
		</td>
	</tr>

	<!-- TERMS AND CONDITIONS -->
	<tr>
		<td colspan="3">
			<div>{checkboxesList}</div>
		</td>
	</tr>
</table>
<br>

<!-- BUTTONS -->
<div>
	<input class="button" type="submit" name="btnCancel" onClick="return confirmCancelRegistrationAdmin('{page_registrationform_message_confirm_cancel_admin_registration}','cancel:admin_registration');" value="{page_registrationform_btn_cancel}">
	<input class="button" type="submit" name="btnSubmit" value="{page_registrationform_btn_save_admin}">
</div>

<div class="required" style="">
	{requiredsign} - {requiredsign_explanation}
</div>

</form>

<script language="JavaScript">
	<!--
	disableEmailFieldIfDontHaveEmailChecked(0,{id},'../');
	ifNoTmpDutchAddressHideTmpAddressFields();
	// -->
</script>
