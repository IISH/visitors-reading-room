<?php 

require_once dirname(__FILE__) . "/file.inc.php";
require_once dirname(__FILE__) . "/misc.inc.php";

class Page {
	protected $page_template;
	protected $content;
	protected $title;
	protected $color;
	protected $showLanguageChoice;
	protected $oWebuser;
	protected $level;
	protected $showMenu;

	function __construct($page_template) {
		global $oWebuser;

		$this->page_template = $page_template;
		$this->content = '';
		$this->title = '';
		$this->color = '73A0C9';
		$this->showLanguageChoice = true;
		$this->level = '';
		$this->showMenu = false;

		$this->oWebuser = $oWebuser;
	}

	public function getPage() {
		$oFile = new class_file();
		$page = $oFile->getFileSource($this->page_template);
		$page = str_replace('{content}', $this->content, $page);
		$page = str_replace('{title}', $this->title, $page);
		$page = str_replace('{color}', $this->color, $page);
		$page = str_replace('{website_name}', Translations::get('website_name'), $page);
		$page = str_replace('{languages_javascript}', $this->createLanguageList_Visitors(), $page);
		$page = str_replace('{languages_click}', $this->createLanguageList_Admin(), $page);
		$page = str_replace('{level}', $this->level, $page);

		$page = str_replace('{adminbar_javascript}', $this->getAdminbarTextUrl_Visitors(), $page);
		$page = str_replace('{adminbar_click}', $this->getAdminbarTextUrl_Admin(), $page);
		$page = str_replace('{adminbar_visibility}', $this->getAdminbarVisibilty(), $page);

		$page = str_replace('{menu}', $this->createMenu(), $page);
		return $page;
	}

	public function setContent( $content ) {
		$this->content = $content;
	}

	public function getContent() {
		return $this->content;
	}

	public function setTitle( $title ) {
		$this->title = $title;
	}

	public function setLevel( $level ) {
		$this->level = $level;
	}

	public function setShowMenu( $showMenu ) {
		$this->showMenu = $showMenu;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setShowLanguageChoice( $choice ) {
		$this->showLanguageChoice = $choice;
	}

	private function createLanguageList_Visitors() {
		$list = '';

		if ( $this->showLanguageChoice ) {
			foreach ( explode(";", Settings::get('available_languages')) as $language ) {
				$list .= "<a href=\"#\" onClick=\"return setSubmitTypeAndSubmitForm('language:$language');\"><img class=\"flag\" src=\"" . $this->level . "images/misc/$language.png\" border=\"0\" width=\"75\" height=\"54\"></a>";
			}
		}

		return $list;
	}

	private function createLanguageList_Admin() {
		$list = '';

		if ( $this->showLanguageChoice ) {
			foreach ( explode(";", Settings::get('available_languages')) as $language ) {
				$list .= "<a href=\"$language.php\" onClick=\"return checkIfChanged();\"><img class=\"flag\" src=\"" . $this->level . "images/misc/$language.png\" border=\"0\" width=\"75\" height=\"54\"></a>";
			}
		}

		return $list;
	}

	private function getAdminbarTextUrl_Visitors() {
		global $oWebuser;

		$ret = '';

		if ( $oWebuser->isAdminOrSuperadmin() ) {
			$ret = 'You are logged in as (super)admin. Go to <a href="admin/" class="adminbar">admin pages</a> or <a href="#" onClick="return setSubmitTypeAndSubmitForm(\'drop:admin\');" class="adminbar">drop admin access</a> if you no longer require it.';
		}

		return $ret;
	}

	private function getAdminbarTextUrl_Admin() {
		global $oWebuser;

		$ret = '';

		if ( $oWebuser->isAdminOrSuperadmin() ) {
			$ret = 'You are in the administrator pages. <a href="drop.php" class="adminbar">Drop admin access</a> if you no longer require it.';
		}

		return $ret;
	}

	private function getAdminbarVisibilty() {
		global $oWebuser;

		$ret = '';

		if ( !$oWebuser->isAdminOrSuperadmin() ) {
			$ret = 'hidden';
		}

		return $ret;
	}

	private function createMenu() {
		global $oWebuser;

		$ret = '';

		if ( $this->showMenu ) {

			if ( $oWebuser->isAdminOrSuperadmin() ) {
				$ret .= $this->createMenuHeader(Translations::get('admin_page_registrations_title'));
				$ret .= $this->createMenuItem('index.php', Translations::get('admin_page_day_title'));
				$ret .= $this->createMenuItem('month.php', Translations::get('admin_page_month_title'));
				$ret .= $this->createMenuItem('year.php', Translations::get('admin_page_year_title'));
				$ret .= '<br>';
			}

			if ( $oWebuser->isAdminOrSuperadmin() ) {
				$ret .= $this->createMenuHeader(Translations::get('admin_page_mailchimp_title'));
				$ret .= $this->createMenuItem('mailchimp.php', Translations::get('admin_page_mailchimp_title'));
				$ret .= '<br>';
			}

			if ( $oWebuser->isAdminOrSuperadmin() ) {
				$ret .= $this->createMenuHeader(Translations::get('admin_page_statistics_title'));
				$ret .= $this->createMenuItem('statistics.year.php', Translations::get('admin_page_statistics_year_title'));
				$ret .= $this->createMenuItem('statistics.month.php', Translations::get('admin_page_statistics_month_title'));
				$ret .= $this->createMenuItem('statistics.day.php', Translations::get('admin_page_statistics_day_title'));
				$ret .= $this->createMenuItem('statistics.countries.php', Translations::get('admin_page_statistics_countries_title'));
				$ret .= $this->createMenuItem('statistics.emaildomain.php', Translations::get('admin_page_statistics_emaildomain_title'));
//				$ret .= $this->createMenuItem('statistics.emaildomain.short.php', Translations::get('admin_page_statistics_emaildomain_short_title'));
				$ret .= '<br>';
			}

			if ( $oWebuser->isAdminOrSuperadmin() ) {
				$ret .= $this->createMenuHeader(Translations::get('admin_page_search_title'));
				$ret .= $this->createMenuItem('search.research.php', Translations::get('admin_page_searchresearch_title'));
				$ret .= $this->createMenuItem('search.goal.php', Translations::get('admin_page_searchgoal_title'));
				$ret .= '<br>';
			}

			if ( $oWebuser->isSuperadmin() ) {
				$ret .= $this->createMenuHeader(Translations::get('admin_page_superadminpages_title'));
				$ret .= $this->createMenuItem('administrators.php', Translations::get('admin_page_administrators_title'));
				$ret .= $this->createMenuItem('checkboxes.php', Translations::get('admin_page_checkboxes_title'));
				$ret .= $this->createMenuItem('countries.php', Translations::get('admin_page_countries_title'));
				$ret .= $this->createMenuItem('research_goals.php', Translations::get('admin_page_researchgoals_title'));
				$ret .= $this->createMenuItem('settings.php', Translations::get('admin_page_settings_title'));
				$ret .= $this->createMenuItem('translations.php', Translations::get('admin_page_translations_title'));
				$ret .= $this->createMenuItem('static.country.totals.php', Translations::get('admin_page_static_country_totals'));
			}
		}

		return $ret;
	}

	private function createMenuItem( $url, $label ) {
		$ret = "<a href=\"$url\">$label</a><br>";

		return $ret;
	}

	private function createMenuHeader( $label ) {
		$ret = "<b>$label</b><br>";

		return $ret;
	}
}
