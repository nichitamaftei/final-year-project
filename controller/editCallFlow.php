<?php

require_once("../model/fetchJsonData.php"); 
require_once("../model/utilities.php");

session_start();

if (isset($_POST['cancel'])){
        
    doLogicAndCallIndexView();
    require_once("../view/indexView.php");
} else {

    $jsonData = getCallData(); // gathers the call data

    $arrayOfDepartments = $jsonData['company']['departments']; // puts the array of departments into a variable
    $departmentName = $_SESSION["department"]['name']; // sets the departments name in a variable

    $arrayOfCurrentCallQueues = $_SESSION["department"]["auto_attendant"]["call_queues"];
    $_SESSION["currentCallQueue"] = $arrayOfCurrentCallQueues[0]; // default to the first department 
    
    // dynamically setting the call queue's details
    
    if (isset($_POST['indexCallQueueSelection'])){ // if a call queue selection is made
        $CallQueueIndex = (int)$_REQUEST['indexCallQueueSelection']; // obtains the index of the selected department
        $_SESSION["currentCallQueue"] = $arrayOfCurrentCallQueues[$CallQueueIndex]; // uses the index to select the department
        $_SESSION["callQueueIndex"] = $CallQueueIndex; // sets the index to a session variable

        // set the variables to have the data from the selected cq
        $callQueueMembers = implode("\n", $_SESSION["currentCallQueue"]["group"]["members"]); 
        $waitingTimeAmount = $_SESSION["currentCallQueue"]["answer_timeout_seconds"]; 
        $maxCallsAmount = $_SESSION["currentCallQueue"]["max_calls"];

    } else{ // otherwise set the view to have the first call queue's attributes in the array
        $_SESSION["callQueueIndex"] = 0;
        $callQueueMembers = implode("\n", $arrayOfCurrentCallQueues[0]["group"]["members"]);
        $waitingTimeAmount = $arrayOfCurrentCallQueues[0]["answer_timeout_seconds"];
        $maxCallsAmount = $arrayOfCurrentCallQueues[0]["max_calls"];
    }

    // setting the auto attendant details

    $voicemailMembers = implode("\n", $_SESSION["department"]["auto_attendant"]["voicemail"]["members"]);

    $auto_attendantGreeting =  $_SESSION["department"]["auto_attendant"]["aa_greeting"];

    $fromTimeStart = $_SESSION["department"]["auto_attendant"]["business_hours"]["from_time_start"];
    $fromTimeEnd = $_SESSION["department"]["auto_attendant"]["business_hours"]["from_time_end"];

    $toTimeStart = $_SESSION["department"]["auto_attendant"]["business_hours"]["to_time_start"];
    $toTimeEnd = $_SESSION["department"]["auto_attendant"]["business_hours"]["to_time_end"];

    $callQueueFromStartDay = $_SESSION["department"]["auto_attendant"]["business_hours"]["from_day_start"];
    $callQueueToEndDay = $_SESSION["department"]["auto_attendant"]["business_hours"]["to_day_end"];


    // dynamically generating the 'selected' attribute for the <option> tag for business hours

    $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

    $optionDayStartArray = [];
    $optionDayEndArray = [];

    foreach ($days as $day) {

        if ($day == $callQueueFromStartDay){
            $isSelectedStart = "selected";
        } else{
            $isSelectedStart = "";
        }
        
        if ($day == $callQueueToEndDay){
            $isSelectedEnd = "selected";
        } else{
            $isSelectedEnd = "";
        }
        $optionDayStartArray[] = "<option value=\"$day\" $isSelectedStart form=\"editingCallFlowForm\">$day</option>";
        $optionDayEndArray[] = "<option value=\"$day\" $isSelectedEnd form=\"editingCallFlowForm\">$day</option>";
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

    if (isset($_POST['save'])){ // if the user clicks save
        $index = $_SESSION['deptIndex'];
        $callQueueIndex = $_SESSION["callQueueIndex"];

        $filePath = '../model/fabricated_call_flow_data.json';

        $voicemailMembers = $_REQUEST['voicemailMember'];
        $voicemailMembers = preg_replace('/\s+/', ' ', trim($voicemailMembers));
        $voicemailMembersArray = explode(' ', $voicemailMembers);

        $jsonData["company"]["departments"][$index]["auto_attendant"]["voicemail"]["members"] = $voicemailMembersArray;

        $autoAttendantGreeting = trim($_REQUEST['greetingMessage']);
        $jsonData["company"]["departments"][$index]["auto_attendant"]["aa_greeting"] = $autoAttendantGreeting;

        $dayStart = $_REQUEST['dayStart'];
        $jsonData["company"]["departments"][$index]["auto_attendant"]["business_hours"]["from_day_start"] = $dayStart;

        $fromTimeStart = $_REQUEST['fromTimeStart'];
        $jsonData["company"]["departments"][$index]["auto_attendant"]["business_hours"]["from_time_start"] = $fromTimeStart;

        $fromTimeEnd = $_REQUEST['fromTimeEnd'];
        $jsonData["company"]["departments"][$index]["auto_attendant"]["business_hours"]["from_time_end"] = $fromTimeEnd;

        $dayEnd = $_REQUEST['dayEnd'];
        $jsonData["company"]["departments"][$index]["auto_attendant"]["business_hours"]["to_day_end"] = $dayEnd;

        $toTimeStart = $_REQUEST['toTimeStart'];
        $jsonData["company"]["departments"][$index]["auto_attendant"]["business_hours"]["to_time_start"] = $toTimeStart;

        $toTimeEnd = $_REQUEST['toTimeEnd'];
        $jsonData["company"]["departments"][$index]["auto_attendant"]["business_hours"]["to_time_end"] = $toTimeEnd;

        $callQueueMembers = $_REQUEST['callQueueMembers'];
        $callQueueMembers = preg_replace('/\s+/', ' ', trim($callQueueMembers));
        $callQueueMembersArray = explode(' ', $callQueueMembers);
        $jsonData["company"]["departments"][$index]["auto_attendant"]["call_queues"][$callQueueIndex]["group"]["members"] = $callQueueMembersArray;

        $waitingTimeAmount = $_REQUEST['waitingTimeAmount'];
        $jsonData["company"]["departments"][$index]["auto_attendant"]["call_queues"][$callQueueIndex]["answer_timeout_seconds"] = $waitingTimeAmount;

        $maxCallsAmount = $_REQUEST['maximumCallQueueLimit'];
        $jsonData["company"]["departments"][$index]["auto_attendant"]["call_queues"][$callQueueIndex]["max_calls"] = $maxCallsAmount;

        $jsonData["company"]["departments"][$index]["auto_attendant"]["call_queues"] = array_values(
            $jsonData["company"]["departments"][$index]["auto_attendant"]["call_queues"]
        );
        $jsonData["company"]["departments"] = array_values(
            $jsonData["company"]["departments"]
        );

        $toPutInFile = json_encode($jsonData, JSON_PRETTY_PRINT);
        file_put_contents($filePath, $toPutInFile);
        
        doLogicAndCallIndexView();
        require_once("../view/indexView.php");

    } else if (!isset($_POST['save'])){ // if they havn't clicked save
        
        require_once("../view/editCallFlowView.php");

    }
}


?>