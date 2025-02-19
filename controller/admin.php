<?php

require_once("../model/dataAccess.php");
require_once("../model/employees.php");
require_once("../model/employeerole.php");
require_once("../model/roles.php");
require_once("../model/auditLogs.php");
require_once("../model/fetchJsonData.php"); 
require_once("../model/utilities.php");

session_start();

$employeeToDeleteID = null;

$employeeID = null;

$pdoSingleton = pdoSingleton::getInstance();

if (isset($_POST["removeAdminFromThisEmployeeID"])){ // if the admin removed the admin privledges of an employee
        
    $employeeID = $_POST["removeAdminFromThisEmployeeID"]; // grab the employeeID from the request

    $employeeObject = $pdoSingleton->getEmployeeByID($employeeID); // grab the employee using the ID

    $pdoSingleton->removeAdminFromID($employeeID); // change the isAdmin from 1 to 0

    createNewAuditLog($_SESSION["loggedInEmployee"]->EmployeeID, date("Y-m-d"), date("H:i"), "Admin Removed", "Admin removed Admin Privledges from the User: " . $employeeObject->FirstName . $employeeObject->LastName);

    if ($employeeID == $_SESSION["loggedInEmployee"]->EmployeeID) { // if the admin changed their own admin privledges

        $_SESSION["loggedInEmployee"] = null; // essentially logs them out
    } 
} 

if (isset($_POST["employeeID"]) && isset($_POST["roleID"])){ // if the admin removes a role from an employee
    $employeeID = $_POST["employeeID"];
    $roleID = $_POST["roleID"];

    $employeeObject = $pdoSingleton->getEmployeeByID($employeeID);
    $roleObject = $pdoSingleton->getRoleByID($roleID);

    $pdoSingleton->removeRoleFromEmployee($employeeID, $roleID); // remove the selected role

    createNewAuditLog($_SESSION["loggedInEmployee"]->EmployeeID, date("Y-m-d"), date("H:i"), "Role Removed", "Admin removed the Role: " . $roleObject->RoleName . " from: " . $employeeObject->FirstName . $employeeObject->LastName);
}

if (isset($_POST["employeeToDeleteID"])){ // if the admin deletes an employee
    $employeeToDeleteID = $_POST["employeeToDeleteID"];

    $employeeToBeDeletedDetails = $pdoSingleton->getEmployeeByID($employeeToDeleteID); // used to create audit log
    
    createNewAuditLog($_SESSION["loggedInEmployee"]->EmployeeID, date("Y-m-d"), date("H:i"), "User Deleted", "Admin deleted the User " . $employeeToBeDeletedDetails->FirstName . $employeeToBeDeletedDetails->LastName);

    $pdoSingleton->deleteEmployeeFromID($employeeToDeleteID); // delete the employee

    if ($employeeToDeleteID == $_SESSION["loggedInEmployee"]->EmployeeID){ // if the admin deleted their own account

        $_SESSION["loggedInEmployee"] = null;    
    }
}

if (isset($_POST["addEmployeeID"]) && isset($_POST["addRoleID"])){ // if the admin adds a new role to an employee

    $employeeID = $_POST["addEmployeeID"];
    $roleID = $_POST["addRoleID"];

    $employeeObject = $pdoSingleton->getEmployeeByID($employeeID);
    
    if ($roleID == "admin"){ // if the admin chose to set the employee as an admin

        $pdoSingleton->addAdminFromID($employeeID); // changes isAdmin from 0 to 1
        $details = "Admin added the Administrator Role to: " . $employeeObject->FirstName . $employeeObject->LastName;

    } else{ // if it's a regular role

        $roleObject = $pdoSingleton->getRoleByID($roleID); // grabs the role details from the requested roleID

        $pdoSingleton->addRoleToEmployee($employeeID, $roleID); // add that role in the EmployeeRole table
        $details = "Admin added the Role: " . $roleObject->RoleName . " To: " . $employeeObject->FirstName . $employeeObject->LastName;
    }

    $employeeID = $_SESSION["loggedInEmployee"]->EmployeeID;
    $actionPerformed = "Role added";
    $date = date("Y-m-d");
    $time = date("H:i");

    createNewAuditLog($employeeID, $date, $time, $actionPerformed, $details);
}

if (isset($_REQUEST["firstName"]) && isset($_REQUEST["lastName"]) && isset($_REQUEST["email"]) && isset($_REQUEST["password"])){ // if the admin adds a new user

    $employee = new Employees();
    $employee->FirstName = trim($_REQUEST["firstName"]);

    $employee->LastName = trim($_REQUEST["lastName"]);

    $employee->Email = trim($_REQUEST["email"]);
    $employee->Password = trim($_REQUEST["password"]);
   
    $employeeID = $pdoSingleton->addEmployee($employee);
    $employee->EmployeeID = $employeeID;

    createNewAuditLog($_SESSION["loggedInEmployee"]->EmployeeID, date('Y-m-d'), date('H:i:s'), "User Added", "Admin added new User " . $employee->FirstName . $employee->LastName);
}


