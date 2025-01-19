<?php

require_once("../model/employees.php");
require_once("../model/employeerole.php");
require_once("../model/roles.php");
require_once("../model/dataAccess.php");
require_once("../model/fetchJsonData.php"); 
require_once("../model/utilities.php");

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$employeeToDeleteID = null;

$pdoSingleton = pdoSingleton::getInstance();

if (isset($_POST['removeAdminFromThisEmployeeID'])){ // if the admin removed the admin privledges of an employee
    $employeeID = $_POST['removeAdminFromThisEmployeeID']; // grab the employeeId

    $pdoSingleton->removeAdminFromID($employeeID); // change the isAdmin from 1 to 0

    if ($_SESSION["loggedInEmployee"]->EmployeeID == $employeeID) { // if the admin changed their own admin privledges

        $_SESSION['loggedInEmployee'] = null; 

        doLogicAndCallLoginView(); // kick them  to the log in view
    }

} else if (!isset($_SESSION["loggedInEmployee"])){ // if no one is logged in

    doLogicAndCallLoginView(); // kick them to the log in view

} else if ($_SESSION["loggedInEmployee"]->isAdmin != 1){ // if the employee is logged in, but isn't an admin

    doLogicAndCallIndexView(); // kick them to the home view
   
} else{

    if ($employeeToDeleteID == $_SESSION['loggedInEmployee']->EmployeeID){ // if the admin deleted their own account
        $_SESSION['loggedInEmployee'] = null;

        doLogicAndCallLoginView(); // kick them back to the log in view
    } else{ 

        if (isset($_POST['employeeID']) && isset($_POST['roleID'])){ // if the admin removes a role from an employee
            $employeeID = $_POST['employeeID'];
            $roleID = $_POST['roleID'];
            
            $pdoSingleton->removeRoleFromEmployee($employeeID, $roleID); // remove the selected role
        }
    
        
        if (isset($_POST['employeeToDeleteID'])){ // if the admin deletes an employee
            $employeeToDeleteID = $_POST['employeeToDeleteID'];
            
            $pdoSingleton->deleteEmployeeFromID($employeeToDeleteID); // delete the employee
        }


        if (isset($_POST['addEmployeeID']) && isset($_POST['addRoleID'])){ // if the admin adds a new role to an employee

            $employeeID = $_POST['addEmployeeID'];
            $roleID = $_POST['addRoleID'];

            if ($roleID == "admin"){ // if the admin chose to set the employee as an admin
                $pdoSingleton->removeAllInEmployeeRoleByID($employeeID); // remove all current roles

                $pdoSingleton->addAdminFromID($employeeID); // change isAdmin from 0 to 1
            } else{ // if it's a regular role
                $pdoSingleton->addRoleToEmployee($employeeID, $roleID); // add that role in the EmployeeRole table
            }
        }

        if (isset($_REQUEST['firstName']) && isset($_REQUEST['lastName']) && isset($_REQUEST['email']) && isset($_REQUEST['password'])){
        
            $employee = new Employees();
            $employee->FirstName = trim($_REQUEST['firstName']);

            $employee->LastName = trim($_REQUEST['lastName']);

            $employee->Email = trim($_REQUEST['email']);
            $employee->Password = trim($_REQUEST['password']);
           

            $employeeID = $pdoSingleton->addEmployee($employee);
            $employee->EmployeeID = $employeeID;
        }





        // the following populates $employeeArray with $employee, $assignedRoles[] and $availableRoles[] in order to be iterated through in the adminView

        $employees = $pdoSingleton->getAllEmployees(); // get every Employee
        $allRoles = $pdoSingleton->getAllRoles(); // get every Role
        $allEmployeeRole = $pdoSingleton->getAllEmployeeRole(); // get every EmployeeRole

        $employeeArray = []; // initialise an empty array

        foreach ($employees as $employee){ // for every employee

            $assignedRoles = []; // initialise an empty array to store assigned roles for the current employee
            $availableRoles = $allRoles; // initially set it to every role for the current employee
            
            foreach ($allEmployeeRole as $currentEmployeeRole){ // for every EmployeeRole row

                if ($employee->EmployeeID == $currentEmployeeRole->EmployeeID) { // if the current employee's ID matches the employeeID for an EmployeeRole row

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

            $employeeArray[] = ['employee' => $employee, 'roles' => $assignedRoles, 'availableRoles' => $availableRoles];
        }

        require_once("../view/adminView.php");

    }


   
}
?>