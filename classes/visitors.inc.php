<?php

class Visitors {
	public static function getVisitorById( $id, $year = '' ) {

	}

	public static function getVisitorByEmail( $email, $year = '' ) {
	}

	public static function getVisitorIdByEmail( $email ) {
		global $databases;

		$ret = 0;

		$data['email'] = $email;

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		//
		$query = "SELECT * FROM visitors WHERE visitors.email = '{email}' ";
		$query = Misc::fillQuery($query, $data);
		$result = mysql_query($query, $oConn->getConnection());
		if ( mysql_num_rows($result) > 0 ) {
			if ($row = mysql_fetch_assoc($result)) {
				$ret = $row['id'];
			}
		}
		mysql_free_result($result);

		return $ret;
	}

	public static function isEmailAlreadyUsedByAnotherVisitor( $email, $id = '' ) {
		global $databases;

		$ret = 0;

		if ( $id == '' ) {
			$id = 0;
		}

		$data['email'] = $email;
		$data['id'] = $id;

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		//
		$query = "SELECT * FROM visitors WHERE email = '{email}' AND id <> {id} ";
		$query = Misc::fillQuery($query, $data);

		$result = mysql_query($query, $oConn->getConnection());
		if ( mysql_num_rows($result) > 0 ) {
			$ret = 1;
		}
		mysql_free_result($result);

		return $ret;
	}

	public static function lastPreviousYearEmailUsed( $email ) {
		global $databases;

		$ret = 0;

		$data['email'] = $email;
		$data['year'] = date("Y");

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		//
		$query = "SELECT visitor_misc.year FROM visitors INNER JOIN visitor_misc ON visitors.id = visitor_misc.visitor_id WHERE visitors.email = '{email}' AND visitor_misc.year < {year} ";
		$query = Misc::fillQuery($query, $data);
		$result = mysql_query($query, $oConn->getConnection());
		if ( mysql_num_rows($result) > 0 ) {
			if ($row = mysql_fetch_assoc($result)) {
				$ret = $row['year'];
			}
		}
		mysql_free_result($result);

		return $ret;
	}

	public static function yearEmailAddressIsLastUsed( $email ) {
		global $databases;

		$ret = 0;

		$data['email'] = $email;

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		//
		$query = "SELECT visitor_misc.year FROM visitors INNER JOIN visitor_misc ON visitors.id = visitor_misc.visitor_id WHERE visitors.email = '{email}' ORDER BY visitor_misc.year DESC ";
		$query = Misc::fillQuery($query, $data);
//debug( $query );
		$result = mysql_query($query, $oConn->getConnection());
		if ( mysql_num_rows($result) > 0 ) {
			if ($row = mysql_fetch_assoc($result)) {
				$ret = $row['year'];
			}
		}
		mysql_free_result($result);

		return $ret;
	}

	public static function save() {

		$data = Visitors::getVisitorsDataFromSessionValues();
		$visitorId = Visitors::saveTableVisitors($data);

		$data = Visitors::getVisitorMiscDataFromSessionValues();
		Visitors::saveTableVisitorMisc($visitorId, $data);

		$data = Visitors::getVisitorMainAddressDataFromSessionValues();
		Visitors::saveTableVisitorAddress($visitorId, $data);

		$data = Visitors::getVisitorTmpAddressDataFromSessionValues();
		Visitors::saveTableVisitorAddress($visitorId, $data);

		$data = Visitors::getVisitorCheckboxesDataFromSessionValues();
		Visitors::saveTableVisitorCheckboxes($visitorId, $data);

		Visitors::saveTableVisitorResearchGoals($visitorId);
	}

	public static function save2($dataset, $isAdminRegistrationForm = false) {
		$data = Visitors::getVisitorsDataFromSessionValues2($dataset, true);
		$visitorId = Visitors::saveTableVisitors2($data, $isAdminRegistrationForm);

		$data = Visitors::getVisitorMiscDataFromSessionValues2($dataset);
		Visitors::saveTableVisitorMisc($visitorId, $data);

		$data = Visitors::getVisitorMainAddressDataFromSessionValues2($dataset);
		Visitors::saveTableVisitorAddress($visitorId, $data);

		$data = Visitors::getVisitorTmpAddressDataFromSessionValues2($dataset);
		Visitors::saveTableVisitorAddress($visitorId, $data);

		$data = Visitors::getVisitorCheckboxesDataFromSessionValues2($dataset);
		Visitors::saveTableVisitorCheckboxes($visitorId, $data);
		Visitors::saveTableVisitorResearchGoals2($visitorId, $dataset['year'], $dataset['fldResearchGoals']);
	}

