<?php 

function doLogicAndCallIndexView() {

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    if (!isset($_SESSION["loggedInEmployee"])){ // if an employee isn't logged in 
        $_SESSION["loggedInEmployee"] = null; // set the session variable 'loggedInEmployee' to null
    }
    
    if ($_SESSION["loggedInEmployee"] == null){ // if the session variable 'loggedInEmployee' is null

        doLogicAndCallLoginView(); // go to the log in view
        require_once("../view/loginView.php");
    
    } else if (isset($_POST['signOut'])) { // if the employee clicked the 'sign out' button

        $_SESSION["loggedInEmployee"] = null;

        doLogicAndCallLoginView(); // go to the log in view
        require_once("../view/loginView.php");
    
    } else{ // otherwise

        $pdoSingleton = pdoSingleton::getInstance();

        $jsonData = getCallData();

        $arrayOfDepartments = $jsonData['company']['departments']; // puts the array of departments into a variable

        if ($_SESSION["loggedInEmployee"]->isAdmin == 0) { // if the logged in employee isn't an admin

            // if there are new departments in the json file that are not in the database as a role, add them

            $databaseRoles = $pdoSingleton->getAllRoles();

            foreach ($arrayOfDepartments as $department){

                $databaseRoleExists = false;

                foreach ($databaseRoles as $databaseRole){

                    if ($department['name'] == $databaseRole->RoleName) {
                        $databaseRoleExists = true;
                        break;
                    }
                }

                if (!$databaseRoleExists){
                    $role = new Roles();
                    $role->RoleName = $department['name'];
                    $pdoSingleton->addNewRole($role); 
                }
            }

            // if a department is deleted from the json file, delete it from the database

            foreach ($databaseRoles as $databaseRole) {
                $roleFound = false;
            
                foreach ($arrayOfDepartments as $department) {
                    if ($department['name'] == $databaseRole->RoleName) {
                        $roleFound = true;
                        break; 
                    }
                }
            
                if (!$roleFound) {
                    $pdoSingleton->deleteRoleById($databaseRole->RoleID); 
                }
            }

            // get a list of the department names the loggedin employee has access to

            $arrayOfEmployeeRole = $pdoSingleton->getAllEmployeeRole(); 

            $allowedToViewDepartments = [];


            foreach ($arrayOfEmployeeRole as $allowed) {

                if ($_SESSION['loggedInEmployee']->EmployeeID == $allowed->EmployeeID){
                    $allowedRole = $pdoSingleton->getRoleByID($allowed->RoleID); 

                    if ($allowedRole) {
                        $allowedToViewDepartments[] = $allowedRole->RoleName;
                    }
                }
            }

            // in the arrayOfDepartments, remove departments which are not on the list


            foreach ($arrayOfDepartments as $key => $department) {

                $roleFound = false;

                foreach ($allowedToViewDepartments as $allowed) {
                    if ($department['name'] == $allowed) {
                        $roleFound = true;
                        break; 
                    }
                }
            
                if (!$roleFound) {
                    unset($arrayOfDepartments[$key]); 
                }
            }

            // set the removed departments to it's self
            
            $arrayOfDepartments = array_values($arrayOfDepartments);

        }

        if (empty($arrayOfDepartments)) { // if the employee has access to no department
            
            $departmentName = "You do not have access to any departments.";
            require_once("../view/indexView.php");

        } else{ // otherwise

            if (!isset($_SESSION["department"])){ // if the session variable 'department' isn't set, set it to the first index of $arrayOfDepartments variable
                $_SESSION["department"] = $arrayOfDepartments[0]; // default to the first department
            }
    
            if (isset($_POST['dept'])){ // if the employee navigated to a different department
                $deptIndex = (int)$_REQUEST['dept']; // obtains the index of the selected department
                $_SESSION["department"] = $arrayOfDepartments[$deptIndex]; // uses the index to select the department
                $_SESSION["deptIndex"] = $deptIndex;
            } else{
                $deptIndex = 0; // obtains the index of the selected department
                $_SESSION["department"] = $arrayOfDepartments[$deptIndex]; // uses the index to select the department
                $_SESSION["deptIndex"] = $deptIndex;
            }
    
            $results = $pdoSingleton->getAllEmployees();
            
            $departmentName = $_SESSION["department"]['name']; // displays the currently selected department for debugging purposes
    
            require_once("../view/indexView.php");

        }
    }
}

function doLogicAndCallLoginView(){

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $pdoSingleton = pdoSingleton::getInstance(); // getting the pdoSingleton in order to access methods that speak to the database

    if (!isset($_REQUEST["logInEmail"]) && !isset($_REQUEST["logInPassword"])){ // if nothing was input, set default values
        $_REQUEST["logInEmail"] = "";
        $_REQUEST["logInPassword"] = "";
    }
    else if ($_REQUEST["logInEmail"] != "" && $_REQUEST["logInPassword"] != ""){ // if all forms have been entered

        $employees = $pdoSingleton->getAllEmployees(); // get an array of all employees from the database
        $found = false;

        foreach ($employees as $employee): 
            if ($employee->Email == $_REQUEST["logInEmail"] && $employee->Password == $_REQUEST["logInPassword"]){ // checking if a user in the database has the same email and password which was inputted
                $found = true;
                $foundEmployee = $employee;
            }
        endforeach;

        if ($found == true){
            $_SESSION["loggedInEmployee"] = $foundEmployee;
            $pdoSingleton->updateLastLogInByID($_SESSION["loggedInEmployee"]->EmployeeID);

            $auditLog = new AuditLog();
            $auditLog->EmployeeID = $_SESSION['loggedInEmployee']->EmployeeID;
            $auditLog->Date = date('Y-m-d');
            $auditLog->Time = date('H:i:s');

            if ($_SESSION["loggedInEmployee"]->isAdmin == 0){

                $auditLog->ActionPerformed = "User Logged in";
                $auditLog->Details = "User Logged in";

            } else{

                $auditLog->ActionPerformed = "Admin Logged in";
                $auditLog->Details = "Admin Logged in";

            }

            $auditLogID = $pdoSingleton->addNewAuditLog($auditLog);
            $auditLog->AuditLogID = $auditLogID;   
        }
    }

    if (isset($_SESSION["loggedInEmployee"])){

        doLogicAndCallIndexView();
    }
    else{
        require_once("../view/loginView.php");
    }
}

?>