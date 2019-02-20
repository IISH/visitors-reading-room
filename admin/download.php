<?php
require_once "../classes/start.inc.php";

//
if ( !isset($settings) ) {
	$settings = array();
}

//
$oWebuser->checkLoggedInAsAdminOrSuperadmin();

$download["dataset"] = $protect->requestCharactersAndDigits('get', 'dataset');
$download['year'] = $protect->requestFieldDefaultMinMax('get', 'year', date("Y"), Settings::get('first_static_year'), date("Y"));

$query = '';

switch ( $download["dataset"] ) {

	case "mailchimp":
		$download["filename"] = Translations::get('admin_page_mailchimp_title') . ' ' . $download['year'];
		$query = "
SELECT visitor_misc.firstname AS `" . Translations::get('fld_firstname') . "`, visitor_misc.lastname AS `" . Translations::get('fld_lastname') . "`, visitors.email AS `" . Translations::get('fld_email') . "`
FROM `visitors`
	INNER JOIN `visitor_misc` ON `visitors`.id=`visitor_misc`.visitor_id
WHERE `visitors`.is_deleted=0
	AND `visitor_misc`.newsletter=1
	AND `visitor_misc`.is_deleted=0
	AND `visitor_misc`.year={year}
ORDER BY visitor_misc.lastname, visitor_misc.firstname ";
		break;

	case "countriesperyear":
		$download["filename"] = Translations::get('admin_page_statistics_title') . ' - ' . Translations::get('admin_page_statistics_countries_title') . ' ' . $download['year'];

		if ( $download['year'] < Settings::get('first_year') ) {
			$query = "
SELECT name_" . Misc::getLanguage() . " AS `" . Translations::get('fld_country') . "`, total AS `" . Translations::get('fld_total') . "`
FROM `countries`
	INNER JOIN `static_statistics_countries` ON `countries`.id=`static_statistics_countries`.country_id
WHERE year=" . $download['year'] . "
GROUP BY country_id, name_" . Misc::getLanguage() . "
ORDER BY `" . Translations::get('fld_total') . "` DESC, `" . Translations::get('fld_country') . "` ";

		} else {
			$query = "
SELECT name_" . Misc::getLanguage() . " AS `" . Translations::get('fld_country') . "`, count(*) AS `" . Translations::get('fld_total') . "`
FROM `visitors`
	INNER JOIN `visitor_addresses` ON `visitors`.id=`visitor_addresses`.visitor_id
	INNER JOIN countries ON `visitor_addresses`.country_id=countries.id
WHERE `visitors`.is_deleted=0
	AND `visitor_addresses`.is_deleted=0
	AND `visitor_addresses`.year={year}
GROUP BY country_id, name_" . Misc::getLanguage() . "
ORDER BY count(*) DESC, name_" . Misc::getLanguage() . "
";
		}

		break;

	case "totalsperyear":
		$download["filename"] = Translations::get('admin_page_statistics_title') . ' - ' . Translations::get('admin_page_statistics_year_title');
		$query = "
SELECT year AS `" . Translations::get('fld_year') . "`, count(*) AS `" . Translations::get('fld_total') . "`
FROM `visitors`
	INNER JOIN `visitor_addresses` ON `visitors`.id=`visitor_addresses`.visitor_id
WHERE `visitors`.is_deleted=0
	AND `visitor_addresses`.is_deleted=0
GROUP BY SUBSTRING(`" . Translations::get('fld_year') . "`,1,4)

UNION

SELECT `year` AS `" . Translations::get('fld_year') . "`, SUM(total) as _COUNT
FROM `static_statistics_countries`
GROUP BY `" . Translations::get('fld_year') . "`

ORDER BY SUBSTRING(`" . Translations::get('fld_year') . "`,1,4) DESC
";
		break;

	case "totalspermonth":
		$download["filename"] = Translations::get('admin_page_statistics_title') . ' - ' . Translations::get('admin_page_statistics_month_title');
		$query = "
SELECT SUBSTRING(visitor_addresses.date_added,1,7) AS `" . Translations::get('fld_month') . "`, count(*) AS `" . Translations::get('fld_total') . "`
FROM `visitors`
	INNER JOIN `visitor_addresses` ON `visitors`.id=`visitor_addresses`.visitor_id
WHERE `visitors`.is_deleted=0
	AND `visitor_addresses`.is_deleted=0
GROUP BY SUBSTRING(visitor_addresses.date_added,1,7)
ORDER BY SUBSTRING(visitor_addresses.date_added,1,7) DESC
";
		break;

	case "totalsperday":
		$download["filename"] = Translations::get('admin_page_statistics_title') . ' - ' . Translations::get('admin_page_statistics_day_title');
		$query = "
SELECT SUBSTRING(visitor_addresses.date_added,1,10) AS `" . Translations::get('fld_day') . "`, count(*) AS `" . Translations::get('fld_total') . "`
FROM `visitors`
	INNER JOIN `visitor_addresses` ON `visitors`.id=`visitor_addresses`.visitor_id
WHERE `visitors`.is_deleted=0
	AND `visitor_addresses`.is_deleted=0
GROUP BY SUBSTRING(visitor_addresses.date_added,1,10)
ORDER BY SUBSTRING(visitor_addresses.date_added,1,10) DESC
";
		break;

	default:
		die('Error 8541278: unknown download choice');
}
$query = str_replace("{year}", $download['year'], $query);

//
if ( $download["filename"] == '' ) {
	$download["filename"] = $download["dataset"] . ' ' . $download['year'];
}

// filename
// TODO: easy filter (remove forbidden chars) everything except digits characters _ - and that kind of stuff
$download["filename"] = substr(trim(str_replace(array("\t", "\r", "\n", "..", "/", "\\", "(", ")", "?", "!", "|", ":", ";", "%20"), ' ', $download["filename"])), 0, 50);
$download["filename"] .=  " (" . Translations::get('export_date') . " " . date(Settings::get('export_date_format')) . ")";

$output = '';
$headers = '';
$counter = 0;
$oConn = new class_mysql($databases['default']);
$oConn->connect();

$result = mysql_query($query, $oConn->getConnection());
if ( mysql_num_rows($result) > 0 ) {
	while($row = mysql_fetch_assoc($result)) {
		$counter++;
		$separator = '';
		foreach($row as $key => $value) {
			if ( $counter == 1 ) {
				$headers .= $separator . trim($key);
			}
			$output .= $separator .  trim(str_replace(array("\t", "\r", "\n"), " ", $value));
			$separator = "\t";
		}
		$output .= "\r\n";
	}

	mysql_free_result($result);
}

$output = $download["filename"] . "\r\n" . $headers . "\r\n" . $output;

//
header( "Content-type: text/plain" );
header( "Content-Disposition: attachment; filename=\"" . strtolower($download["filename"]) . ".txt\"" );
echo $output;
