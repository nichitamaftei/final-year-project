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

$pdoSingleton = pdoSingleton::getInstance();

if (isset($_POST["image"])){ // if the admin removes a role from an employee

    $pngData = $_POST["image"];

    $databaseRoles = $pdoSingleton->getAllRoles();

    $departmentID;

    foreach ($databaseRoles as $databaseRole){

        if ($_SESSION["department"]["name"] == $databaseRole->RoleName){
            $departmentID = $databaseRole->RoleID;
        }
    }

    $employeeID = $_SESSION["loggedInEmployee"]->EmployeeID;

    date_default_timezone_set("Europe/London"); 

    $date = date("Y-m-d");
    $time = date("H:i:s");

    $pdoSingleton->addDiagram($departmentID, $employeeID, $pngData, $date, $time);
}
?>