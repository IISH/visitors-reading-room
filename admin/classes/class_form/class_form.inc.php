<?php 
class class_form {
	private $m_form;
	protected $settings;
	private $m_array_of_fields = array();

	protected $oDb;
	protected $oClassFile;

	private $m_errors = array();
	protected $m_doc_id;
	private $m_old_doc_id;

	function class_form($settings, $oDb) {
		$this->settings = $settings;
		$this->oDb = $oDb;
		$this->oClassFile = new class_file();
	}

	// function calculate_document_id
	function calculate_document_id() {
		// document id
		$this->m_doc_id = ( isset($_GET[$this->m_form["primarykey"]]) ? $_GET[$this->m_form["primarykey"]] : '' );
		if ( $this->m_doc_id == '' ) {
			$this->m_doc_id = "0";
		}

		// remember the current document id
		$this->m_old_doc_id = $this->m_doc_id;

		return true;
	}

	function form_check_required_and_fieldtype() {
		$result = 1;

		// loop velden
		foreach ($this->m_array_of_fields as $one_field_in_array_of_fields) {
			$veldwaarde = $one_field_in_array_of_fields->get_form_value();

			// if required, then test if not empty
			if ( $one_field_in_array_of_fields->is_field_required() == 1 ) {

				if ( $veldwaarde == '' ) {
					// it's empty
					$result = 0;
					array_push($this->m_errors, "Field '" . $one_field_in_array_of_fields->get_fieldlabel() . "' is required.");
				}
			}

			if ( $veldwaarde <> "" ) {
				switch ( $one_field_in_array_of_fields->is_field_value_correct($veldwaarde) ) {
					case 0:
						$result = 0;
						array_push($this->m_errors, "Field '" . $one_field_in_array_of_fields->get_fieldlabel() . "' is not correct.");
						break;
					case -1:
						$result = 0;
						$minValue = $one_field_in_array_of_fields->getMinValue();
						$maxValue = $one_field_in_array_of_fields->getMaxValue();
						$message = "Field '" . $one_field_in_array_of_fields->get_fieldlabel() . "' is out of range (";
						if ( $minValue != null ) {
							$message .= "min.value: " . $minValue;
						}
						if ( $maxValue != null ) {
							if ( $minValue != null ) {
								$message .= ", ";
							}
							$message .= "max.value: " . $maxValue;
						}
						$message .= ")";
						array_push($this->m_errors, $message);
						break;
				}
			}

		}

		return $result;
	}

	//
	function form_save() {
		$result = 1; // default = okay
		$query_fields = array();

		// loop velden
		foreach ($this->m_array_of_fields as $one_field_in_array_of_fields) {
			$query_fields = $one_field_in_array_of_fields->push_field_into_query_array($query_fields);
		}

		$extra_query_fields = $this->MakeExtraQueryChanges();

		if ( $this->m_doc_id == "0" ) {
			$query = $this->MakeQuery($query_fields, "insert", '', $extra_query_fields);
		} else {
			$query_where = " WHERE " . $this->m_form["primarykey"] . "=" . $_GET[$this->m_form["primarykey"]];
			$query = $this->MakeQuery($query_fields, "update", $query_where, $extra_query_fields);
		}

		// execute query
		$res = mysql_query($query, $this->oDb->getConnection()) or die(mysql_error());

		// if current id = 0
		// get the last id
		if ( $this->m_doc_id == "0" || $this->m_doc_id == '' ) {
			// tabelnaam moet als variable
			$this->m_doc_id = $this->timecard_mysql_insert_id($this->m_form["table"]);
		}

		// 
		$this->postSave();

		return $result;
	}

	function get_document_id($table) {
		// if current id = 0
		// get the last id
		if ( $this->m_doc_id == "0" || $this->m_doc_id == '' ) {
			$this->m_doc_id = $this->timecard_mysql_insert_id($table);
		}

		return $this->m_doc_id;
	}

