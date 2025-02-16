<?php
session_start();

header('Content-Type: application/json'); // sending response in json

error_reporting(E_ALL);
ini_set("display_errors", 1);

if (isset($_POST["request"])){
    $request = $_POST["request"];

    if (isset($_SESSION["department"])){

        if ($request == "callMetrics"){
            echo json_encode($_SESSION["department"]["call_metrics"]); // return the current session department's call metrics to javascript in json
        } elseif ($request == "departmentName"){
            echo json_encode($_SESSION["department"]["name"]); // return the current session department's name to javascript in json
        } else{
            echo json_encode($_SESSION["department"]); // return the current session department to javascript in json
        }  
    } 
}
?>