	public static function saveTableVisitorMisc( $visitorId, $data ) {
		global $databases;

		$data['visitor_id'] = $visitorId;

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		//
		$query = "SELECT * FROM visitor_misc WHERE visitor_id={visitor_id} AND year={year} ";
		$query = Misc::fillQuery($query, $data);
		$result = mysql_query($query, $oConn->getConnection());
		if ( mysql_num_rows($result) > 0 ) {
				// template for update query
				$queryIUD = "UPDATE visitor_misc SET firstname='{firstname}', lastname='{lastname}', newsletter={newsletter}, subject='{subject}', revision='{revision}', modified_by='{modified_by}', ip_address='{ip_address}', is_deleted=0 WHERE visitor_id={visitor_id} AND `year`={year} ";
		} else {
				// template for insert query
				$queryIUD = "INSERT INTO visitor_misc(firstname, lastname, newsletter, subject, date_added, revision, visitor_id, `year`, modified_by, ip_address) VALUES ('{firstname}', '{lastname}', {newsletter}, '{subject}', '{date_added}', '{revision}', {visitor_id}, {year}, '{modified_by}', '{ip_address}') ";
		}
		mysql_free_result($result);

		//
		$queryIUD = Misc::fillQuery($queryIUD, $data);
		$result = mysql_query($queryIUD, $oConn->getConnection());
	}

	public static function saveTableVisitors( $data ) {
		global $databases;

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		$visitorId = 0;

		// outside the if criterium !!!
		// insert if email is empty AND also insert when email does not exist
		// template for insert query
		$queryIU = "INSERT INTO visitors (date_added, revision, email, modified_by, ip_address) VALUES ('{date_added}', '{revision}', '{email}', '{modified_by}', '{ip_address}') ";

		if ( $data['email'] != '' ) {
			//
			$query = "SELECT * FROM visitors WHERE email='" . $data['email'] . "' ";
			$result = mysql_query($query, $oConn->getConnection());
			if ( mysql_num_rows($result) > 0 ) {
				//
				if ($row = mysql_fetch_assoc($result)) {
					$visitorId = $row['id'];
				}

				// template for update query
				$queryIU = "UPDATE visitors SET revision='{revision}', modified_by='{modified_by}', ip_address='{ip_address}', is_deleted=0 WHERE email='{email}' ";
			}
			mysql_free_result($result);
		}

		//
		$queryIU = Misc::fillQuery($queryIU, $data);
		$result = mysql_query($queryIU, $oConn->getConnection());

		//
		$lastInsertId = mysql_insert_id();
		if ( $lastInsertId > 0 ) {
			$visitorId = $lastInsertId;
		}

		return $visitorId;
	}

	public static function saveTableVisitors2( $data, $isAdminRegistrationForm = false ) {
		global $databases;

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		//
		if ( $isAdminRegistrationForm ) {
			// admin registration form
			$query = "SELECT * FROM visitors WHERE id=" . $data['id'];
		} else {
			// visitor registration form
			$query = "SELECT * FROM visitors WHERE email='" . $data['email'] . "' ";
		}

		$result = mysql_query($query, $oConn->getConnection());
		if ( mysql_num_rows($result) > 0 ) {
			//
			if ($row = mysql_fetch_assoc($result)) {
				$visitorId = $row['id'];
			}

			// template for update query
			if ( $isAdminRegistrationForm ) {
				$queryIU = "UPDATE visitors SET revision='{revision}', modified_by='{modified_by}', email='{email}', remarks_intern='{remarks_intern}', ip_address='{ip_address}', is_deleted=0 WHERE id=" . $data['id'];
			} else {
				$queryIU = "UPDATE visitors SET revision='{revision}', modified_by='{modified_by}', ip_address='{ip_address}', is_deleted=0 WHERE email='{email}' ";
			}
		} else {
			// template for insert query
			if ( $isAdminRegistrationForm ) {
				$queryIU = "INSERT INTO visitors (date_added, revision, email, remarks_intern, modified_by, ip_address) VALUES ('{date_added}', '{revision}', '{email}', '{remarks_intern}', '{modified_by}', '{ip_address}') ";
			} else {
				$queryIU = "INSERT INTO visitors (date_added, revision, email, modified_by, ip_address) VALUES ('{date_added}', '{revision}', '{email}', '{modified_by}', '{ip_address}') ";
			}
		}
		mysql_free_result($result);

		//
		$queryIU = Misc::fillQuery($queryIU, $data);
		$result = mysql_query($queryIU, $oConn->getConnection());

		//
		if ( $isAdminRegistrationForm ) {
			$visitorId = $data['id'];
		} else {
			$lastInsertId =mysql_insert_id();
			if ( $lastInsertId > 0 ) {
				$visitorId = $lastInsertId;
			}
		}

		return $visitorId;
	}

