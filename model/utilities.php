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
    
    } elseif ($_SESSION["updatedPassword"] == false){

        doLogicAndCallUpdatePasswordView(); // kick them to the update password view
        
    } else{ // otherwise

        $pdoSingleton = pdoSingleton::getInstance();

        $jsonData = getCallData();

        $arrayOfDepartments = $jsonData['company']['departments']; // puts the array of departments into a variable

        // if a role exists in the JSON file but not the database, add the role to the database

        $databaseRoles = $pdoSingleton->getAllRoles();

        foreach ($arrayOfDepartments as $department){

            $databaseRoleExists = false;

            foreach ($databaseRoles as $databaseRole){

                if ($department['name'] == $databaseRole->RoleName){
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

        // if a department exists in the database but not the JSON file, delete the role from the database

        foreach ($databaseRoles as $databaseRole){
            $roleFound = false;
        
            foreach ($arrayOfDepartments as $department){
                if ($department['name'] == $databaseRole->RoleName){
                    $roleFound = true;
                    break; 
                }
            }
        
            if (!$roleFound) {
                $pdoSingleton->deleteRoleById($databaseRole->RoleID); 
            }
        }

        if ($_SESSION["loggedInEmployee"]->isAdmin == 0){ // if the logged in employee isn't an admin

            // get a list of the department names the loggedin employee has access to

            $arrayOfEmployeeRole = $pdoSingleton->getAllEmployeeRole(); 

            $allowedToViewDepartments = [];

            foreach ($arrayOfEmployeeRole as $allowed){

                if ($_SESSION['loggedInEmployee']->EmployeeID == $allowed->EmployeeID){
                    $allowedRole = $pdoSingleton->getRoleByID($allowed->RoleID); 

                    if ($allowedRole) {
                        $allowedToViewDepartments[] = $allowedRole->RoleName;
                    }
                }
            }

            // in the arrayOfDepartments, remove departments which are not on the list

            foreach ($arrayOfDepartments as $key => $department){

                $roleFound = false;

                foreach ($allowedToViewDepartments as $allowed){
                    if ($department['name'] == $allowed){
                        $roleFound = true;
                        break; 
                    }
                }
            
                if (!$roleFound) {
                    unset($arrayOfDepartments[$key]); 
                }
            }

            // set the new array of departments the employee has access to to it's self
            $arrayOfDepartments = array_values($arrayOfDepartments);
        }

        if (empty($arrayOfDepartments)){ // if the employee has access to no department
            
            $departmentName = "You do not have access to any departments.";
            require_once("../view/indexView.php");

        } else{ // otherwise
    
            
            if (isset($_POST['dept'])){ // if the employee navigated to a different department
                $deptIndex = (int)$_REQUEST['dept']; // obtains the index of the selected department
                $_SESSION["department"] = $arrayOfDepartments[$deptIndex]; // uses the index to select the department
                $_SESSION["deptIndex"] = $deptIndex;
            } else if (isset($_SESSION["deptIndex"])) { // if a department view was already set, present that instead of the first index
                $deptIndex = $_SESSION["deptIndex"]; 
                $_SESSION["department"] = $arrayOfDepartments[$deptIndex];
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


    if (!isset($_SESSION["loggedInEmployee"])) {
        $_SESSION["loggedInEmployee"] = null;
    }
    
    $_SESSION["updatedPassword"] = false;


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

                $foundEmployee = $employee;

                $_SESSION["loggedInEmployee"] = $foundEmployee;

                if ($foundEmployee->LastLogIn == null){

                } else {
                    $_SESSION["updatedPassword"] = true;
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

                    break;
                }
            }
        endforeach;
    }

    if (isset($_SESSION["loggedInEmployee"]) && $_SESSION["updatedPassword"] == true){

        doLogicAndCallIndexView();
    }
    elseif (!isset($_SESSION["loggedInEmployee"])){

        $_SESSION["department"] = null;
        $_SESSION["deptIndex"] = null;

        require_once("../view/loginView.php");
    } else{

        doLogicAndCallUpdatePasswordView();
    }
}


function doLogicAndCallUpdatePasswordView(){

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $pdoSingleton = pdoSingleton::getInstance();

    if (!isset($_REQUEST["newPassword"]) && !isset($_REQUEST["confirmPassword"])){ // if nothing was input, set default values
        $_REQUEST["newPassword"] = "";
        $_REQUEST["confirmPassword"] = "";
    }

    if (!isset($_SESSION["loggedInEmployee"])){ // if an employee isn't logged in 
        $_SESSION["loggedInEmployee"] = null; // set the session variable 'loggedInEmployee' to null

    }

    if (!isset($_SESSION["updatedPassword"])){
        $_SESSION["updatedPassword"] = false;

        echo "update password set to false";
    }
    
    if ($_SESSION["loggedInEmployee"] == null && $_SESSION["updatedPassword"] == false){ // if the session variable 'loggedInEmployee' is null

        doLogicAndCallLoginView(); // go to the log in view

    } else{

        // getting the pdoSingleton in order to access methods that speak to the database
        if ($_REQUEST["newPassword"] != "" && $_REQUEST["confirmPassword"] != ""){ // if all forms have been entered
            
            if ($_REQUEST["newPassword"] == $_REQUEST["confirmPassword"]){ // checking if the new pass is equal to the confirm pass

                $_SESSION["updatedPassword"] = true;

                $pdoSingleton->updateEmployeePasswordByID($_SESSION["loggedInEmployee"]->EmployeeID, $_REQUEST["confirmPassword"]);

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
    }

    if ($_SESSION["updatedPassword"] == true){
        doLogicAndCallIndexView();

    } else{
        require_once("../view/updatePasswordView.php");
    }
}

?>