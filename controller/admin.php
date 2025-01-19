<?php

require_once("../model/employees.php");
require_once("../model/employeerole.php");
require_once("../model/roles.php");
require_once("../model/dataAccess.php");
require_once("../model/fetchJsonData.php"); 
require_once("../model/utilities.php");

session_start();

if ( $_SESSION["loggedInEmployee"] == null){

    doLogicAndCallLoginView();

} else if ($_SESSION["loggedInEmployee"]->isAdmin != 1){

    doLogicAndCallIndexView();
   
} else{

    $pdoSingleton = pdoSingleton::getInstance();

    $employees = $pdoSingleton->getAllEmployees();

    $employeeArray = [];

    foreach ($employees as $employee){
        $allowedToViewDepartments = [];
        $arrayOfEmployeeRole = $pdoSingleton->getAllEmployeeRole();

        foreach ($arrayOfEmployeeRole as $currentEmployeeRole) {

            if ($employee->EmployeeID == $currentEmployeeRole->EmployeeID){
                $allowedRole = $pdoSingleton->getRoleByID($currentEmployeeRole->RoleID);
                if ($allowedRole){
                    $allowedToViewDepartments[] = $allowedRole->RoleName;
                }
            }
        }
        $employeeArray[] = ['employee' => $employee, 'roles' => $allowedToViewDepartments];
    }
    require_once("../view/adminView.php");
}



?>