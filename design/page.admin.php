<!doctype html>

<html>
<head>
	<title>{title}</title>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta name="Robots" content="noindex,nofollow" />
	<link rel="stylesheet" type="text/css" href="{level}design/bsrs.css.php?c={color}" />
	<script type="text/javascript" src="{level}js/javascript.js.php"></script>
</head>
<body>

<div class="wrapper">
		<div class="adminbar {adminbar_visibility}" id="adminbar" name="adminbar">{adminbar_click}</div>

		<div class="header">
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="130"><a href="../"><div class="logo"><img src="{level}images/logo/{color}.png"></div></a></td>
					<td><h1>{website_name}</h1></td>
					<td align="right" width="160">&nbsp;{languages_click}</td>
				</tr>
			</table>
		</div>

		<div class="menu_admin">
			{menu}
		</div>

		<div class="content_admin">
			{content}
		</div>
</div>

</body>
</html>