if (!isset($_SESSION["loggedInEmployee"]) || $_SESSION["updatedPassword"] == false){ // if no one is logged or they havn't updated their password

    doLogicAndCallLoginView(); // kick them to the log in view

} else if ($_SESSION["loggedInEmployee"]->isAdmin != 1 || isset($_POST["adminGoBackButton"])){ // if the employee is logged in, but isn't an admin or the admin pressed the back button

    doLogicAndCallIndexView(); // kick them to the home view
   
} else{

        // --- logic to set and handle the "Users" and "Logs" tab in the adminView ---

        if (!isset($_SESSION["currentTab"])){
            $_SESSION["currentTab"] = "users";
        } 

        if (isset($_REQUEST["users"])){
            $_SESSION["currentTab"] = "users";
        }
        if (isset($_REQUEST["logs"])){
            $_SESSION["currentTab"] = "logs";
        }


        // --- logic to set and handle the filtering for the "Users" Tab ---

        if(!isset($_SESSION["userFilter"])){ // if the default values for User filtering havn't been set yet, set them
            $_SESSION["userFilter"] = [
                "name" => "not set",
                "email" => "not set",
                "logIn" => "not set"   
            ];
        }

        $filterUserKeys = ["name", "email", "logIn"]; // filterable users columns

        foreach ($filterUserKeys as $filter){
            if (isset($_REQUEST[$filter . "FilterForm"])){ // if the user clicked a column icon
                toggleFilterState("userFilter", $filter); // cycle the value to the next (e.g not set -> asc)
            }
        }

        // --- logic to set and handle the filtering for the "Logs" Tab ---

        if(!isset($_SESSION["logsFilter"])){ // if the default values for Log filtering havn't been set yet, set them
            $_SESSION["logsFilter"] = [
                "date" => "desc",
                "time" => "desc",
                "nameLog" => "not set",  
                "eventType" => "not set",
                "details" => "not set"
            ];
        }

        $filterLogsKeys = ["date", "time", "nameLog", "eventType", "details"]; // filterable logs columns

        foreach ($filterLogsKeys as $filter){
            if (isset($_REQUEST[$filter . "FilterForm"])){ // if the user clicked a column icon
                toggleFilterState("logsFilter", $filter); // cycle the value to the next (e.g not set -> asc)
            }
        }

        // --- the following populates the $employeeArray key value pair with an $employee object, $assignedRoles[] array of their assigned roles and $availableRoles[] array of their available roles in order to be iterated through in the adminView ---

        $employees = $pdoSingleton->getAllEmployees($_SESSION["userFilter"]); // get every Employee
        $allRoles = $pdoSingleton->getAllRoles(); // get every Role
        $allEmployeeRole = $pdoSingleton->getAllEmployeeRole(); // get every EmployeeRole

        $employeeArray = []; // initialise an empty array

        foreach ($employees as $employee){ // for every employee

            $assignedRoles = []; // initialise an empty array to store assigned roles for the current employee
            $availableRoles = $allRoles; // initially set it to every role for the current employee
            
            foreach ($allEmployeeRole as $currentEmployeeRole){ // for every EmployeeRole row

                if ($employee->EmployeeID == $currentEmployeeRole->EmployeeID){ // if the current employee's ID matches the employeeID for an EmployeeRole row

                    $assignedRole = $pdoSingleton->getRoleByID($currentEmployeeRole->RoleID); // get the role and put it inside the $assignedRole 

                    if ($assignedRole){ // if the role exists within the role table

                        // assignedRoles[] array determines what roles an employee has to display
                        $assignedRoles[] = $assignedRole; // get the role and put it inside the $assignedRole[] array
                        
                        // availableRoles[] array determines which roles an employee doesn't already have to select from
                        $availableRoles = [];

                        foreach ($allRoles as $role){ // for every role
                            $isAssigned = false; // set a false flag

                            foreach ($assignedRoles as $assignedRole){ // for every assigned role that has been assigned to the employee

                                if ($role->RoleID == $assignedRole->RoleID){ // if the roleID matches the roleID for an already assigned role of the current employee
                                    $isAssigned = true; // set flag to true
                                    break; // stop the loop
                                }
                            }

                            if (!$isAssigned){ // if the flag is false
                                $availableRoles[] = $role; // add the role to $availableRoles[] array
                            }
                        }
                    }
                }
            }
            // populate the key value pair array with the values filled from the for each loops
            $employeeArray[] = ["employee" => $employee, "roles" => $assignedRoles, "availableRoles" => $availableRoles]; 
        }

        // populates logs tab
        $auditLogs = $pdoSingleton->getAllAuditLogsWithEmployeeNames($_SESSION["logsFilter"]); 

        require_once("../view/adminView.php");
    }
?>