	public static function getVisitorsDataFromSessionValues() {
		$data = array();
		$data = Visitors::getDefaultData($data);
		$data['year'] = date('Y');

		if ( $_SESSION["bsrs_registrationform"]["fldDontHaveEmail"] > '' ) {
			$data['email'] = '';
		} else {
			$data['email'] = $_SESSION["bsrs_registrationform"]["fldEmail"];
		}

		return $data;
	}

	public static function getVisitorsDataFromSessionValues2($data, $isAdminRegistrationForm = false ) {
		$ret = array();
		$ret = Visitors::getDefaultData($ret);

		$ret['id'] = $data["id"];
		$ret['year'] = $data['year'];

		if ( $data["fldDontHaveEmail"] > '' ) {
			$ret['email'] = '';
		} else {
			$ret['email'] = $data["fldEmail"];
		}

		if ( $isAdminRegistrationForm ) {
			$ret['remarks_intern'] = $data["fldRemarksIntern"];
		}

		return $ret;
	}

	public static function getVisitorMiscDataFromSessionValues() {
		$data = array();
		$data = Visitors::getDefaultData($data);
		$data['year'] = date('Y');

		$data['firstname'] = $_SESSION["bsrs_registrationform"]["fldFirstname"];
		$data['lastname'] = $_SESSION["bsrs_registrationform"]["fldLastname"];

		if ( $_SESSION["bsrs_registrationform"]["fldNewsletter"] > '' ) {
			$data['newsletter'] = '1';
		} else {
			$data['newsletter'] = '0';
		}

		$data['subject'] = trim($_SESSION["bsrs_registrationform"]["fldResearchSubject"]);

		return $data;
	}

	public static function getVisitorMiscDataFromSessionValues2($data) {
		$ret = array();
		$ret = Visitors::getDefaultData($ret);

		$ret['id'] = $data["id"];
		$ret['year'] = $data['year'];

		$ret['firstname'] = $data["fldFirstname"];
		$ret['lastname'] = $data["fldLastname"];

		if ( $data["fldNewsletterChecked"] > '' ) {
			$ret['newsletter'] = '1';
		} else {
			$ret['newsletter'] = '0';
		}

		$ret['subject'] = trim($data["fldResearchSubject"]);

		return $ret;
	}

	public static function getVisitorMainAddressDataFromSessionValues() {
		$data = array();
		$data = Visitors::getDefaultData($data);
		$data['year'] = date('Y');

		$data['is_temporary_address'] = 0;
		$data['country_id'] = trim($_SESSION["bsrs_registrationform"]["fldCountry"]);
		$data['address'] = trim($_SESSION["bsrs_registrationform"]["fldAddress"]);
		$data['place'] = trim($_SESSION["bsrs_registrationform"]["fldCity"]);

		return $data;
	}

	public static function getVisitorMainAddressDataFromSessionValues2($data) {
		$ret = array();
		$ret = Visitors::getDefaultData($ret);

		$ret['id'] = $data["id"];
		$ret['year'] = $data['year'];

		$ret['is_temporary_address'] = 0;
		$ret['country_id'] = trim($data["fldCountry"]);
		$ret['address'] = trim($data["fldAddress"]);
		$ret['place'] = trim($data["fldCity"]);

		return $ret;
	}