	function timecard_mysql_insert_id($table) {
		$retval = '0';

		$query = "SELECT ID FROM $table ORDER BY ID DESC LIMIT 0, 1 ";
		$result = mysql_query($query, $this->oDb->getConnection());
		if ( $row = mysql_fetch_assoc($result) ) {
			$retval = $row["ID"];
		}
		mysql_free_result($result);

		return $retval;
	}

	function MakeQuery($query_fields, $insert_or_update, $query_where, $extra_query_fields) {
		$query = '';

		if ( $insert_or_update == "insert" ) {
			$query = "INSERT INTO " . $this->m_form["table"];
			$fields = '';
			$values = '';
			$separator = '';

			if ( is_array($query_fields) ) {
				foreach ($query_fields as $one_item_from_array) {
					foreach ( $one_item_from_array as $fieldname => $fieldvalue ) {
						if ( $fieldname != 'ID' ) {
							$fields .= $separator . $fieldname;
							$values .= $separator . $fieldvalue;
							$separator = ", ";
						}
					}
				}
			}

			if ( is_array($extra_query_fields) ) {
				foreach ($extra_query_fields as $one_item_from_array) {
					foreach ( $one_item_from_array as $fieldname => $fieldvalue ) {
						$fields .= $separator . $fieldname;
						$values .= $separator . $fieldvalue;
						$separator = ", ";
					}
				}
			}

			// DATE ADDED
			$fields .= $separator . 'date_added';
			$values .= $separator . "'" . date("Y-m-d H:i:s") . "'";

			// REVISION
			$fields .= $separator . 'revision';
			$values .= $separator . "'" . date("Y-m-d H:i:s") . "'";

			// MODIFIED BY
			$fields .= $separator . 'modified_by';
			$values .= $separator . "'" . Misc::getCurrentlyLoggedInUser() . "'";

			// IP ADDRESS
			$fields .= $separator . 'ip_address';
			$values .= $separator . "'" . Misc::getIpAddress() . "'";

			//
			$query .= " (" . $fields . ") VALUES (" . $values . ") ";
		} elseif ( $insert_or_update == "update" ) {
			$query = "UPDATE " . $this->m_form["table"] . " SET ";

			$separator = '';

			if ( is_array($query_fields) ) {
				foreach ($query_fields as $one_item_from_array) {
					foreach ( $one_item_from_array as $fieldname => $fieldvalue ) {

						if ( $fieldname != 'ID' ) {
							$query .= $separator . $fieldname . "=" . $fieldvalue;
							$separator = ", ";
						}

					}
				}
			}

			if ( is_array($extra_query_fields) ) {
				foreach ($extra_query_fields as $one_item_from_array) {
					foreach ( $one_item_from_array as $fieldname => $fieldvalue ) {
						$query .= $separator . $fieldname . "=" . $fieldvalue;
						$separator = ", ";
					}
				}
			}

			// REVISION
			$query .= $separator . "revision='" . date("Y-m-d H:i:s") . "'";

			// MODIFIED BY
			$query .= $separator . "modified_by='" . Misc::getCurrentlyLoggedInUser() . "'";

			// IP ADDRESS
			$query .= $separator . "ip_address='" . Misc::getIpAddress() . "'";

			// where (only for update)
			if ( $insert_or_update == "update" ) {
				if ( $query_where == '' ) {
					$query .= " WHERE 1=0 "; // don't execute query, because every record in table would be changed
				} else {
					$query .= $query_where;
				}
			}
		}

//die( $query );
		return $query;
	}

	function MakeExtraQueryChanges() {
		$extraqueryfields = array();

		return $extraqueryfields;
	}

