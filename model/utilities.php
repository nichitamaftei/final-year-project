<?php 

function doLogicAndCallLoginView(){

    if (!isset($_SESSION["loggedInEmployee"])){ // if no one's logged in before
        $_SESSION["loggedInEmployee"] = null;
        $_SESSION["updatedPassword"] = false;
    }

    $pdoSingleton = pdoSingleton::getInstance(); // getting the pdoSingleton in order to access methods that speak to the database

    if (!isset($_REQUEST["logInEmail"]) && !isset($_REQUEST["logInPassword"])){ // if nothing was input, set default values
        $_REQUEST["logInEmail"] = "";
        $_REQUEST["logInPassword"] = "";
    }
    else if ($_REQUEST["logInEmail"] != "" && $_REQUEST["logInPassword"] != ""){ // if all forms have been entered

        $employees = $pdoSingleton->getAllEmployees($_SESSION["userFilter"]); // get an array of all employees from the database

        // searching and comparing emails and password to facilitate a login
        foreach ($employees as $employee): 
            if ($employee->Email == $_REQUEST["logInEmail"] && $employee->Password == $_REQUEST["logInPassword"]){ // checking if a user in the database has the same email and password which was inputted

                $foundEmployee = $employee;

                $_SESSION["loggedInEmployee"] = $foundEmployee;

                if ($foundEmployee->LastLogIn != null){ // if the employee has logged in before
                    $_SESSION["updatedPassword"] = true;
                    $pdoSingleton->updateLastLogInByID($_SESSION["loggedInEmployee"]->EmployeeID);

                    // changing audit log details based on if the employee is an admin or not
                    if ($_SESSION["loggedInEmployee"]->isAdmin == 0){

                        $actionPerformed = "User Logged in";
                        $details = "User Logged in";

                    } else{

                        $actionPerformed = "Admin Logged in";
                        $details = "Admin Logged in";
                    }

                    // create the log with the relevant details
                    createNewAuditLog($_SESSION["loggedInEmployee"]->EmployeeID, date("Y-m-d"), date("H:i:s"), $actionPerformed, $details);
                }
            }
        endforeach;
    }

    if (isset($_SESSION["loggedInEmployee"]) && $_SESSION["updatedPassword"] == true){ // if a match was found and they have an updated password

        doLogicAndCallIndexView(); // the log in was successfull show them to the index view
    }
    elseif (!isset($_SESSION["loggedInEmployee"])){ // if email/password was incorrect

        $_SESSION["department"] = null;
        $_SESSION["deptIndex"] = null;

        require_once("../view/loginView.php");

    } else{ // if a match was found but they didn't update their password

        doLogicAndCallUpdatePasswordView(); // kick them to the update password view
    }
}

function doLogicAndCallUpdatePasswordView(){

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

                // changing audit log details based on if the employee is an admin or not
                if ($_SESSION["loggedInEmployee"]->isAdmin == 0){

                    $actionPerformed = "User Logged in";
                    $details = "User Logged in";

                } else{

                    $actionPerformed = "Admin Logged in";
                    $details = "Admin Logged in";

                }
                
                // create the log with the relevant details
                createNewAuditLog($_SESSION["loggedInEmployee"]->EmployeeID, date("Y-m-d"), date("H:i:s"), $actionPerformed, $details);
            } 
        } 
    }

    if ($_SESSION["updatedPassword"] == true){ // if the password matches

        doLogicAndCallIndexView(); // show them to the index view
    } else{ // otherwise let them try again
        require_once("../view/updatePasswordView.php");
    }
}

