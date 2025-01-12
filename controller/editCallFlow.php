<?php

require_once("../model/fetchJsonData.php"); 

session_start();

$jsonData = getCallData();

$arrayOfDepartments = $jsonData['company']['departments']; // puts the array of departments into a variable
$departmentName = $_SESSION["department"]['name'];

$arrayOfCurrentCallQueues = $_SESSION["department"]["auto_attendant"]["call_queues"];


if (!isset($_SESSION["department"])) {
    $_SESSION["department"] = $arrayOfDepartments[0]; // default to the first department
}

if (!isset($_SESSION["currentCallQueue"])) {
    $_SESSION["currentCallQueue"] = $arrayOfCurrentCallQueues[0]; // default to the first department
}

if (isset($_POST['indexCallQueueSelection'])) {
    $CallQueueIndex = (int)$_REQUEST['indexCallQueueSelection']; // obtains the index of the selected department
    $_SESSION["currentCallQueue"] = $arrayOfCurrentCallQueues[$CallQueueIndex]; // uses the index to select the department
    $_SESSION["callQueueIndex"] = $CallQueueIndex;

    $callQueueMembers = implode("\n", $_SESSION["currentCallQueue"]["group"]["members"]);
    $waitingTimeAmount = $_SESSION["currentCallQueue"]["answer_timeout_seconds"];
    $maxCallsAmount = $_SESSION["currentCallQueue"]["max_calls"];
} else {
    $callQueueMembers = implode("\n", $arrayOfCurrentCallQueues[0]["group"]["members"]);
    $waitingTimeAmount = $arrayOfCurrentCallQueues[0]["answer_timeout_seconds"];
    $maxCallsAmount = $arrayOfCurrentCallQueues[0]["max_calls"];
}

if (isset($_POST['save'])) {

    $index = $_SESSION['deptIndex'];
    echo $index;
    $callQueueIndex = $_SESSION["callQueueIndex"];
    echo $callQueueIndex;

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

    $toPutInFile = json_encode($jsonData, JSON_PRETTY_PRINT);
    file_put_contents($filePath, $toPutInFile);
} 

    $callQueueName = $_SESSION["currentCallQueue"]["queue_name"];

    $voicemailMembers = implode("\n", $_SESSION["department"]["auto_attendant"]["voicemail"]["members"]);

    $auto_attendantGreeting =  $_SESSION["department"]["auto_attendant"]["aa_greeting"];


    $businessHoursFromStartDay =  $_SESSION["currentCallQueue"]["queue_name"];

    $fromTimeStart = $_SESSION["department"]["auto_attendant"]["business_hours"]["from_time_start"];
    $fromTimeEnd = $_SESSION["department"]["auto_attendant"]["business_hours"]["from_time_end"];

    $toTimeStart = $_SESSION["department"]["auto_attendant"]["business_hours"]["to_time_start"];
    $toTimeEnd = $_SESSION["department"]["auto_attendant"]["business_hours"]["to_time_end"];

    $callQueueFromStartDay = $_SESSION["department"]["auto_attendant"]["business_hours"]["from_day_start"];
    $callQueueToEndDay = $_SESSION["department"]["auto_attendant"]["business_hours"]["to_day_end"];


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

    require_once("../view/editCallFlowView.php");
?>