	public static function getVisitorTmpAddressDataFromSessionValues() {
		$data = array();
		$data = Visitors::getDefaultData($data);
		$data['year'] = date('Y');

		$data['is_temporary_address'] = 1;
		$data['country_id'] = trim($_SESSION["bsrs_registrationform"]["fldCountryTmp"]);
		$data['address'] = trim($_SESSION["bsrs_registrationform"]["fldAddressTmp"]);
		$data['place'] = trim($_SESSION["bsrs_registrationform"]["fldCityTmp"]);

		return $data;
	}

	public static function getVisitorTmpAddressDataFromSessionValues2($data) {
		$ret = array();
		$ret = Visitors::getDefaultData($ret);

//		$ret['year'] = date('Y');
		$ret['id'] = $data["id"];
		$ret['year'] = $data['year'];

		$ret['is_temporary_address'] = 1;
		$ret['country_id'] = trim($data["fldCountryTmp"]);
		$ret['address'] = trim($data["fldAddressTmp"]);
		$ret['place'] = trim($data["fldCityTmp"]);

		return $ret;
	}

	public static function saveTableVisitorAddress( $visitorId, $data ) {
		global $databases;

		$data['visitor_id'] = $visitorId;

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		if ( $data['is_temporary_address'] == 1 && $data['country_id'] == '' ) {
			// delete temporary address
			$queryIU = "UPDATE visitor_addresses SET is_deleted=1 WHERE visitor_id={visitor_id} AND `year`={year} AND is_temporary_address={is_temporary_address} ";
			$queryIU = Misc::fillQuery($queryIU, $data);
			$result = mysql_query($queryIU, $oConn->getConnection());
		} else {
			//
			if ( $data['is_temporary_address'] == 1 ) {
				$data['country_id'] = 'NULL';
			}

			//
			$query = "SELECT * FROM visitor_addresses WHERE visitor_id={visitor_id} AND year={year} AND is_temporary_address={is_temporary_address} ";
			$query = Misc::fillQuery($query, $data);
			$result = mysql_query($query, $oConn->getConnection());
			if ( mysql_num_rows($result) > 0 ) {
				// template for update query
				$queryIU = "UPDATE visitor_addresses SET revision='{revision}', is_temporary_address={is_temporary_address}, country_id={country_id}, address='{address}', place='{place}', modified_by='{modified_by}', ip_address='{ip_address}', is_deleted=0 WHERE visitor_id={visitor_id} AND `year`={year} AND is_temporary_address={is_temporary_address} ";
			} else {
				// template for insert query
				$queryIU = "INSERT INTO visitor_addresses(date_added, revision, visitor_id, `year`, is_temporary_address, country_id, address, place, modified_by, ip_address) VALUES ('{date_added}', '{revision}', {visitor_id}, {year}, {is_temporary_address}, {country_id}, '{address}', '{place}', '{modified_by}', '{ip_address}') ";
			}
			mysql_free_result($result);

			//
			$queryIU = Misc::fillQuery($queryIU, $data);
			$result = mysql_query($queryIU, $oConn->getConnection());
		}
	}

	public static function getVisitorCheckboxesDataFromSessionValues() {
		$data = array();
		$data = Visitors::getDefaultData($data);
		$data['year'] = date('Y');

		$data['checkboxes'] = $_SESSION["bsrs_registrationform"]["fldCheckbox"];

		return $data;
	}

	public static function getVisitorCheckboxesDataFromSessionValues2($data) {
		$ret = array();
		$ret = Visitors::getDefaultData($ret);

		$ret['id'] = $data["id"];
		$ret['year'] = $data['year'];

		$ret['checkboxes'] = $data["fldCheckbox"];

		return $ret;
	}

