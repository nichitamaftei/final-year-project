<?php 

require_once("../model/employees.php");
require_once("../model/employeerole.php");
require_once("../model/roles.php");
require_once("../model/dataAccess.php");
require_once("../model/fetchJsonData.php");
require_once("../model/utilities.php");

session_start();

doLogicAndCallIndexView(); // call the index view function from the "utilities.php" file

?>