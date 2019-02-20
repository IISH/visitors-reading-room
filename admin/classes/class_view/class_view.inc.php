<?php 
class class_view {
	protected $oDb;
	protected $oClassFile;
	private $settings;

	private $m_view;
	private $m_array_of_fields = Array();

	private $m_order_by;
	private $m_group_by;

	function class_view($settings, $oDb) {
		$this->settings = $settings;

		$this->oDb = $oDb;
		$this->oClassFile = new class_file();
	}

	function calculate_group_by() {
		$by = ( isset($_GET["group_by"]) ? $_GET["group_by"] : '' );
		if ( $by == '' ) {
			$by = ( isset($this->m_view["group_by"]) ? $this->m_view["group_by"] : '');
		}

		$by = str_replace("%20", " ", $by);

		$this->m_group_by = $by;

		return 1;
	}

	function calculate_order_by() {
		$order_by = ( isset($_GET["order_by"]) ? $_GET["order_by"] : '' );
		if ( $order_by == '' ) {
			$order_by = ( isset($this->m_view["order_by"]) ? $this->m_view["order_by"] : '');
		}

		$order_by = str_replace("%20", " ", $order_by);

		$this->m_order_by = $order_by;

		return 1;
	}

	function add_viewfilters_to_query($query) {
		$filter = '';

		foreach ($this->m_array_of_fields as $one_field_in_array_of_fields) {

			if ( $one_field_in_array_of_fields->m_viewfilter ) {

				foreach ( $one_field_in_array_of_fields->m_viewfilter["filter"] as $field => $value) {

					$fieldname = ( isset($value["fieldname"]) ? $value["fieldname"] : '' );
					$type = ( isset($value["type"]) ? $value["type"] : '' );
					$search_in = ( isset($value["search_in"]) ? $value["search_in"] : '' );

					$tmp_filter = $this->CreateSpecialCriterium($fieldname, $type, $search_in);

					if ( $tmp_filter <> "" ) {
						$filter .= " AND " . $tmp_filter . " ";
					}
				}
			}
		}

		$query .= $filter;

		return $query;
	}

	function CreateSpecialCriterium($field, $type, $search_in) {
		$retval = '';
		$separatorAND = '';
		$separatorOR = '';

		if ( !isset($_GET["vf_" . $field]) ) {
			$_GET["vf_" . $field] = '';
		}

		$value = trim($_GET["vf_" . $field]);

		if ( $value <> "" ) {
			$fields = explode(";", $search_in);

			$values = explode(" ", $value);

			foreach ( $values as $values_field => $values_value) {
				$separatorOR = '';
				$retval .= $separatorAND . " ( ";

				foreach ( $fields as $fields_field => $fields_value) {
					if ( $values_value == "-NULL-" ) {
						// indien men zoekt op -NULL- moet gecontroleerd worden of veld leeg is (leeg of null)
						// is eigenlijk gemaakt voor een select/list opdracht General/Special of leeg
						$retval .= $separatorOR . " ( " . $fields_value . " = '' OR " . $fields_value . " IS NULL ) ";
					} else {
						$retval .= $separatorOR . $fields_value . " LIKE '";

						if ( $type == 'select' ) {
							$retval .= $values_value;
						} else { // string
							$retval .= "%" . $values_value . "%";
						}

						$retval .= "' ";
					}
					$separatorOR = " OR ";
				}

				$retval .= " ) ";
				$separatorAND = " AND ";
			}
		}

		return $retval;
	}

	function generate_view_header() {
		$return_value = '';
		$total_header = '';
		$tmp_header = '';

		$row_template = "<tr>::TR::</tr>";
		$header_template = "
<TH align=\"left\" valign=\"top\"><a class=\"nolink\">::TH::</a>::FILTER::</TH>
";

		// show or not show counter
		if ( isset($this->m_view["show_counter"]) && $this->m_view["show_counter"] == true ) {
			$total_header .= "<TH>&nbsp;</TH>";
		}

		// loop and show all fields
		foreach ($this->m_array_of_fields as $one_field_in_array_of_fields) {

//			if ( $_POST["form_fld_pressed_button"] != '-delete-' ) {

				$tmp_header = $header_template;

				// plaats label en buttons in header
				$tmp_header = str_replace("::TH::", $one_field_in_array_of_fields->get_fieldlabel(), $tmp_header);

				//
				if ( is_array( $one_field_in_array_of_fields->m_viewfilter ) ) {
					$filter = '<br>';

					foreach ( $one_field_in_array_of_fields->m_viewfilter["filter"] as $filterfield => $filtervalue ) {
						$filter .= $this->CreateViewFilterInputField($filtervalue);
					}

					$tmp_header = str_replace("::FILTER::", $filter, $tmp_header);
				} else {
					$tmp_header = str_replace("::FILTER::", '', $tmp_header);
				}

				$total_header .= $tmp_header;
//			}
		}

		$return_value = str_replace("::TR::", $total_header, $row_template);

		if ( isset($this->m_view["viewfilter"]) && $this->m_view["viewfilter"] === true ) {

			$viewfilter = "
<script LANGUAGE=JavaScript>
<!--
document.onkeydown = onKeyDown;
document.onkeyup = onKeyUp;

var anyChanges = 0;

function onKeyUp(e) {
	var code = (window.event) ? event.keyCode : e.keyCode;

	if (code != 13 && code != 37 && code != 38	&& code != 39 && code != 40) {
		anyChanges = 1;
	}
}

function onSelectChange() {
	anyChanges = 1;
}

function onKeyDown(e) {
	var code = (window.event) ? event.keyCode : e.keyCode;
	if (code == 13) {
		if (anyChanges == 1) {
			//document.filterform.submit();
			document.frmBsrs.submit();
		}
	}
}
// -->
</script>

<form name=\"frmBsrs\" id=\"frmBsrs\" type=\"get\">
<xxxinput type=\"text\" name=\"filter\" id=\"filter\" value=\"::FILTER::\">

::HIDDENFIELDS::

::HEADER::

</form>
";

			//
			$viewfilter = str_replace("::HIDDENFIELDS::", $this->createHiddenFields(), $viewfilter);

			//
			$return_value = str_replace("::HEADER::", $return_value, $viewfilter);
		}

		return $return_value;
	}

