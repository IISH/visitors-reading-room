<input type="hidden" id="submit_type" name="submit_type" value="{submit_type}">

<h2>{page_login_lbl_please_log_in}</h2>

<div class="error {error_visibility}">{error}</div>

<table class="login">
	<tr>
		<td align="left">{page_login_lbl_loginname}</td>
		<td><input type="text" name="fldLogin" class="login" maxlength="50" value="{fldLogin}" placeholder="{page_login_loginname_format}"></td>
	</tr>
	<tr>
		<td align="left">{page_login_lbl_password}</td>
		<td><input type="password" name="fldPassword" class="password" maxlength="50" placeholder="{page_login_password_comment}"></td>
	</tr>
</table>

<div>
	<input class="button" type="reset" name="btnReset" value="{page_login_btn_clear}">
	<input class="button" type="submit" name="btnSubmit" value="{page_login_btn_login}">
</div>

<script language="javascript">
<!--
document.frmBsrs.fldLogin.focus();
// -->
</script>
