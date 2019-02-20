<?php 
require_once("./classes/class_form/fieldtypes/class_field.inc.php");

class class_field_date extends class_field {
	function class_field_date($fieldsettings) {
		parent::class_field($fieldsettings);

		if ( is_array( $fieldsettings ) ) {
			foreach ( $fieldsettings as $field => $value ) {
				switch ($field) {
					// only string specific parameters

				}
			}
		}
	}

	function form_field($row, $m_form, $required_typecheck_result = 0 ) {
		// welke waarde moeten we gebruiken, uit de db? of uit de form?
		// indien niet goed bewaard gebruik dan de form waarde
		if ( $required_typecheck_result == 0 ) {
			$veldwaarde = $this->get_form_value();
		} else {
			$veldwaarde = $row[$this->get_fieldname()];

			$onNewValue = $this->get_onNew($m_form["primarykey"]);
			if ( $onNewValue != "" ) {
				$veldwaarde = $onNewValue;
			}
		}

		// strip slashes
		$veldwaarde = stripslashes($veldwaarde);
		$veldwaarde = str_replace("\"", "&quot;", $veldwaarde);

		// verwijder tijd uit datum
		$veldwaarde = trim(str_replace('12:00:00:000AM', '', $veldwaarde));
		$veldwaarde = trim(str_replace('12:00AM', '', $veldwaarde));
		$veldwaarde = trim(str_replace('00:00:00', '', $veldwaarde));

		$veldwaarde = date("Y-m-d", strtotime($veldwaarde));
		$veldwaarde2 = date("j F Y", strtotime($veldwaarde));

		$inputfield = '';
		if ( $this->m_readonly == 1) {
			$inputfield .= "<input name=\"FORM_::FIELDNAME::\" id=\"FORM_::FIELDNAME::\" type=\"hidden\" value=\"::VALUE::\">::VALUE2::\n";
		} else {
			$inputfield .= "<input name=\"FORM_::FIELDNAME::\" id=\"FORM_::FIELDNAME::\" type=\"text\" value=\"::VALUE::\" size=\"::SIZE::\" ::CLASS::>\n";
		}

		$inputfield = str_replace("::SIZE::", $this->m_size, $inputfield);

		$inputfield = str_replace("::FIELDNAME::", $this->get_fieldname(), $inputfield);
		$inputfield = str_replace("::VALUE::", $veldwaarde, $inputfield);
		$inputfield = str_replace("::VALUE2::", $veldwaarde2, $inputfield);

		return $inputfield;
	}

	function form_row($row, $row_design, $m_form, $required_typecheck_result = 0) {
		// place input field in row template
		$field = $this->form_field($row, $m_form, $required_typecheck_result);
		$row_design = str_replace("::FIELD::", $field, $row_design);

		// place fieldname in row template
		$row_design = str_replace("::LABEL::", $this->get_fieldlabel(), $row_design);

		// place if necessary required sign in row template
		$row_design = str_replace("::REQUIRED::", $this->get_required_sign(), $row_design);

		return $row_design;
	}
}
