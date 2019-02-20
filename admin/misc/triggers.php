<?php
require_once "../../classes/start.inc.php";

//
if ( !isset($settings) ) {
	$settings = array();
}

$tables = array('administrators', 'checkboxes', 'countries', 'research_goals', 'settings', 'translations', 'visitors', 'visitor_addresses', 'visitor_checkboxes', 'visitor_research_goals', 'visitor_misc');

$trigger_template_after_insert = "
-- TRIGGER {table} AFTER UPDATE

DROP TRIGGER IF EXISTS {table}_after_insert;

DELIMITER //

CREATE TRIGGER {table}_after_insert
AFTER INSERT
   ON {table} FOR EACH ROW

BEGIN

   INSERT INTO revisions_{table}
      ( {revision_table_fields}, harddelete)
   VALUES
      ( {new_table_fields}, 0 );

END; //

DELIMITER ;
";

$trigger_template_after_update = "
-- TRIGGER {table} AFTER UPDATE

DROP TRIGGER IF EXISTS {table}_after_update;

DELIMITER //

CREATE TRIGGER {table}_after_update
AFTER UPDATE
   ON {table} FOR EACH ROW

BEGIN

   INSERT INTO revisions_{table}
      ( {revision_table_fields}, harddelete)
   VALUES
      ( {new_table_fields}, 0 );

END; //

DELIMITER ;
";

$trigger_template_before_delete = "
-- TRIGGER {table} BEFORE DELETE

DROP TRIGGER IF EXISTS {table}_before_delete;

DELIMITER //

CREATE TRIGGER {table}_before_delete
BEFORE DELETE
   ON {table} FOR EACH ROW

BEGIN

   INSERT INTO revisions_{table}
      ( {revision_table_fields}, harddelete)
   VALUES
      ( {old_table_fields}, 1 );

END; //

DELIMITER ;
";


preprint('-- Create BSRS Triggers');
preprint('-- Copy the code and execute it in the database');

preprint('-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -');

foreach ( $tables as $table ) {

	$fields = getListOfFieldsInSpecifiedTable( $table );

	$data = array();
	$labels = array();
	$labels['table'] = $table;
	$labels['revision_table_fields'] = implode(', ', $fields);
	$labels['new_table_fields'] = createListOfNewTableFields($fields);
	$labels['old_table_fields'] = createListOfOldTableFields($fields);

	$triggerAI = Misc::fillTemplate($trigger_template_after_insert, $data, $labels);
	preprint($triggerAI);

	$triggerAU = Misc::fillTemplate($trigger_template_after_update, $data, $labels);
	preprint($triggerAU);

	$triggerBD = Misc::fillTemplate($trigger_template_before_delete, $data, $labels);
	preprint($triggerBD);

	preprint('-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -');
}

preprint('-- End');


function getListOfFieldsInSpecifiedTable( $table ) {
	global $databases;

	$arr = array();

	$oConn = new class_mysql($databases['default']);
	$oConn->connect();

	$query = "SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = 'bsrs' AND TABLE_NAME = '$table' ORDER BY ordinal_position";
	$result = mysql_query($query, $oConn->getConnection());
	if ( mysql_num_rows($result) > 0 ) {

		while ($row = mysql_fetch_assoc($result)) {
			$arr[] = $row['COLUMN_NAME'];
		}
		mysql_free_result($result);
	}

	return $arr;
}

function createListOfNewTableFields($fields) {
	$ret = '';
	$separator = '';

	foreach ( $fields as $field ) {
		$ret .= $separator . 'NEW.' . $field;
		$separator = ', ';
	}

	return $ret;
}

function createListOfOldTableFields($fields) {
	$ret = '';
	$separator = '';

	foreach ( $fields as $field ) {
		switch ( $field ) {
			// if revision different new field value
			case "revision":
				$ret .= $separator . 'NOW()';
				break;
			// if ... different new field value
			case "modified_by":
				$ret .= $separator . 'USER()';
				break;
			default:
				$ret .= $separator . 'OLD.' . $field;
		}
		$separator = ', ';
	}

	return $ret;
}
