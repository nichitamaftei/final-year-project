<?php

require_once("../model/fetchJsonData.php"); 

session_start();

$jsonData = getCallData();

$departmentName = $_SESSION["department"]['name'];

$arrayOfDepartments = $jsonData['company']['departments']; // puts the array of departments into a variable

require_once("../view/editCallFlowView.php");

?>