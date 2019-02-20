<div class="indentedCheckbox">
	<input type="checkbox" name="fldResearchGoal{fieldValue}" id="fldResearchGoal{fieldValue}" value="{fieldValue}" {checked}>
	<label for="fldResearchGoal{fieldValue}">
		<a class="hrefCheckbox" href="#" onClick="checkUncheckResearchGoal('{fieldValue}'); return false;">{fieldLabel}</a>
	</label>
	<input class="{class}" type="{typeOfInputField}" name="fldResearchGoalText{fieldValue}" id="fldResearchGoalText{fieldValue}" value="{fldResearchGoalTextValue}" onkeypress="checkResearchGoalIfNotEmpty('{fieldValue}');">
</div>
