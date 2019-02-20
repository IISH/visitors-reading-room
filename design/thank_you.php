<input type="hidden" id="submit_type" name="submit_type" value="{submit_type}">

<h2>{page_thank_you_lbl_thank_you}</h2>

<a href="#" onClick="return setSubmitTypeAndSubmitForm('clear:registration');"><img src="images/misc/green_check.jpg" border="0" title="{page_thank_you_lbl_go_to_first_page}"></a><br>

<input type="hidden" id="fldTimer" value="{thank_you_timer_go_to_first_page_in_x_seconds}">
<script language="JavaScript">
setTimeout(decreaseTimer, 1000);
</script>

<a href="#" onClick="return setSubmitTypeAndSubmitForm('clear:registration');"><b>{page_thank_you_lbl_click_on_checkmark}</b></a>