	function createHiddenFields() {
		$ret = '';

		if ( isset($this->m_view["hidden_fields"]) ) {
			foreach ( $this->m_view["hidden_fields"] as $fields) {
				foreach ( $fields as $field => $value) {
					$ret .= '<input type="hidden" name="' . $field . '" value="' . htmlentities($value) . '">';
				}
			}
		}

		return $ret;
	}

	function CreateViewFilterInputField($field) {
		$retval = '';

		$label = ( isset($field["fieldlabel"]) ? $field["fieldlabel"] : '' );
		$name = ( isset($field["fieldname"]) ? $field["fieldname"] : '' );

		$class = ( isset($field["class"]) ? $field["class"] : '' );

		if ( !isset($_GET["vf_" . $name]) ) {
			$_GET["vf_" . $name] = '';
		}

		$value = ( isset( $_GET["vf_" . $name] ) ? $_GET["vf_" . $name] : '' );
		$value = str_replace("\\\"", "&quot;", $value);
		$value = str_replace("\'", "'", $value);

		if ( !isset($field["type"]) || $field["type"] == '' || $field["type"] == 'string' ) {

			$retval .= "::LABEL:: <input type=\"text\" name=\"vf_::NAME::\" id=\"vf_::NAME::\" value=\"::VALUE::\" class=\"::CLASS::\">\n";
			$retval = str_replace("::VALUE::", $value, $retval);

		} elseif ( $field["type"] == 'select' ) {
			$retval .= "::LABEL:: <SELECT name=\"vf_::NAME::\" onchange=\"onSelectChange();\" class=\"::CLASS::\">\n\t<OPTION value=\"\"></OPTION>\n::OPTIONS::\n</SELECT>\n";

			$options = '';
			if ( isset($field["choices"]) ) {
				if ( is_array($field["choices"]) ) {
					foreach ( $field["choices"] as $choice) {
						$tmpOption = "\t<OPTION value=\"" . $choice[0] . "\" ::SELECTED::>" . $choice[1] . "</OPTION>\n";

						// 'SELECT' de gekozen optie
						if ( $choice[0] == $value ) {
							$tmpOption = str_replace("::SELECTED::", "SELECTED", $tmpOption);
						} else {
							$tmpOption = str_replace("::SELECTED::", '', $tmpOption);
						}

						$options .= $tmpOption;
					}
				}
			}

			$retval = str_replace("::OPTIONS::", $options, $retval);
		}

		$retval = str_replace("::CLASS::", $class, $retval);
		$retval = str_replace("::LABEL::", $label, $retval);
		$retval = str_replace("::NAME::", $name, $retval);

		return trim($retval);
	}

