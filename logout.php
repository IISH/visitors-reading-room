<?php 
require_once "classes/start.inc.php";

session_unset();
session_destroy();

// go to next page
Misc::goToNextPage('index.php');
