<?php 
require_once("./classes/class_view/fieldtypes/class_field.inc.php");

class class_field_string extends class_field {
	function class_field_string($fieldsettings) {
		parent::class_field($fieldsettings);

		if ( is_array( $fieldsettings ) ) {
			foreach ( $fieldsettings as $field => $value ) {
				switch ($field) {
					// only string specific parameters

				}
			}
		}
	}

	function view_field($row) {
		$retval = parent::view_field($row);

		$href2otherpage = $this->get_href();

		if ( $href2otherpage <> "" ) {
			$retval = $this->get_if_no_value($retval);

			$href2otherpage = Misc::ReplaceSpecialFieldsWithDatabaseValues($href2otherpage, $row);
			$href2otherpage = Misc::ReplaceSpecialFieldsWithQuerystringValues($href2otherpage);

			$retval = "<A HREF=\"" . $href2otherpage . "\" " . $this->getHtmlTarget() . " >" . $retval . "</a>";
		}

		return $retval;
	}
}
