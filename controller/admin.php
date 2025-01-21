<?php

require_once("../model/employees.php");
require_once("../model/employeerole.php");
require_once("../model/roles.php");
require_once("../model/auditLogs.php");
require_once("../model/dataAccess.php");
require_once("../model/fetchJsonData.php"); 
require_once("../model/utilities.php");

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$employeeToDeleteID = null;

$employeeID = null;

$pdoSingleton = pdoSingleton::getInstance();

if (isset($_POST['removeAdminFromThisEmployeeID'])){ // if the admin removed the admin privledges of an employee
        
    $employeeID = $_POST['removeAdminFromThisEmployeeID']; // grab the employeeID

    $employeeObject = $pdoSingleton->getEmployeeByID($employeeID);

    $pdoSingleton->removeAdminFromID($employeeID); // change the isAdmin from 1 to 0

    $auditLog = new AuditLog();
    $auditLog->EmployeeID = $_SESSION['loggedInEmployee']->EmployeeID;
    $auditLog->Date = date('Y-m-d');
    $auditLog->Time = date('H:i');
    $auditLog->ActionPerformed = "Admin Removed";
    $auditLog->Details = "Admin removed Admin Privledges from the User: " . $employeeObject->FirstName . $employeeObject->LastName;
    $auditLogID = $pdoSingleton->addNewAuditLog($auditLog);
    $auditLog->AuditLogID = $auditLogID;

    if ($employeeID == $_SESSION["loggedInEmployee"]->EmployeeID) { // if the admin changed their own admin privledges

        $_SESSION['loggedInEmployee'] = null; 
    } 

} 

if (isset($_POST['employeeID']) && isset($_POST['roleID'])){ // if the admin removes a role from an employee
    $employeeID = $_POST['employeeID'];
    $roleID = $_POST['roleID'];

    $employeeObject = $pdoSingleton->getEmployeeByID($employeeID);
    $roleObject = $pdoSingleton->getRoleByID($roleID);

    $pdoSingleton->removeRoleFromEmployee($employeeID, $roleID); // remove the selected role

    $auditLog = new AuditLog();
    $auditLog->EmployeeID = $_SESSION['loggedInEmployee']->EmployeeID;
    $auditLog->Date = date('Y-m-d');
    $auditLog->Time = date('H:i');
    $auditLog->ActionPerformed = "Role Removed";
    $auditLog->Details = "Admin removed the Role: " . $roleObject->RoleName . " from: " . $employeeObject->FirstName . $employeeObject->LastName;
    $auditLogID = $pdoSingleton->addNewAuditLog($auditLog);
    $auditLog->AuditLogID = $auditLogID;
}


if (isset($_POST['employeeToDeleteID'])){ // if the admin deletes an employee
    $employeeToDeleteID = $_POST['employeeToDeleteID'];

    $employeeToBeDeletedDetails = $pdoSingleton->getEmployeeByID($employeeToDeleteID);
    
    $auditLog = new AuditLog();
    $auditLog->EmployeeID = $_SESSION['loggedInEmployee']->EmployeeID;
    $auditLog->Date = date('Y-m-d');
    $auditLog->Time = date('H:i');
    $auditLog->ActionPerformed = "User Deleted";
    $auditLog->Details = "Admin deleted the User " . $employeeToBeDeletedDetails->FirstName . $employeeToBeDeletedDetails->LastName;
    $auditLogID = $pdoSingleton->addNewAuditLog($auditLog);
    $auditLog->AuditLogID = $auditLogID;

    $pdoSingleton->deleteEmployeeFromID($employeeToDeleteID); // delete the employee

    if ($employeeToDeleteID == $_SESSION['loggedInEmployee']->EmployeeID){ // if the admin deleted their own account

        $_SESSION['loggedInEmployee'] = null;    
    }
}


