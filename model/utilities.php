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
    } else {
        $deptIndex = 0; // obtains the index of the selected department
        $_SESSION["department"] = $arrayOfDepartments[$deptIndex]; // uses the index to select the department
        $_SESSION["deptIndex"] = $deptIndex;
    }
    

    $departmentName = $_SESSION["department"]['name']; // displays the currently selected department for debugging purposes

    require_once("../view/indexView.php");
}

function doLogicAndCallEditView(){

    if (isset($_POST['cancel'])){
        
        doLogicAndCallIndexView();
        require_once("../view/indexView.php");
    } else {

        $jsonData = getCallData();

        $arrayOfDepartments = $jsonData['company']['departments']; // puts the array of departments into a variable
        $departmentName = $_SESSION["department"]['name'];

        $arrayOfCurrentCallQueues = $_SESSION["department"]["auto_attendant"]["call_queues"];
        $_SESSION["currentCallQueue"] = $arrayOfCurrentCallQueues[0]; // default to the first department   
        
        if (isset($_POST['indexCallQueueSelection'])) {
            $CallQueueIndex = (int)$_REQUEST['indexCallQueueSelection']; // obtains the index of the selected department
            $_SESSION["currentCallQueue"] = $arrayOfCurrentCallQueues[$CallQueueIndex]; // uses the index to select the department
            $_SESSION["callQueueIndex"] = $CallQueueIndex;

            $callQueueMembers = implode("\n", $_SESSION["currentCallQueue"]["group"]["members"]);
            $waitingTimeAmount = $_SESSION["currentCallQueue"]["answer_timeout_seconds"];
            $maxCallsAmount = $_SESSION["currentCallQueue"]["max_calls"];
        } else {
            $_SESSION["callQueueIndex"] = 0;
            $callQueueMembers = implode("\n", $arrayOfCurrentCallQueues[0]["group"]["members"]);
            $waitingTimeAmount = $arrayOfCurrentCallQueues[0]["answer_timeout_seconds"];
            $maxCallsAmount = $arrayOfCurrentCallQueues[0]["max_calls"];
        }

    
        if (!isset($_POST['save'])){
    
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

            $optionCallQueueArray = [];

            foreach ($arrayOfCurrentCallQueues as $index => $callQueue){

                if ($callQueue["queue_name"] == $_SESSION["currentCallQueue"]["queue_name"]){
                    $isSelected = "selected";
                } else {
                    $isSelected = "";
                }
                $optionCallQueueArray[] = "<option $isSelected value=\"$index\">{$callQueue['queue_name']}</option>";               
            }

            $callQueueName = $_SESSION["currentCallQueue"]["queue_name"];

            require_once("../view/editCallFlowView.php");

        } else if (isset($_POST['save'])){
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

        } 
    }
}
?>