function doLogicAndCallIndexView() {

    if (!isset($_SESSION["callFlowEdited"])){ // if the session hasn't been set, set it to false (this is used to check if a call flow was edited)
        $_SESSION["callFlowEdited"] = false;
    }

    // reset the sessions if they have already been inputted to avoid array access errors when going to different departments after editing a department
    if(isset($_SESSION["currentBusinessHoursDay"])){
        $_SESSION["currentCallQueue"] = null;
        $_SESSION["callQueueIndex"] = null;

        $_SESSION["currentBusinessHoursDay"] = null;
        $_SESSION["businessHoursDayIndex"] = null;
    }

    if (!isset($_SESSION["loggedInEmployee"])){ // if an employee isn't logged in 
        $_SESSION["loggedInEmployee"] = null;
    }
    
    if ($_SESSION["loggedInEmployee"] == null){ // if the session variable 'loggedInEmployee' is null

        doLogicAndCallLoginView(); // kick them to the log in view
        require_once("../view/loginView.php");
    
    } else if (isset($_POST["signOut"])) { // if the employee clicked the 'sign out' button

        $_SESSION["loggedInEmployee"] = null;

        doLogicAndCallLoginView(); // kick them to the log in view
        require_once("../view/loginView.php");
    
    } elseif ($_SESSION["updatedPassword"] == false){ // if the employee didn't update thier password

        doLogicAndCallUpdatePasswordView(); // kick them to the update password view
        
    } else{ // if passed the previous checks

        $pdoSingleton = pdoSingleton::getInstance();

        $jsonData = getCallData();

        $arrayOfDepartments = $jsonData["company"]["departments"]; // puts the array of departments into a variable

        // --- logic to dynamically create or delete Roles based on the data ---

        // if a role exists in the JSON file but not the database, add the role to the database

        $databaseRoles = $pdoSingleton->getAllRoles();

        foreach ($arrayOfDepartments as $department){

            $databaseRoleExists = false;

            foreach ($databaseRoles as $databaseRole){

                if ($department["name"] == $databaseRole->RoleName){
                    $databaseRoleExists = true;
                    break;
                }
            }

            if (!$databaseRoleExists){
                $role = new Roles();
                $role->RoleName = $department["name"];
                $pdoSingleton->addNewRole($role); 
            }
        }

        // if a department exists in the database but not the JSON file, delete the role from the database

        foreach ($databaseRoles as $databaseRole){
            $roleFound = false;
        
            foreach ($arrayOfDepartments as $department){
                if ($department["name"] == $databaseRole->RoleName){
                    $roleFound = true;
                    break; 
                }
            }
        
            if (!$roleFound) {
                $pdoSingleton->deleteRoleById($databaseRole->RoleID); 
            }
        }

        if ($_SESSION["loggedInEmployee"]->isAdmin == 0){ // if the logged in employee isn't an admin

            // get an array of the department names the loggedin employee has access to

            $arrayOfEmployeeRole = $pdoSingleton->getAllEmployeeRole(); 

            $allowedToViewDepartments = [];

            foreach ($arrayOfEmployeeRole as $allowed){

                if ($_SESSION["loggedInEmployee"]->EmployeeID == $allowed->EmployeeID){
                    $allowedRole = $pdoSingleton->getRoleByID($allowed->RoleID); 

                    // if the role exists add it to the array
                    if ($allowedRole) {
                        $allowedToViewDepartments[] = $allowedRole->RoleName;
                    }
                }
            }

            // in the arrayOfDepartments, remove departments which are not on the array - this facilitates which departments the employee has access to 

            foreach ($arrayOfDepartments as $key => $department){

                $roleFound = false;

                foreach ($allowedToViewDepartments as $allowed){
                    if ($department["name"] == $allowed){
                        $roleFound = true;
                        break; 
                    }
                }
            
                if (!$roleFound){
                    unset($arrayOfDepartments[$key]); 
                }
            }

            // set the new array of departments the employee has access to to it's self
            $arrayOfDepartments = array_values($arrayOfDepartments);
        }

        if (empty($arrayOfDepartments)){ // if the employee has access to no department
            
            $departmentName = "You do not have access to any departments! Contact Administrator";
            require_once("../view/indexView.php");

        } else{ // otherwise
    
            if (isset($_POST["dept"])){ // if the employee navigated to a different department
                $deptIndex = (int)$_REQUEST["dept"]; // obtains the index of the selected department
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
            
            $departmentName = $_SESSION["department"]["name"]; // displays the currently selected department for debugging purposes


            // -- beginning of calculating and setting up the call metrics --

            $arrayOfCallMetrics = $_SESSION["department"]["call_metrics"]; // gets an array of all the metrics for the current department

            // sets up the array for the Top 5 Callers info

            $arrayOfAllNumbersCalled = array_column($arrayOfCallMetrics, "number");

            $timesANumberHasBeenCalled = array_count_values($arrayOfAllNumbersCalled);

            arsort($timesANumberHasBeenCalled);

            $topNumbers = array_slice($timesANumberHasBeenCalled, 0, 5, true);


            // sets up the variables for the Incoming Calls Today info

            $totalCallers = count($arrayOfAllNumbersCalled);

            $totalWaitTime = 0;

            foreach ($arrayOfCallMetrics as $metric){
                $totalWaitTime += $metric["wait_time"];
            }

            if ($totalCallers > 0){
                $averageWaitTime = $totalWaitTime / $totalCallers;
            } else {
                $averageWaitTime = 0;
            }

            // sets up the varirables for the Abandonded Today info

            $abdandondedArray = [];

            foreach ($arrayOfCallMetrics as $metric){
                if ($metric["abandoned"] == true){
                    $abdandondedArray[] = $metric;
                }
            }

            $totalAbandondedCalls =  count($abdandondedArray);

            $abandondedRate = ($totalAbandondedCalls / $totalCallers) * 100;

            // sets up the variables for the Service Target Level

            $answeredOnTime = [];

            foreach ($arrayOfCallMetrics as $metric){
                if ($metric["wait_time"]  < 20){
                    $answeredOnTime[] = $metric;
                }
            }

            $numberOfCallsAnsweredOnTime = count($answeredOnTime);

            $actualServiceLevelPercentage = round(($numberOfCallsAnsweredOnTime / $totalCallers) * 100);

            // sets up the variables for the Extra Metrics Today info

            $answereddArray = [];

            foreach ($arrayOfCallMetrics as $metric){
                if ($metric["abandoned"] == false){
                    $answereddArray[] = $metric;
                }
            }

            $totalAnsweredCalls =  count($answereddArray);

            $arrayOfWaitTimes = array_column($arrayOfCallMetrics, "wait_time");

            $longestWaitTime = max($arrayOfWaitTimes);


            require_once("../view/indexView.php");
        }
    }
}


function toggleFilterState($filterTab, $filterKey){ // cycling through the filterable options
    
    if ($_SESSION[$filterTab][$filterKey] == "not set"){
        $_SESSION[$filterTab][$filterKey] = "asc";
    } elseif ($_SESSION[$filterTab][$filterKey] == "asc"){
        $_SESSION[$filterTab][$filterKey] = "desc";
    } else{
        $_SESSION[$filterTab][$filterKey] = "not set";
    }
}

function createNewAuditLog($employeeID, $date, $time, $actionPerformed, $details){ // function to create an audit log and put it in the database

    date_default_timezone_set("Europe/London"); 

    $pdoSingleton = pdoSingleton::getInstance();

    $auditLog = new AuditLog(); // intantiate an object from the AuditLog class
    $auditLog->EmployeeID = $employeeID;
    $auditLog->Date = $date;
    $auditLog->Time = $time;
    $auditLog->ActionPerformed = $actionPerformed;
    $auditLog->Details = $details;

    $auditLogID = $pdoSingleton->addNewAuditLog($auditLog);
    $auditLog->AuditLogID = $auditLogID;

}
?>