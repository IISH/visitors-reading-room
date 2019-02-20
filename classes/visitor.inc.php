<?php 
class VisitorCheckboxes {
	protected $visitorId;
	protected $year;
	protected $arr = array();

	function __construct( $visitorId, $year ) {
		$this->visitorId = $visitorId;
		$this->year = $year;

		$this->loadFromDatabase();
	}

	private function loadFromDatabase() {
		global $databases;

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		$query = "SELECT * FROM visitor_checkboxes WHERE visitor_id=" . $this->visitorId . ' AND year=' . $this->year . ' AND is_deleted=0 ';
		$result = mysql_query($query, $oConn->getConnection());

		while ($row = mysql_fetch_assoc($result)) {
			$this->arr[] = $row['checkbox_id'];
		}

		mysql_free_result($result);
	}

	public function getCheckboxes() {
		return $this->arr;
	}
}

class VisitorAddress {
	protected $_is_found = false;
	protected $id;
	protected $visitorId;
	protected $year;
	protected $isTemporaryAddress;
	protected $countryId;
	protected $address;
	protected $city;

	function __construct( $visitorId, $year, $isTemporaryAddress ) {
		$this->visitorId = $visitorId;
		$this->year = $year;
		$this->isTemporaryAddress = $isTemporaryAddress;

		$this->loadFromDatabase();
	}

	private function loadFromDatabase() {
		global $databases;

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		$query = "SELECT * FROM visitor_addresses WHERE visitor_id=" . $this->visitorId . ' AND year=' . $this->year . ' AND is_temporary_address=' . $this->isTemporaryAddress . ' AND is_deleted=0 ';
		$result = mysql_query($query, $oConn->getConnection());

		if ($row = mysql_fetch_assoc($result)) {
			$this->_is_found = true;
			$this->countryId = $row['country_id'];
			$this->address = $row['address'];
			$this->city = $row['place'];
		} else {
			$this->countryId = '';
			$this->address = '';
			$this->city = '';
		}
		mysql_free_result($result);
	}

	public function isFound() {
		return $this->_is_found;
	}

	public function getCountryId() {
		return $this->countryId;
	}

	public function getAddress() {
		return $this->address;
	}

	public function getCity() {
		return $this->city;
	}
}

class Visitor {
	protected $id;
	protected $email;
	protected $firstname;
	protected $lastname;
	protected $subject;
	protected $newsletter;
	protected $homelandAddress;
	protected $temporaryAddress;
	protected $checkboxes;
	protected $year;
	protected $remarks_intern;

	function __construct( $id = 0, $year = '' ) {
		if ( $year == '' ) {
			$year = date('Y');
		}
		$this->id = $id;
		$this->year = $year;

		$this->loadFromDatabase();
		$this->homelandAddress = new VisitorAddress($this->id, $this->year, 0);
		$this->temporaryAddress = new VisitorAddress($this->id, $this->year, 1);
		$this->checkboxes = new VisitorCheckboxes($this->id, $this->year);
	}

	private function loadFromDatabase() {
		global $databases;

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		$query = "SELECT * FROM visitors LEFT JOIN visitor_misc ON visitors.id = visitor_misc.visitor_id AND visitor_misc.year=" . $this->year . " WHERE visitors.id=" . $this->id;
		$result = mysql_query($query, $oConn->getConnection());
		if ( mysql_num_rows($result) > 0 ) {
			while ($row = mysql_fetch_assoc($result)) {
				$this->email = $row['email'];
				$this->email = $row['email'];
				$this->firstname = $row['firstname'];
				$this->lastname = $row['lastname'];
				$this->subject = $row['subject'];
				$this->newsletter = $row['newsletter'];
				$this->remarks_intern = $row['remarks_intern'];
			}
			mysql_free_result($result);
		}
	}

	public function getRemarksIntern() {
		return $this->remarks_intern;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getLastname() {
		return $this->lastname;
	}

	public function getFirstname() {
		return $this->firstname;
	}

	public function getFirstLastname() {
		return trim($this->firstname . ' ' . $this->lastname);
	}

	public function getSubject() {
		return $this->subject;
	}

	public function getNewsletter() {
		return $this->newsletter;
	}

	public function getHomelandAddress() {
		return $this->homelandAddress;
	}

	public function getTemporaryAddress() {
		return $this->temporaryAddress;
	}

	public function getCheckboxes() {
		return $this->checkboxes;
	}

	public function __toString() {
		return "Class: " . get_class($this) . "\n";
	}

	public function getVisitorsResearchGoals() {
		global $databases;

		$arr = array();

		$oConn = new class_mysql($databases['default']);
		$oConn->connect();

		$query = 'SELECT * FROM visitor_research_goals WHERE is_deleted=0 AND visitor_id=' . $this->id . " AND year=" . $this->year;
		$result = mysql_query($query, $oConn->getConnection());
		if ( mysql_num_rows($result) > 0 ) {

			while ($row = mysql_fetch_assoc($result)) {
				$tmp = array(
					'id' => $row['research_goal_id']
					, 'text' => $row['comment']
					, 'checked' => 'yes'
					);

				$arr[] = $tmp;
			}
			mysql_free_result($result);
		}

		return $arr;
	}
}