	// generate_view
	function generate_view() {
		$return_value = '';

		// connect to server
		$this->oDb->connect();

		// default template for td
		$template_td = "<TD class=\"recorditem\">::TD::&nbsp;</td>\n";

		// place querystring parameters in query
		$this->m_view["query"] = Misc::PlaceURLParametersInQuery($this->m_view["query"]);

		// add viewfilters to query
		$this->m_view["query"] = $this->add_viewfilters_to_query($this->m_view["query"]);

		// calculate order by
		$this->calculate_group_by();
		if ( $this->m_group_by <> "" ) {
			$this->m_view["query"] .= " GROUP BY " . $this->m_group_by;
		}

		// calculate order by
		$this->calculate_order_by();
		if ( $this->m_order_by <> "" ) {
			$this->m_view["query"] .= " ORDER BY " . $this->m_order_by;
		}

		// execute query
		$res = mysql_query($this->m_view["query"], $this->oDb->getConnection()) or die( "error 8712378" . "<br>" . mysql_error() . "<br>" . $this->m_view["query"]);

		// get submit buttons (add new / go back)
		// show buttons at top
		$return_value .= $this->get_add_new_button();

		//
		if($res){

			// show calculate_total
			if ( isset($this->m_view["calculate_total"]) && is_array($this->m_view["calculate_total"]) ) {
				$calculate_total[$this->m_view["calculate_total"]["field"]] = 0;
			}

			$return_value .= "<table";

			// extra tabel parameters
			if ( isset($this->m_view["table_class"]) && $this->m_view["table_class"] != '' ) {
				$return_value .= " class=\"" . $this->m_view["table_class"] . "\" ";
			}

			// sluit tabel
			$return_value .= ">";

			$row_template = "<tr>::TR::</tr>";
			$total_row = '';

			// add header row
			$return_value .= $this->generate_view_header();

			// doorloop gevonden recordset
			$counter = 0;
			while($row = mysql_fetch_assoc($res)){
				$counter++;
				$total_data = '';
				$anchor = '';

				if ( $this->m_view["anchor_field"] <> "" ) {
					$anchor = "<A NAME=\"::ANCHOR::\"></A>";
					$anchor = str_replace("::ANCHOR::", $row[$this->m_view["anchor_field"]], $anchor);
				}

				if ( isset($this->m_view["show_counter"]) && $this->m_view["show_counter"] == true ) {
					$tmp_data = $template_td;
					$tmp_data = str_replace("::TD::", $counter . '.', $tmp_data);
					$total_data .= $tmp_data;
				}

				foreach ($this->m_array_of_fields as $one_field_in_array_of_fields) {

						$tmp_data = $template_td;

						// get veld waarde
						$veldwaarde = $one_field_in_array_of_fields->view_field($row);
						$dbwaarde = $one_field_in_array_of_fields->get_value($row);
						// add calculate_total
						if ( isset($this->m_view["calculate_total"]) && is_array($this->m_view["calculate_total"]) ) {
							if ( strtolower( $one_field_in_array_of_fields->get_fieldname() ) == strtolower($this->m_view["calculate_total"]["field"]) ) {
								$calculate_total[$this->m_view["calculate_total"]["field"]] += $dbwaarde;
							}
						}

						// plaats veldwaarde in tabel cell
						$tmp_data = str_replace("::TD::", $anchor . $veldwaarde, $tmp_data);
						$anchor = '';
						$total_data .= $tmp_data;

				}

				// plaats alle cellen in row template
				$total_row .= str_replace("::TR::", $total_data, $row_template);
			}
			// voeg alle rijen toe aan tabel
			$return_value .= $total_row;

			// show calculate_total
			if ( isset($this->m_view["calculate_total"]) && is_array($this->m_view["calculate_total"]) ) {
				$return_value .= "<tr><td colspan=\"" . $this->m_view["calculate_total"]["nrofcols"] . "\"><hr></td></tr>";
				$return_value .= "<tr><td colspan=\"" . ($this->m_view["calculate_total"]["totalcol"]-1) . "\"><b>Total:</b></td>";

				$t = $calculate_total[$this->m_view["calculate_total"]["field"]];
				if ( isset ( $this->m_view["calculate_total"]["type"] ) && $this->m_view["calculate_total"]["type"] == 'integer' ) {
					$t = $t;
				} elseif (  isset ( $this->m_view["calculate_total"]["type"] ) && $this->m_view["calculate_total"]["type"] == 'integer_thousand_separator' ) {
					$t = number_format ($t, 0, ',', '.');
				} else {
//					$t = class_datetime::ConvertTimeInMinutesToTimeInHoursAndMinutes($t);
					$t = $t;
				}
				$return_value .= "<td><b>" . $t . "</b>&nbsp;&nbsp;</td>";

				$return_value .= "</tr>";
			}

			// end table
			$return_value .= "</table>";
		}

		// free result set
		mysql_free_result($res);

		// return result
		return $return_value;
	}

	// set_view
	function set_view($aView) {
		$this->m_view = $aView;

		return 1;
	}

	// add_field
	function add_field($aField) {
		array_push($this->m_array_of_fields, $aField);
		return 1;
	}

	function get_add_new_button() {
		$url = '';
		$label = '';

		if ( isset($this->m_view["add_new_button"]) ) {
			$url = $this->m_view["add_new_button"]['url'];
			$label = $this->m_view["add_new_button"]['label'];
		}

		if ( $url == '' ) {
			return '';
		}

		if ( $label == '' ) {
			$label = 'Add new';
		}

		// place submit buttons
		$addButton = "<p style=\"line-height:20px\"><a href=\"::URL::\" class=\"button\">::LABEL::</a></p>";

		$url = str_replace("\n", '', $url);
		$url = str_replace("\t", '', $url);
		$url = str_replace("\r", '', $url);

		$url = Misc::ReplaceSpecialFieldsWithQuerystringValues($url);

		// create add new button
		$addButton = str_replace("::URL::", $url, $addButton);
		$addButton = str_replace("::LABEL::", $label, $addButton);

		return $addButton;
	}
}
