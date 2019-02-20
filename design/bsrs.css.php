<?php 
header('Content-type: text/css');
require_once "../classes/misc.inc.php";
require_once "../classes/website_protection.inc.php";

// color (only 6 char/digit allowed)
$protect = new WebsiteProtection();
$c = $protect->request('get', 'c', '/^[0-9a-zA-Z]{6,6}$/');
if ( $c == '' ) {
	$c = '#73A0C9';
} else {
	$c = '#' . $c;
}
?>
html, body, input, select, textarea {
	font-family: Verdana;
}

.error {
	color: red;
	font-weight: bold;
	margin-bottom: 15px;
}

a
, a:visited
, a:active
, a:hover {
	color: <?php echo $c; ?>;
	text-decoration: none;
}

a.adminbar
, a.adminbar:visited
, a.adminbar:active
, a.adminbar:hover {
	color: yellow;
	text-decoration: underline;
}

input, select, textarea {
	border-width: 1px;
	border-style: solid;
	border-color: <?php echo $c; ?>;
}

input.inputError, select.inputError, textarea.inputError {
	border-color: red;
}

.button {
	color: <?php echo $c; ?>;
	background-color: white;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	padding-left: 15px;
	padding-right: 15px;
	padding-top: 3px;
	padding-bottom: 3px;
	border: 1px solid <?php echo $c; ?>;
	margin-left: 10px;
	margin-right: 10px;
	font-size: 90%;
}

h1 {
	color: <?php echo $c; ?>;
	font-family: 'Times New Roman';
	font-size: 200%;
	font-weight: bold;
	margin-top: 10px;
}

h2 {
	color: <?php echo $c; ?>;
	margin-top: 0px;
	margin-bottom: 15px;
	font-size: 130%;
}

.downloadribbon {
	margin-bottom: 15px;
}

div.wrapper {
	max-width: 1000px;
	margin: 0 auto;
}

div.header {
	max-width: 1000px;
	margin-top: auto;
	margin-bottom: auto;
}

div.logo {
	margin-left: -13px;
	margin-bottom: 7px;
	height: 94px;
	width: 122px;
}

div.content {
	max-width: 1000px;
	border: 1px solid #AAAAAA;
	text-align: center;
	margin-top: 5px;
	margin-bottom: 5px;
	padding-top: 5px;
	padding-bottom: 15px;
	padding-left: 5px;
	padding-right: 5px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
}

div.menu_admin {
	float: left;
	max-width: 185px;
	width: 185px;
	border: 1px solid #AAAAAA;
	text-align: left;
	margin-top: 5px;
	margin-bottom: 5px;
	padding-top: 5px;
	padding-bottom: 15px;
	padding-left: 5px;
	padding-right: 5px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
}

div.content_admin {
	float: right;
	max-width: 785px;
	width: 785px;
	border: 1px solid #AAAAAA;
	text-align: left;
	margin-top: 5px;
	margin-bottom: 5px;
	padding-top: 5px;
	padding-bottom: 15px;
	padding-left: 5px;
	padding-right: 5px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
}

div.disclaimer {
	margin: auto;
	max-width: 1200px;
	font-size: 75%;
	font-style: italic;
	color: #AAAAAA;
	margin-bottom: 15px;
}

table {
	margin: 0 auto;
}

div.adminbar {
	max-width: 1000px;
	background-color: red;
	color: white;
	font-weight: bold;
	border: 1px solid #AAAAAA;
	text-align: center;
	margin-top: 5px;
	margin-bottom: 5px;
	padding-top: 5px;
	padding-bottom: 5px;
	padding-left: 5px;
	padding-right: 5px;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
}

#error_existing_email {
	font-size: 80%;
}

.hidden {
	display: none;
}

.flag:hover {
	opacity:0.6;
}

table, th, td {
	border: 0px solid black;
}

table {
	border-spacing: 0px;
	border-collapse: collapse;
}

table.registrationForm tr td, table.login tr td, table.admin tr td {
	text-align: left;
	vertical-align: top;
	padding: 3px;
	white-space: nowrap;
}

table.admin {
	float: left;
}

table.login {
	margin-bottom: 15px;
}

hr {
	height: 1px;
	border: 0;
	background-color: <?php echo $c; ?>;
	width: 90%;
	margin-top: 15px;
	margin-bottom: 15px;
}

div.indentedCheckbox {
	display: inline-block;
	margin-left: 1.5em;
	text-indent: -1.5em;
	white-space: normal;
	float: left;
	width: 97%;
}

.required {
	margin-top: 20px;
	text-align: left;
	font-size: 85%;
	font-style: italic;
}

a.hrefCheckbox
, a.hrefCheckbox:visited
, a.hrefCheckbox:active
, a.hrefCheckbox:hover {
	color: black;
	text-decoration: none;
}

a.hrefSingleCheckbox
, a.hrefSingleCheckbox:visited
, a.hrefSingleCheckbox:active
, a.hrefSingleCheckbox:hover {
	font-size: 90%;
	font-style: italic;
}

input[type="text"], input[type="email"], select {
	width: 350px;
}

textarea {
	width: 350px;
	font-size: 88%;
}

input.admin[type="text"], input[type="email"], select.admin {
	width: 320px;
}

input.goal_admin[type="text"] {
	width: 250px;
}

textarea.admin {
	width: 320px;
	font-size: 88%;
}

textarea.research_subject, textarea.remarks_intern {
	width: 97%;
	font-size: 88%;
}

input.login, input.password {
	width: 175px;
}

input.inputError[type="checkbox"] {
	outline: 1px solid red;
}

.quicksearch {
	width: 100px !important;
}

.extrainfo {
	font-size: 85%;
	font-style: italic;
	margin-left: 0.5em;
	white-space: normal;
}

.prevButton {
	display:inline-block;
	width:20px;
	text-align:right;
}

.nextButton {
	display:inline-block;
	width:20px;
	text-align:left;
}

.linkdisabled {
	color: lightgrey;
}

.smaller {
	font-size: 85%;
}

.you_only_have_to_register_if {
    margin-top: 30px;
    font-size: 95%;
    font-style: italic;
}
