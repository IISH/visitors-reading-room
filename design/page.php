<!doctype html>

<html>
<head>
	<title>{title}</title>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta name="Robots" content="noindex,nofollow" />
	<link rel="stylesheet" type="text/css" href="design/bsrs.css.php?c={color}" />
	<script type="text/javascript" src="js/javascript.js.php"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
</head>
<body>

<div class="wrapper">
	<form id="frmBsrs" name="frmBsrs" method="POST">

		<div class="adminbar {adminbar_visibility}" id="adminbar" name="adminbar">{adminbar_javascript}</div>

		<div class="header">
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="130"><a href="#" onClick="return setSubmitTypeAndSubmitForm('cancel:registration');"><div class="logo"><img src="images/logo/{color}.png"></div></a></td>
					<td><h1>{website_name}</h1></td>
					<td align="right" width="160">&nbsp;{languages_javascript}</td>
				</tr>
			</table>
		</div>

		<div class="content">
			{content}
		</div>
	</form>
</div>

<script>
$(document).ready(function(){
});
$(":input").change(function () {
    if ( $(this).attr('name') != 'fldEmail' && $(this).attr('name') != 'fldCountryTmp' && $(this).attr('name') != 'fldDontHaveEmail' && $(this).attr('name') != 'submit_type' && $(this).attr('name') != 'isDirty' ) {
        $('#isDirty').val( parseInt($('#isDirty').val(),10) + 1 );
    }
});
</script>
</body>
</html>
