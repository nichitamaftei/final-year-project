<?php 

function getCallData(){
    
    $filePath = (__DIR__.'/fabricated_call_flow_data.json'); // putting the file path of the data to the variable

    $data = file_get_contents($filePath); // reads the contents of the file
    
    $jsonData = json_decode($data, true); // decodes it into json format

    return $jsonData; // returns the object
}

?>