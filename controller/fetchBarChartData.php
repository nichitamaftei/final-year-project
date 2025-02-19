<?php

session_start();

header('Content-Type: application/json'); // sending response in json

// creating a default key value pair array ready to be populated
$daysOfWeek = ["Monday" => 0,"Tuesday" => 0,"Wednesday" => 0,"Thursday" => 0,"Friday" => 0,"Saturday" => 0,"Sunday" => 0];

if (isset($_SESSION["department"])){ // (this session variable should exist regardless)

    $arrayOfCallMetrics = $_SESSION["department"]["call_metrics"]; // grab just the call metrics for the current department

    // for every metric it calculates the day of the week from the timestamp attribute and increments the value associated with the day of the week 
    foreach ($arrayOfCallMetrics as $metric){
        $timestamp = $metric["timestamp"];
        $date = new DateTime($timestamp);
        $dayOfWeek = $date->format('l');

        $daysOfWeek[$dayOfWeek]++;
    }

    echo json_encode($daysOfWeek); // return the key value pair array in json
}
?>