	// generate_form
	function generate_form() {
		global $protect;

		// document id
		$this->calculate_document_id();

		$return_value = '';
		$required_typecheck_result = -1;	// default, nog nix geprobeerd te bewaren
								// -1 nix, 0 errors bij bewaren, 1 bewaren okay

		// connect to server
		$this->oDb->connect();

		// if form submitted try to save document
		if ( isset($_POST["issubmitted"]) && $_POST["issubmitted"] == "1" ) {
			// check first if all required fields are filled in
			// and also check if the values are of the correct type
			$required_typecheck_result = $this->form_check_required_and_fieldtype();

			// if everything correct, try to save the document
			if ( $required_typecheck_result == 1 ) {
				$saveresult = $this->form_save();
			}
		}

		// als form is gesubmit
		// en als save resultaat okay is
		// en als button close is aangeklikt
		// ga dan naar backurl
		if ( isset($_POST["issubmitted"]) && $_POST["issubmitted"] == "1" ) {

			if ( $required_typecheck_result == 1 ) {

				if ( isset($_POST["pressedbutton"]) && $_POST["pressedbutton"] == "saveclose" ) {

					$this->postSave();

					$backurl = Misc::getBackUrl();

					if ( strpos($backurl, "#") === false ) {
						if ( $this->m_doc_id <> "0" ) {
							$backurl .= "#" . $this->m_doc_id;
						}
					}

					header("Location: " . $backurl);
				} elseif ( isset($_POST["pressedbutton"]) && $_POST["pressedbutton"] == "delete" ) {

					$backurl = Misc::getBackUrl();

					// verwijder anchor
					$backurl = str_replace("#" . $this->m_doc_id, '', $backurl);
					header("Location: " . $backurl);

				}
			}
		}

		// default template for form
		$template_tr = "
<tr>
	<TD valign=\"top\"><span class=\"form_field_label\">::LABEL:: </span><span class=\"errormessage\">::REQUIRED::</span>&nbsp;</td>
	<td>::FIELD::</td>
</tr>
";
		$template_form = "
<form name=\"frmBsrs\"  id=\"frmBsrs\" action=\"::ACTION::\" method=\"POST\" onchange=\"setIsChanged();\">
<input type=\"hidden\" name=\"issubmitted\"  id=\"issubmitted\" value=\"1\">
<input type=\"hidden\" name=\"pressedbutton\" id=\"pressedbutton\" value=\"\">

::FORMFIELDS::

</form>
";
		$template_error_message = '<span class="errormessage">::ERROR::</span><br>';

		// show errors
		if ( count($this->m_errors) > 0 ) {
			foreach ($this->m_errors as $errormessage) {
				$return_value .= str_replace("::ERROR::", $errormessage, $template_error_message);
			}
			$return_value .= "<br>";
		}

		if ( $protect->requestDigits('get', "ID") == '' ) {
			$this->m_form["query"] = str_replace("[FLD:ID]", "0", $this->m_form["query"]);
		}

		// plaats url parameters in query
		$this->m_form["query"] = Misc::PlaceURLParametersInQuery($this->m_form["query"]);

		// execute query
		$res = mysql_query($this->m_form["query"], $this->oDb->getConnection()) or die(mysql_error());

		if ($res){

			$form_template = "::CONTENT::";

// + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + +

			// start table
			$all_fields = "<table class=\"admin\">";

			// get submit buttons
			$submitbuttons = $this->get_form_edit_buttons();

			$total_row = '';

			$row = mysql_fetch_assoc($res);

			foreach ($this->m_array_of_fields as $one_field_in_array_of_fields) {

				// get row template (label + input field)
				$tmp_data = $template_tr;

				$tmp_data = $one_field_in_array_of_fields->form_row($row, $tmp_data, $this->m_form, $required_typecheck_result);

				$total_row .= $tmp_data . "\n";
			}

			// voeg alle rijen toe aan tabel
			$all_fields .= $total_row;

			// add submit buttons to view (and extra empty row)
			$all_fields .= "<tr><td>&nbsp;</td></tr>" . $submitbuttons;

			// end table
			$all_fields .= "</table>";

// + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + + +

			$return_value .= str_replace("::CONTENT::", $all_fields, $form_template);

		}

		// free result set
		mysql_free_result($res);

		// get form_start
		$form_start = $template_form;
		$form_action = $_SERVER["SCRIPT_NAME"];
		$form_query_string = $_SERVER["QUERY_STRING"];
		if ( $form_query_string <> "" ) {
			if ( $this->m_doc_id <> $this->m_old_doc_id ) {
				$form_query_string = str_replace($this->m_form["primarykey"] . "=0", $this->m_form["primarykey"] . "=" . $this->m_doc_id, $form_query_string);

				$backurl = Misc::getBackUrl();

				if ( strpos($backurl, "#") === false ) {
					if ( $this->m_doc_id <> "" ) {
						$form_query_string .= "%23" . $this->m_doc_id;
					}
				}
			}
			$form_action .= "?" . $form_query_string;
		}
		$form_start = str_replace("::ACTION::", $form_action, $form_start);

		$return_value = str_replace("::FORMFIELDS::", $return_value, $form_start);

		// return result
		return $return_value;
	}

