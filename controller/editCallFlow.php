<?php

require_once("../model/fetchJsonData.php");
require_once("../model/employees.php"); 
require_once("../model/roles.php");
require_once("../model/employeerole.php");
require_once("../model/dataAccess.php");
require_once("../model/auditLogs.php");
require_once("../model/utilities.php");

session_start();

if (isset($_POST["cancel"])){ // if the user presses the cancel button
        
    doLogicAndCallIndexView(); // kick them back to the home view
    require_once("../view/indexView.php");

} elseif (!isset($_SESSION["loggedInEmployee"]) || $_SESSION["updatedPassword"] == false){ // if a user isn't logged in or hasn't updated their password

    doLogicAndCallLoginView(); // kick them to the log in view
    
} else{

    $jsonData = getCallData(); // gathers the call data

    $arrayOfDepartments = $jsonData["company"]["departments"]; // puts the array of departments into a variable
    $departmentName = $_SESSION["department"]["name"]; // sets the departments name in a variable

    // --- logic to handle the Call Queue Selection --- 

    $arrayOfCurrentCallQueues = $_SESSION["department"]["auto_attendant"]["call_queues"]; // sets the departments call queue's array in a variable
    
    
    // fetching the selection to dynamically set the call queue's details
    
    if (isset($_POST["indexCallQueueSelection"])){ // if a call queue selection is made
        $CallQueueIndex = (int)$_REQUEST["indexCallQueueSelection"]; // obtains the index of the selected department
        $_SESSION["currentCallQueue"] = $arrayOfCurrentCallQueues[$CallQueueIndex]; // uses the index to select the department
        $_SESSION["callQueueIndex"] = $CallQueueIndex; // sets the index to a session variable

        // set the variables to have the data from the selected cq
        $callQueueMembers = implode("\n", $_SESSION["currentCallQueue"]["group"]["members"]); 
        $waitingTimeAmount = $_SESSION["currentCallQueue"]["answer_timeout_seconds"]; 
        $maxCallsAmount = $_SESSION["currentCallQueue"]["max_calls"];

    }

    // if a selection has already been initialised, display that selection

    if(isset($_SESSION["currentCallQueue"])){
        $_SESSION["currentCallQueue"] =  $arrayOfCurrentCallQueues[$_SESSION["callQueueIndex"]];

        $callQueueMembers = implode("\n", $_SESSION["currentCallQueue"]["group"]["members"]); 
        $waitingTimeAmount = $_SESSION["currentCallQueue"]["answer_timeout_seconds"]; 
        $maxCallsAmount = $_SESSION["currentCallQueue"]["max_calls"];
    }else { // if it's the first time in the view, set it to the first index
        $_SESSION["currentCallQueue"] = $arrayOfCurrentCallQueues[0]; // default to the first department 
        $_SESSION["callQueueIndex"] = 0;

        $callQueueMembers = implode("\n", $arrayOfCurrentCallQueues[0]["group"]["members"]);
        $waitingTimeAmount = $arrayOfCurrentCallQueues[0]["answer_timeout_seconds"];
        $maxCallsAmount = $arrayOfCurrentCallQueues[0]["max_calls"];
    }

    // --- logic to handle the Business Hours Selection --- 

    $arrayOfBusinessHoursDays = $_SESSION["department"]["auto_attendant"]["business_hours"]["days"]; // sets the array of business days info to a variable
   
    // fetching the selection to dynamically set the selected business day
    if (isset($_POST["businessHoursDaySelection"])){ // if a businesshours day selection is made
        $businessHoursDayIndex = (int)$_REQUEST["businessHoursDaySelection"]; // obtains the index of the selected department
        $_SESSION["currentBusinessHoursDay"] = $arrayOfBusinessHoursDays[$businessHoursDayIndex]; // uses the index to select the department
        $_SESSION["businessHoursDayIndex"] = $businessHoursDayIndex; // sets the index to a session variable

        // set the variables to have the data from the selected cq
        $dayStartTime = $_SESSION["currentBusinessHoursDay"]["from_time_start"]; 
        $dayEndTime = $_SESSION["currentBusinessHoursDay"]["from_time_end"];

    } 

    // if a selection has already been initialised, display that selection
    if(isset($_SESSION["currentBusinessHoursDay"])){
        $_SESSION["currentBusinessHoursDay"] =  $arrayOfBusinessHoursDays[$_SESSION["businessHoursDayIndex"]];

        $dayStartTime = $_SESSION["currentBusinessHoursDay"]["from_time_start"]; 
        $dayEndTime = $_SESSION["currentBusinessHoursDay"]["from_time_end"];
    }else { // if it's the first time in the view, set it to the first index
        $_SESSION["currentBusinessHoursDay"] = $arrayOfBusinessHoursDays[0];
        $_SESSION["businessHoursDayIndex"] = 0;

        $dayStartTime = $arrayOfBusinessHoursDays[0]["from_time_start"]; 
        $dayEndTime = $arrayOfBusinessHoursDays[0]["from_time_end"];
    }


    // setting the auto attendant details

    $voicemailMembers = implode("\n", $_SESSION["department"]["auto_attendant"]["voicemail"]["members"]);

    $auto_attendantGreeting =  $_SESSION["department"]["auto_attendant"]["aa_greeting"];

    // dynamically generating the 'selected' attribute for the <option> tag for each day

    $optionsDayArray = [];

    foreach ($arrayOfBusinessHoursDays as $index => $dayArray) {

        if ($dayArray["day_name"] == $_SESSION["currentBusinessHoursDay"]["day_name"]){ // if the name of the call queue is the same to the one being currently displayed
            $isSelected = "selected";
        } else { // otherwise
            $isSelected = "";
        }

        $optionsDayArray[] = "<option $isSelected value=\"$index\">{$dayArray['day_name']}</option>";
    }

    // dynamically generating the 'selected' attribute for the <option> tag for call queue selection

    $optionCallQueueArray = []; // declares an empty array 

    foreach ($arrayOfCurrentCallQueues as $index => $callQueue){ // for every call queue in the department

        if ($callQueue["queue_name"] == $_SESSION["currentCallQueue"]["queue_name"]){ // if the name of the call queue is the same to the one being currently displayed
            $isSelected = "selected";
        } else { // otherwise
            $isSelected = "";
        }
        $optionCallQueueArray[] = "<option $isSelected value=\"$index\">{$callQueue['queue_name']}</option>";  //progrmatically generating the <option> tag and placing it in the array             
    }

    $callQueueName = $_SESSION["currentCallQueue"]["queue_name"];

    $businessHourDayName = $_SESSION["currentBusinessHoursDay"]["day_name"];

    if (isset($_POST["save"])){ // if the user clicks save

        // gathers the relevant state selection for the users current selection
        $index = $_SESSION["deptIndex"];
        $callQueueIndex = $_SESSION["callQueueIndex"];
        $businessHoursDayIndex = $_SESSION["businessHoursDayIndex"];

        $filePath = "../model/fabricated_call_flow_data.json";

        // replacing the voicemail members with the new input
        $voicemailMembers = htmlentities($_REQUEST["voicemailMember"]);
        $voicemailMembersArray = explode("\n", $voicemailMembers);
        $voicemailMembersArray = array_map('trim', $voicemailMembersArray);

        $jsonData["company"]["departments"][$index]["auto_attendant"]["voicemail"]["members"] = $voicemailMembersArray;

        // replacing the auto attendant greeting with the new input
        $autoAttendantGreeting = htmlentities(trim($_REQUEST["greetingMessage"]));
        $jsonData["company"]["departments"][$index]["auto_attendant"]["aa_greeting"] = $autoAttendantGreeting;

        // replacing the time details with the new inputs
        $fromTimeStart = htmlentities($_REQUEST["fromTimeStart"]);
        $jsonData["company"]["departments"][$index]["auto_attendant"]["business_hours"]["days"][$businessHoursDayIndex]["from_time_start"] = $fromTimeStart;

        $fromTimeEnd = htmlentities($_REQUEST["fromTimeEnd"]);
        $jsonData["company"]["departments"][$index]["auto_attendant"]["business_hours"]["days"][$businessHoursDayIndex]["from_time_end"] = $fromTimeEnd;

        // replacing the call queue members with the new members
        $callQueueMembers = htmlentities($_REQUEST["callQueueMembers"]);
        $callQueueMembers = explode("\n", $callQueueMembers);
        $callQueueMembersArray = array_map('trim', $callQueueMembers);

        $jsonData["company"]["departments"][$index]["auto_attendant"]["call_queues"][$callQueueIndex]["group"]["members"] = $callQueueMembersArray;

        // replacing the waiting time amount with the new time
        $waitingTimeAmount = htmlentities($_REQUEST["waitingTimeAmount"]);
        $jsonData["company"]["departments"][$index]["auto_attendant"]["call_queues"][$callQueueIndex]["answer_timeout_seconds"] = $waitingTimeAmount;

        // replacing the maximum call queue limit amount with the new limit
        $maxCallsAmount = htmlentities($_REQUEST["maximumCallQueueLimit"]);
        $jsonData["company"]["departments"][$index]["auto_attendant"]["call_queues"][$callQueueIndex]["max_calls"] = $maxCallsAmount;

        // reindexting the call queues
        $jsonData["company"]["departments"][$index]["auto_attendant"]["call_queues"] = array_values(
            $jsonData["company"]["departments"][$index]["auto_attendant"]["call_queues"]
        );

        // reindexing the departments
        $jsonData["company"]["departments"] = array_values(
            $jsonData["company"]["departments"]
        );

        // converts back to JSON and puts it back into the file
        $toPutInFile = json_encode($jsonData, JSON_PRETTY_PRINT);
        file_put_contents($filePath, $toPutInFile);

        createNewAuditLog($_SESSION["loggedInEmployee"]->EmployeeID, date("Y-m-d"), date("H:i:s"), $departmentName . "Call Flow Updated", "Admin updated " . $departmentName . "'s Call Flow");

        $_SESSION["callFlowEdited"] = true;

        doLogicAndCallIndexView();
        require_once("../view/indexView.php");

    } else if (!isset($_POST["save"])){ // if they havn't clicked save
        
        require_once("../view/editCallFlowView.php");
    }
}
?>