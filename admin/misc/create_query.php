<?php
// create sql command's

require_once "../../classes/start.inc.php";

//
if ( !isset($settings) ) {
	$settings = array();
}

$tables = array( "revisions_checkboxes", "revisions_countries", "revisions_research_goals", "revisions_settings", "revisions_translations", "revisions_visitors", "revisions_visitor_addresses", "revisions_visitor_checkboxes", "revisions_visitor_misc", "revisions_visitor_research_goals", "visitors", "visitor_addresses", "visitor_checkboxes", "visitor_misc", "visitor_research_goals" );

/*
$template = "
-- show data for table {tablename}
SELECT * FROM `{tablename}`;

";
*/

/*
$template = "
--
UPDATE `{tablename}`
SET date_added = revision
WHERE date_added IS NULL;
";
*/

$template = "
--
UPDATE `{tablename}`
SET revision = '2015-01-01 00:00:00'
WHERE revision IS NULL;

UPDATE `{tablename}`
SET date_added = '2015-01-01 00:00:00'
WHERE date_added IS NULL;
";

preprint("-- Copy the code and execute it in the database

-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
");

foreach ( $tables as $table ) {
	preprint(str_replace('{tablename}', $table, $template));
	preprint('-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -');
}

preprint('-- End');
