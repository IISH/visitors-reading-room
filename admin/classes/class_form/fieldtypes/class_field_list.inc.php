<?php 
require_once("./classes/class_form/fieldtypes/class_field.inc.php");

class class_field_list extends class_field {
	protected $oDb;

	private $m_query;
	private $m_id_field;
	private $m_description_field;
	private $m_empty_value;
	private $m_show_empty_row;
	private $m_onchange;
	private $m_javascriptcode;

	function class_field_list($fieldsettings) {
		global $databases;

		parent::class_field($fieldsettings);

		$this->m_query = '';
		$this->m_id_field = '';
		$this->m_description_field = '';
		$this->m_empty_value = '';
		$this->m_show_empty_row = "0";
		$this->m_onchange = '';
		$this->m_javascriptcode = '';
		$this->m_dbhandle = null;

		$this->oDb = new class_mysql($databases['default']);

		if ( is_array( $fieldsettings ) ) {
			foreach ( $fieldsettings as $field => $value ) {
				switch ($field) {
					// only list specific parameters

					case "query":
						$this->m_query = $fieldsettings["query"];
						break;

					case "id_field":
						$this->m_id_field = $fieldsettings["id_field"];
						break;

					case "description_field":
						$this->m_description_field = $fieldsettings["description_field"];
						break;

					case "empty_value":
						$this->m_empty_value = $fieldsettings["empty_value"];
						break;

					case "show_empty_row":
						$this->m_show_empty_row = $fieldsettings["show_empty_row"];
						break;

					case "onchange":
						$this->m_onchange = $fieldsettings["onchange"];
						break;

					case "javascriptcode":
						$this->m_javascriptcode = $fieldsettings["javascriptcode"];
						break;

					case "dbhandle":
						$this->m_dbhandle = $fieldsettings["dbhandle"];
						break;
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

		$inputfield = "::JAVASCRIPTCODE::<select name=\"FORM_::FIELDNAME::\" id=\"FORM_::FIELDNAME::\" ::ONCHANGE::>\n";

		if ( $this->m_onchange != '' ) {
			$inputfield = str_replace("::ONCHANGE::", "onchange=\"" . $this->m_onchange . "\"", $inputfield);
		} else {
			$inputfield = str_replace("::ONCHANGE::", '', $inputfield);
		}

		if ( $this->m_javascriptcode != '' ) {
			$inputfield = str_replace("::JAVASCRIPTCODE::", "\n<script type=\"text/javascript\">\n<!--\n" . $this->m_javascriptcode . "\n//-->\n</script>\n", $inputfield);
		} else {
			$inputfield = str_replace("::JAVASCRIPTCODE::", '', $inputfield);
		}

		// connect to server
		$this->oDb->connect();

		// execute query
		$veldwaarde_currentvalue = $veldwaarde;
		if ( $veldwaarde_currentvalue == '' ) {
			$veldwaarde_currentvalue = 0;
		}
		$this->m_query = str_replace('[CURRENTVALUE]', $veldwaarde_currentvalue, $this->m_query);

		if ( $this->m_dbhandle == null ) {
			// TODOTODO
			$res2 = mysql_query(Misc::PlaceURLParametersInQuery($this->m_query), $this->oDb->getConnection()) or die(mysql_error());
		} else {
			// TODOTODO
			$res2 = mysql_query(Misc::PlaceURLParametersInQuery($this->m_query), $this->m_dbhandle) or die(mysql_error());
		}

		$selectedOption = (string)$row[$this->get_fieldname()];

		// required, no? add empty option
		if ( $this->is_field_required() == false || $this->m_show_empty_row === true ) {
			$inputfield .= "\t<option value=\"" . $this->m_empty_value . "\"></option>\n";
		}

		// TODOTODO
		while( $row2 = mysql_fetch_assoc($res2) ){

			$optionvalue = $row2[$this->m_id_field];
			$inputfield .= "\t<option value=\"" . $optionvalue . "\"";

			if ( $optionvalue == $veldwaarde ) {
				$inputfield .= " SELECTED";
			}
			$inputfield .= ">" . stripslashes(trim($row2[$this->m_description_field])) . "</option>\n";
		}

		$inputfield .= "</select>\n";

		$inputfield = str_replace("::FIELDNAME::", $this->get_fieldname(), $inputfield);
		$inputfield = str_replace("::VALUE::", $veldwaarde, $inputfield);

		// TODOTODO
		mysql_free_result($res2);

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

	function is_field_value_correct($veldwaarde = "") {
		$retval = 1; // default = okay

		// is select list value ZERO allowed???
		if ( $this->is_field_required() == 1 ) {
			if ( $veldwaarde == 0 ) {
				$retval = 0;
			}
		}

		return $retval;
	}
}
