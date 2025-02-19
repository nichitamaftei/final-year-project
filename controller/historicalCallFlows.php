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

if (isset($_POST["historicalCallFlowGoBackButton"])){ // if the back button is pressed

    doLogicAndCallIndexView(); // kick them to the home view

} else{
    
    // --- logic to set and handle the filtering for the "historicalCallFlowsView" ---

    if(!isset($_SESSION["historicalFlowFilter"])){ // if the default values for historical call flows havn't been set yet, set them
        $_SESSION["historicalFlowFilter"] = [
            "historicalFlowDate" => "desc",
            "historicalFlowTime" => "desc",
            "historicalFlowModifiedBy" => "not set",  
        ];
    }

    $filterUserKeys = ["historicalFlowDate", "historicalFlowTime", "historicalFlowModifiedBy"]; // filterable historical call flows columns

    foreach ($filterUserKeys as $filter){
        if (isset($_REQUEST[$filter . "FilterForm"])){ // if the user clicked a column icon
            toggleFilterState("historicalFlowFilter", $filter); // cycle the value to the next (e.g not set -> asc)
        }
    }

    $pdoSingleton = pdoSingleton::getInstance(); 

    $databaseRoles = $pdoSingleton->getAllRoles();

    $departmentID;

    // gathering which role relates to the currently viewed department
    foreach ($databaseRoles as $databaseRole){

        if ($_SESSION["department"]["name"] == $databaseRole->RoleName){
            $departmentID = $databaseRole->RoleID;
        }
    }

    $departmentName = $_SESSION["department"]["name"];

    // fetch all the image data associated with the current department
    $images = $pdoSingleton->getDiagramsByDepartmentID($_SESSION["historicalFlowFilter"], $departmentID);

    require_once("../view/historicalCallFlowsView.php");
}
?>