	function get_form_edit_buttons() {
		// place submit buttons
		$submitbuttons = "
<tr>
	<td colspan=\"2\" align=\"center\">

		<!-- cancelbutton -->
		<a href=\"::CANCELURL::\" class=\"button\">Cancel</a>
		&nbsp; &nbsp; &nbsp; &nbsp;
		<!-- /cancelbutton -->

		<!-- deletebutton -->
		<input type=\"button\" class=\"button\" name=\"deleteButton\" id=\"deleteButton\" value=\"Delete\" onClick=\"doc_delete('delete');\">
		&nbsp; &nbsp; &nbsp; &nbsp;
		<!-- /deletebutton -->

		<!-- savebutton -->
		<input type=\"button\" class=\"button\" name=\"saveButtonGoBack\" id=\"saveButtonGoBack\" value=\"Save\" onClick=\"doc_submit('saveclose');\">
		<!-- /savebutton -->

	</td>
</tr>
";
		if ( !isset($this->m_form["disallow_delete"]) ) {
			$this->m_form["disallow_delete"] = false;
		}

		if ( $this->m_doc_id == "0" || $this->m_form["disallow_delete"] === true ) {
			$searchstr = '@<!-- ' . 'deletebutton' . ' -->.*?<!-- /' . 'deletebutton' . ' -->@si';
			$submitbuttons = preg_replace ($searchstr, '', $submitbuttons);
		}

		$cancelurl = Misc::getBackUrl();

		if ( strpos ($cancelurl, "#") === false ) {
			if ( $this->m_doc_id <> "0" ) {
				$cancelurl .= "#" . $this->m_doc_id;
			}
		}
		$submitbuttons = str_replace("::CANCELURL::", $cancelurl, $submitbuttons);

		return $submitbuttons;
	}

	// set_form
	function set_form($aView) {
		$this->m_form = $aView;

		return 1;
	}

	// add_field
	function add_field($aField) {
		array_push($this->m_array_of_fields, $aField);
		return 1;
	}

	function postSave() {
		if ( $_GET[$this->m_form["primarykey"]] == "0" ) {
			if ( $this->m_doc_id != 0 ) {

				$url = $_SERVER["SCRIPT_NAME"] . "?" . $_SERVER["QUERY_STRING"];

				// vervang id 0 door nieuwe id
				$url = str_replace("?" . $this->m_form["primarykey"] . "=" . $_GET[$this->m_form["primarykey"]], "?" . $this->m_form["primarykey"] . "=" . $this->m_doc_id, $url);
				$url = str_replace("&" . $this->m_form["primarykey"] . "=" . $_GET[$this->m_form["primarykey"]], "?" . $this->m_form["primarykey"] . "=" . $this->m_doc_id, $url);

				header("Location: " . $url);
			}
		}

		return true;
	}
}