	public static function saveTableVisitorCheckboxes($visitorId, $data) {
		global $databases;

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		$data['visitor_id'] = $visitorId;

		// delete not selected
		if ( $data['checkboxes'] == '' ) {
			$queryIU = "UPDATE visitor_checkboxes SET revision='{revision}', modified_by='{modified_by}', ip_address='{ip_address}', is_deleted=1 WHERE visitor_id={visitor_id} AND `year`={year} ";
		} else {
			$queryIU = "UPDATE visitor_checkboxes SET revision='{revision}', modified_by='{modified_by}', ip_address='{ip_address}', is_deleted=1 WHERE visitor_id={visitor_id} AND `year`={year} AND checkbox_id NOT IN ( {checkboxes} ) ";
		}

		$queryIU = Misc::fillQuery($queryIU, $data);
		$result = mysql_query($queryIU, $oConn->getConnection());

		// UPDATE
		if ( $data['checkboxes'] != '' ) {

			// UPDATE existing
			$queryIU = "UPDATE visitor_checkboxes SET revision='{revision}', modified_by='{modified_by}', ip_address='{ip_address}', is_deleted=0 WHERE visitor_id={visitor_id} AND `year`={year} AND checkbox_id IN ( {checkboxes} ) ";
			$queryIU = Misc::fillQuery($queryIU, $data);
			$result = mysql_query($queryIU, $oConn->getConnection());

			// INSERT new
			$query = "SELECT * FROM checkboxes WHERE id IN ({checkboxes}) AND is_deleted=0 AND id NOT IN (SELECT checkbox_id FROM visitor_checkboxes WHERE visitor_id={visitor_id} AND `year`={year}) ";
			$query = Misc::fillQuery($query, $data);
			$result = mysql_query($query, $oConn->getConnection());
			if ( mysql_num_rows($result) > 0 ) {
				while ($row = mysql_fetch_assoc($result)) {
					$data['checkbox_id'] = $row['id'];
					$queryIU = "INSERT INTO visitor_checkboxes(date_added, revision, visitor_id, `year`, checkbox_id, modified_by, ip_address) VALUES ('{date_added}', '{revision}', {visitor_id}, {year}, {checkbox_id}, '{modified_by}', '{ip_address}') ";
					$queryIU = Misc::fillQuery($queryIU, $data);
					$result = mysql_query($queryIU, $oConn->getConnection());
				}
			}
		}
	}

	public static function saveTableVisitorResearchGoals($visitorId) {
		global $databases;
		$data = array();
		$data = Visitors::getDefaultData($data);
		$data['year'] = date('Y');

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		$data['visitor_id'] = $visitorId;

		$arrResearchGoals = Goals::getArrayOfGoals();

		//
		foreach ( $arrResearchGoals as $goal  ) {
			$data['research_goal_id'] = $goal->getId();

			$isChecked = $_SESSION["bsrs_registrationform"]["fldResearchGoal" . $goal->getId()];
			if ( strtolower($isChecked) != 'yes' ) {
				$data['is_deleted'] = 1;
			} else {
				$data['is_deleted'] = 0;
			}

			$data['comment'] = $_SESSION["bsrs_registrationform"]["fldResearchGoalText" . $goal->getId()];

			$oRG = new VisitorResearchGoal();
			$oRG->constructFromFormData($data);
			$oRG->save();
		}
	}

	public static function saveTableVisitorResearchGoals2($visitorId, $year, $fldResearchGoals) {
		global $databases;
		$data = array();
		$data = Visitors::getDefaultData($data);

		$goals = array();
		foreach ( $fldResearchGoals as $goal) {
			$goals[$goal['id']] = array('checked' => $goal['checked'], 'text' => $goal['text']);
		}

		$data['id'] = $visitorId;
		$data['year'] = $year;
		$data['visitor_id'] = $visitorId;

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		$arrResearchGoals = Goals::getArrayOfGoals();

		//
		foreach ( $arrResearchGoals as $goal  ) {
			$data['research_goal_id'] = $goal->getId();

			$isChecked = $goals[$goal->getId()]['checked'];
			if ( strtolower($isChecked) != 'yes' ) {
				$data['is_deleted'] = 1;
			} else {
				$data['is_deleted'] = 0;
			}

			$data['comment'] = $goals[$goal->getId()]['text'];

			$oRG = new VisitorResearchGoal();
			$oRG->constructFromFormData($data);
			$oRG->save();
		}
	}

	public static function getDefaultData( $data ) {
		$data['date_added'] = date('Y-m-d H:i:s');
		$data['revision'] = date('Y-m-d H:i:s');
		$data['modified_by'] = Misc::getCurrentlyLoggedInUser();
		$data['ip_address'] = Misc::getIpAddress();

		return $data;
	}
}
