<?php

class PrevNextDate {
	protected $date;

	function __construct() {
	}

	public function dateAsString($date) {
		// convert to date format
		$this->date = strtotime( $date );
	}

	public function getPrevYear( $format = '%Y-%m-%d' ) {
		return strftime($format, $this->calculate('-1 year'));
	}

	public function getNextYear( $format = '%Y-%m-%d' ) {
		return strftime($format, $this->calculate('+1 year'));
	}

	public function getPrevMonth( $format = '%Y-%m-%d' ) {
		return strftime($format, $this->calculate('-1 month'));
	}

	public function getNextMonth( $format = '%Y-%m-%d' ) {
		return strftime($format, $this->calculate('+1 month'));
	}

	public function getPrevDay( $format = '%Y-%m-%d', $skipWeekendDays = 0 ) {
		if ( $skipWeekendDays == 1 ) {
			return $this->getPrevWorkingDay($format);
		}
		return strftime($format, $this->calculate('-1 day'));
	}

	public function getNextDay( $format = '%Y-%m-%d', $skipWeekendDays = 0 ) {
		if ( $skipWeekendDays == 1 ) {
			return $this->getNextWorkingDay($format);
		}

		return strftime($format, $this->calculate('+1 day'));
	}

	public function getPrevWorkingDay( $format = '%Y-%m-%d' ) {
		$whatToDo = '-1 day';

		if ( strftime('%w', $this->date) == 1 ) {
			$whatToDo = '-3 days';
		}

		return strftime($format, $this->calculate($whatToDo));
	}

	public function getNextWorkingDay( $format = '%Y-%m-%d' ) {
		$whatToDo = '+1 day';

		if ( strftime('%w', $this->date) == 5 ) {
			$whatToDo = '+3 days';
		}
		return strftime($format, $this->calculate($whatToDo));
	}

	private function calculate( $whatToDo ) {
		// convert date to string
		$dateAsString = strftime('%Y-%m-%d', $this->date);

		// what to do
		$dateAsString .= ' ' . $whatToDo;

		// convert string back to date
		$prevDateAsDate = strtotime($dateAsString);

		return $prevDateAsDate;
	}
}