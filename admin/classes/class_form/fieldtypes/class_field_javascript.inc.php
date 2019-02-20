<?php 
require_once("./classes/class_form/fieldtypes/class_field.inc.php");

class class_field_javascript extends class_field {
	protected $m_code = '';

	function class_field_javascript($fieldsettings) {
		parent::class_field($fieldsettings);

		$this->m_url = '';

		if ( is_array( $fieldsettings ) ) {
			foreach ( $fieldsettings as $field => $value ) {
				switch ($field) {
					// 

					case "code":
						$this->m_code = $fieldsettings["code"];
						break;

				}
			}
		}
	}

	function form_field($row, $m_form, $required_typecheck_result = 0 ) {
//		$veldwaarde = $row[$this->get_fieldname()];

//		$onNewValue = $this->get_onNew($m_form["primarykey"]);
//		if ( $onNewValue != "" ) {
//			$veldwaarde = $onNewValue;
//		}

//		// strip slashes
//		$veldwaarde = stripslashes($veldwaarde);
//		$veldwaarde = str_replace("\"", "&quot;", $veldwaarde);

		// input field format
//		$inputfield = "<a href=\"::URL::\" target=\"::TARGET::\">::LABEL::</a>";

		$inputfield = $this->m_code;
//		$inputfield = str_replace("::TARGET::", $this->m_target, $inputfield);
//		$inputfield = str_replace("::VALUE::", $veldwaarde, $inputfield);
//		$inputfield = str_replace("::LABEL::", $this->m_url, $inputfield);

		return $inputfield;
	}

	function form_row($row, $row_design, $m_form, $required_typecheck_result = 0) {
		// place input field in row template
		$row_design = str_replace("::FIELD::", $this->form_field($row, $m_form, $required_typecheck_result), $row_design);

		// place fieldname in row template
		$row_design = str_replace("::LABEL::", $this->get_fieldlabel(), $row_design);

		// place if necessary required sign in row template
		$row_design = str_replace("::REQUIRED::", $this->get_required_sign(), $row_design);

		return $row_design;
	}

	function push_field_into_query_array($query_fields) {
		return $query_fields;
	}
}
