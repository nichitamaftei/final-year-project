<?php 

function doLogicAndCallIndexView() {

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    if (!isset($_SESSION["loggedInEmployee"])){
        $_SESSION["loggedInEmployee"] = null;
    }
    
    if ($_SESSION["loggedInEmployee"] == null){

        doLogicAndCallLoginView();
        require_once("../view/loginView.php");
    
    } else{

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

        

        if (!isset($_SESSION["department"])) {
            $_SESSION["department"] = $arrayOfDepartments[0]; // default to the first department
        }

        if (isset($_POST['dept'])) {
            $deptIndex = (int)$_REQUEST['dept']; // obtains the index of the selected department
            $_SESSION["department"] = $arrayOfDepartments[$deptIndex]; // uses the index to select the department
            $_SESSION["deptIndex"] = $deptIndex;
        } else {
            $deptIndex = 0; // obtains the index of the selected department
            $_SESSION["department"] = $arrayOfDepartments[$deptIndex]; // uses the index to select the department
            $_SESSION["deptIndex"] = $deptIndex;
        }

        $results = $pdoSingleton->getAllEmployees();
        
        $departmentName = $_SESSION["department"]['name']; // displays the currently selected department for debugging purposes

        require_once("../view/indexView.php");
    }
}

function doLogicAndCallLoginView(){

    $pdoSingleton = pdoSingleton::getInstance(); // getting the pdoSingleton in order to access methods that speak to the database

    if (!isset($_REQUEST["logInEmail"]) && !isset($_REQUEST["logInPassword"])){
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
            //$_SESSION["signOut"] = "Logged in as: " . $foundUser->email . " (Click here to log out)";
        }
    }

    if ($_SESSION["loggedInEmployee"] != null){
    
        doLogicAndCallIndexView();
    }
    else{
        require_once("../view/loginView.php");
    }
}

?>