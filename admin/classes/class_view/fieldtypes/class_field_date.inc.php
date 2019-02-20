<?php 
require_once("./classes/class_view/fieldtypes/class_field.inc.php");

class class_field_date extends class_field {
	private $m_format;

	function class_field_date($fieldsettings) {
		parent::class_field($fieldsettings);

		$this->m_format = '';

		if ( is_array( $fieldsettings ) ) {
			foreach ( $fieldsettings as $field => $value ) {
				switch ($field) {
					// only string specific parameters

					case "format":
						$this->m_format = $fieldsettings["format"];
						break;

				}
			}
		}

	}

	function view_field($row) {
		$retval = parent::view_field($row);

		if ( $retval != '' ) {
			// verwijder tijd uit datum
			$retval = trim(str_replace('12:00:00:000AM', '', $retval));
			$retval = trim(str_replace('12:00AM', '', $retval));

			if ( $this->m_format != '' ) {
				$retval = date($this->m_format, strtotime($retval));
			}
		}

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
