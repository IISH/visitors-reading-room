<?php
// create sql command's for clearing the revisions data and resetting the autonumber

require_once "../../classes/start.inc.php";

//
if ( !isset($settings) ) {
	$settings = array();
}

$tables = array( "revisions_checkboxes", "revisions_countries", "revisions_research_goals", "revisions_settings", "revisions_translations", "revisions_visitors", "revisions_visitor_addresses", "revisions_visitor_checkboxes", "revisions_visitor_misc", "revisions_visitor_research_goals" );

$template = "
-- clear table {tablename}
TRUNCATE TABLE {tablename};
ALTER TABLE {tablename} AUTO_INCREMENT = 1;

";

preprint("-- Create BSRS Clear data tables commands

-- Copy the code and execute it in the database

-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
");

foreach ( $tables as $table ) {
	preprint(str_replace('{tablename}', $table, $template));
	preprint('-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -');
}

preprint('-- End');
