<?php
class Webuser {
	protected $loginname = '';
	protected $adminLevel = '';

	function __construct($loginname) {
		global $databases;
		$this->databases = $databases;

		$this->loginname = trim($loginname);
		$this->calculateAdminLevel();
	}

	private function calculateAdminLevel() {
		if ( $this->loginname != '' ) {
			$oConn = new class_mysql($this->databases['default']);
			$oConn->connect();

			$query = "SELECT * FROM administrators WHERE loginname='" . addslashes($this->loginname) . "' AND is_deleted=0 ";
			$result = mysql_query($query, $oConn->getConnection());
			if ( mysql_num_rows($result) > 0 ) {
				if ($row = mysql_fetch_assoc($result)) {
					if ( $row['is_superadmin'] == 1 ) {
						$this->adminLevel = 'superadmin';
					} else {
						$this->adminLevel = 'admin';
					}
				}
				mysql_free_result($result);
			}
		}
	}

	public function getLoginname() {
		$ret = trim($this->loginname);

		return $ret;
	}

	public function isLoggedIn() {
		if ( $this->loginname != '' ) {
			return true;
		}

		return false;
	}

	public function checkLoggedIn() {
		if ( !$this->isLoggedIn() ) {
			$next_url = "login.php";
			Header("Location: " . $next_url);
			die(Translations::get('go_to') . " <a href=\"" . $next_url . "\">" . Translations::get('next_page') . "</a>");
		}
	}

	public function checkLoggedInAsAdminOrSuperadmin() {
		if ( !$this->isLoggedIn() || !$this->isAdminOrSuperadmin() ) {
			$next_url = "../login.php?code=notadmin";
			Header("Location: " . $next_url);
			die(Translations::get('go_to') . " <a href=\"" . $next_url . "\">" . Translations::get('next_page') . "</a>");
		}
	}

	public function checkLoggedInAsSuperadmin() {
		if ( !$this->isLoggedIn() || !$this->isSuperadmin() ) {
			$next_url = "../login.php?code=notsuperadmin";
			Header("Location: " . $next_url);
			die(Translations::get('go_to') . " <a href=\"" . $next_url . "\">" . Translations::get('next_page') . "</a>");
		}
	}

	public function dropAdminAccess() {
		$this->loginname = Settings::get('default_visitor_account');
		$_SESSION["bsrs"]["visitor_loginname"] = $this->loginname;

		// re-calculate admin level
		$this->calculateAdminLevel();
	}

	public function isAdminOrSuperadmin() {
		$ret = false;

		if ( $this->adminLevel == 'admin' || $this->adminLevel == 'superadmin' ) {
			$ret = true;
		}

		return $ret;
	}

	public function isSuperadmin() {
		$ret = false;

		if ( $this->adminLevel == 'superadmin' ) {
			$ret = true;
		}

		return $ret;
	}
}
