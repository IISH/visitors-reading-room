<?php
require_once "../../classes/start.inc.php";

//
if ( !defined('JSON_PRETTY_PRINT') ) {
	define('JSON_PRETTY_PRINT', 128);
}

//
if ( !isset($settings) ) {
	$settings = array();
}

// get year
$year = $protect->requestFieldDefaultMinMax('get', 'year', date("Y"), 0, 9999);

$arr = array();
$query = '';

// only if correct year
if ( $year >= Settings::get('first_static_year') && $year <= date("Y")) {
	// create query
	if ( $year < Settings::get('first_year') ) {
		// 2014..2015
		$query = "
SELECT name_en, geocoder_id, coordinates, iso_code, total
FROM `countries`
	INNER JOIN `static_statistics_countries` ON `countries`.id=`static_statistics_countries`.country_id
WHERE static_statistics_countries.year=" . $year . "
ORDER BY total DESC, name_en
";
	} else {
		// 2016..
		$query = "
SELECT name_en, geocoder_id, coordinates, iso_code, count(*) AS total
FROM `visitors`
	INNER JOIN `visitor_addresses` ON `visitors`.id=`visitor_addresses`.visitor_id
	INNER JOIN countries ON `visitor_addresses`.country_id=countries.id
WHERE `visitors`.is_deleted=0
	AND `visitor_addresses`.is_deleted=0
	AND `visitor_addresses`.year=" . $year . "
GROUP BY name_en, geocoder_id, coordinates, iso_code
ORDER BY total DESC, name_en
";
	}

	// create db connection
	$oConn = new class_mysql($databases['default']);
	$oConn->connect();

	$result = mysql_query($query, $oConn->getConnection());
	if ( mysql_num_rows($result) > 0 ) {
		while($row = mysql_fetch_assoc($result)) {
			$arr[] = array(
				'country' => $row['name_en']
				, 'iso_code' => $row['iso_code']
				, 'geocoder_id' => $row['geocoder_id']
				, 'coordinates' => $row['coordinates']
				, 'total' => $row['total']
				);
		}

		mysql_free_result($result);
	}
}

$output = array();
$output['title'] = 'IISG Reading Room Visitors ' . $year . ' Statistics';
$output['source'] = full_url( $_SERVER, true );
$output['year'] = $year;
$output['export_date'] = date("Y-m-d H:i:s");
$output['copyright'] = 'International Institute of Social History (IISG)';
$output['url'] = 'http://iisg.amsterdam';
$output['data'] = $arr;

//
header('Content-Type: application/json');
echo json_encode( $output, JSON_PRETTY_PRINT );

function url_origin( $s, $use_forwarded_host = false ) {
	$ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
	$sp       = strtolower( $s['SERVER_PROTOCOL'] );
	$protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
	$port     = $s['SERVER_PORT'];
	$port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
	$host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
	$host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
	return $protocol . '://' . $host;
}

function full_url( $s, $use_forwarded_host = false ) {
	return url_origin( $s, $use_forwarded_host ) . $s['REQUEST_URI'];
}
