<?php 

function doLogicAndCallIndexView() {
    $jsonData = getCallData();

    $arrayOfDepartments = $jsonData['company']['departments']; // puts the array of departments into a variable

    if (!isset($_SESSION["department"])) {
        $_SESSION["department"] = $arrayOfDepartments[0]; // default to the first department
    }

    if (isset($_POST['dept'])) {
        $deptIndex = (int)$_REQUEST['dept']; // obtains the index of the selected department
        $_SESSION["department"] = $arrayOfDepartments[$deptIndex]; // uses the index to select the department
        $_SESSION["deptIndex"] = $deptIndex;
    }

    $departmentName = $_SESSION["department"]['name']; // displays the currently selected department for debugging purposes

    require_once("../view/indexView.php");
}



?>