if (isset($_POST['addEmployeeID']) && isset($_POST['addRoleID'])){ // if the admin adds a new role to an employee

    $employeeID = $_POST['addEmployeeID'];
    $roleID = $_POST['addRoleID'];

    $employeeObject = $pdoSingleton->getEmployeeByID($employeeID);
    $roleObject = $pdoSingleton->getRoleByID($roleID);

    $auditLog = new AuditLog();
    

    if ($roleID == "admin"){ // if the admin chose to set the employee as an admin
        $pdoSingleton->removeAllInEmployeeRoleByID($employeeID); // remove all current roles

        $pdoSingleton->addAdminFromID($employeeID); // change isAdmin from 0 to 1

        $auditLog->EmployeeID = $_SESSION['loggedInEmployee']->EmployeeID;
        $auditLog->Date = date('Y-m-d');
        $auditLog->Time = date('H:i');
        $auditLog->ActionPerformed = "Role added";
        $auditLog->Details = "Admin added the Administrator Role to: " . $employeeObject->FirstName . $employeeObject->LastName;

    } else{ // if it's a regular role
        $pdoSingleton->addRoleToEmployee($employeeID, $roleID); // add that role in the EmployeeRole table


        $auditLog->EmployeeID = $_SESSION['loggedInEmployee']->EmployeeID;
        $auditLog->Date = date('Y-m-d');
        $auditLog->Time = date('H:i');
        $auditLog->ActionPerformed = "Role added";
        $auditLog->Details = "Admin added the Role: " . $roleObject->RoleName . " To: " . $employeeObject->FirstName . $employeeObject->LastName;
        
    }

    $auditLogID = $pdoSingleton->addNewAuditLog($auditLog);
    $auditLog->AuditLogID = $auditLogID;
}

if (isset($_REQUEST['firstName']) && isset($_REQUEST['lastName']) && isset($_REQUEST['email']) && isset($_REQUEST['password'])){ // if the admin adds a new user

    $employee = new Employees();
    $employee->FirstName = trim($_REQUEST['firstName']);

    $employee->LastName = trim($_REQUEST['lastName']);

    $employee->Email = trim($_REQUEST['email']);
    $employee->Password = trim($_REQUEST['password']);
   

    $employeeID = $pdoSingleton->addEmployee($employee);
    $employee->EmployeeID = $employeeID;

    $auditLog = new AuditLog();
    $auditLog->EmployeeID = $_SESSION['loggedInEmployee']->EmployeeID;
    $auditLog->Date = date('Y-m-d');
    $auditLog->Time = date('H:i:s');
    $auditLog->ActionPerformed = "User Added";
    $auditLog->Details = "Admin added new User " . $employee->FirstName . $employee->LastName;

    $auditLogID = $pdoSingleton->addNewAuditLog($auditLog);
    $auditLog->AuditLogID = $auditLogID;
}


if (!isset($_SESSION["loggedInEmployee"]) || $_SESSION["updatedPassword"] == false){ // if no one is logged or they havn't updated their password

    doLogicAndCallLoginView(); // kick them to the log in view

} else if ($_SESSION["loggedInEmployee"]->isAdmin != 1 || isset($_POST['adminGoBackButton'])) { // if the employee is logged in, but isn't an admin or the admin pressed the back button

    doLogicAndCallIndexView(); // kick them to the home view
   
} else{

        // the following populates $employeeArray with $employee, $assignedRoles[] and $availableRoles[] in order to be iterated through in the adminView

        $employees = $pdoSingleton->getAllEmployees(); // get every Employee
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
                        
                        // availableRoles[] array determines which roles an employee doesn't already have, to select from
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

            $employeeArray[] = ['employee' => $employee, 'roles' => $assignedRoles, 'availableRoles' => $availableRoles];
        }

        // populates logs tab
        $auditLogs = $pdoSingleton->getAllAuditLogsWithEmployeeNames(); 

        require_once("../view/adminView.php");

    }
?>