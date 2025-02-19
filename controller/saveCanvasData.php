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

$pdoSingleton = pdoSingleton::getInstance();

if (isset($_POST["image"])){ // if the client side sends image data

    $pngData = $_POST["image"]; // set the $pngData variable with the incoming data

    $databaseRoles = $pdoSingleton->getAllRoles();
    $departmentID;

    // gathering which role relates to the currently viewed department
    foreach ($databaseRoles as $databaseRole){

        if ($_SESSION["department"]["name"] == $databaseRole->RoleName){
            $departmentID = $databaseRole->RoleID;
        }
    }

    $employeeID = $_SESSION["loggedInEmployee"]->EmployeeID;

    date_default_timezone_set("Europe/London"); // making sure the time is correct

    $date = date("Y-m-d");
    $time = date("H:i:s");

    // add the image data along with the relvant info to the database
    $pdoSingleton->addDiagram($departmentID, $employeeID, $pngData, $date, $time);
}
?>