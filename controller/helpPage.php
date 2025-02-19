<?php 

require_once("../model/utilities.php");
require_once("../model/image.php");
require_once("../model/employees.php");
require_once("../model/employeerole.php");
require_once("../model/roles.php");
require_once("../model/auditLogs.php");
require_once("../model/dataAccess.php");
require_once("../model/fetchJsonData.php"); 

session_start();

if (isset($_POST["helpPageGoBackButton"])){ // if the back button is pressed

    doLogicAndCallIndexView(); // kick them to the home view
    
} else{

    require_once("../view/helpPageView.php");
}
?>