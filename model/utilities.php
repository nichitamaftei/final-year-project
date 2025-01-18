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

        print_r($results);
        
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