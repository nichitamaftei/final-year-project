<?php 

session_start();

header('Content-Type: application/json'); // sending response in json


if (isset($_POST["request"])){
    $request = $_POST["request"];

    if ($request == "user"){

        if (isset($_SESSION["userFilter"])){
            echo json_encode($_SESSION["userFilter"]); // return the current session department to javascript in json
        }

    } else if ($request == "logs"){

        if (isset($_SESSION["logsFilter"])){
            echo json_encode($_SESSION["logsFilter"]); // return the current session department to javascript in json
        } 

    }
}



?>