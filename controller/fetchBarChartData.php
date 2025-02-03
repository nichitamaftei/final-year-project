<?php

session_start();

header('Content-Type: application/json'); // sending response in json

$daysOfWeek = ["Monday" => 0,"Tuesday" => 0,"Wednesday" => 0,"Thursday" => 0,"Friday" => 0,"Saturday" => 0,"Sunday" => 0];

if (isset($_SESSION["department"])){

    $arrayOfCallMetrics = $_SESSION["department"]["call_metrics"];

    foreach ($arrayOfCallMetrics as $metric){
        $timestamp = $metric["timestamp"];
    
        $date = new DateTime($timestamp);

        $dayOfWeek = $date->format('l');

        $daysOfWeek[$dayOfWeek]++;
    }

    echo json_encode($daysOfWeek); // return the key value pair array in json
}
?>