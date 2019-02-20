<?php

class class_file {

	public static function getFileSource($filename) {
		$ret = '';

		// check if file exists
		if ( file_exists($filename) ) {
			// get source
			$ret = file_get_contents($filename);
		} else {
			die('Cannot find file: ' . $filename);
		}

		return $ret;
	}
}
