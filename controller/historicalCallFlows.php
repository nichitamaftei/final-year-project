<?php 

require_once("../model/image.php");
require_once("../model/employees.php");
require_once("../model/employeerole.php");
require_once("../model/roles.php");
require_once("../model/auditLogs.php");
require_once("../model/dataAccess.php");
require_once("../model/fetchJsonData.php"); 
require_once("../model/utilities.php");

session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

if (isset($_POST["historicalCallFlowGoBackButton"])){ // if the back button is pressed

    doLogicAndCallIndexView(); // kick them to the home view
    
} else{

    $pdoSingleton = pdoSingleton::getInstance();

    $databaseRoles = $pdoSingleton->getAllRoles();

    $departmentID;

    foreach ($databaseRoles as $databaseRole){

        if ($_SESSION["department"]["name"] == $databaseRole->RoleName){
            $departmentID = $databaseRole->RoleID;
        }
    }

    $departmentName = $_SESSION["department"]["name"];


    $images = $pdoSingleton->getDiagramsByDepartmentID($departmentID);


    require_once("../view/historicalCallFlowsView.php");

}

?>