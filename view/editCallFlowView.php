<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../view/css_js_images/style.css"> <!-- linking to style sheet -->
        <script src="../@joint/core/dist/joint.js"></script> <!-- linking to JointJS library -->
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script> <!-- linking to j query -->
        <script type="text/javascript" src="../view/css_js_images/javascript.js"></script> <!-- linking to javascript -->
        <title>Index</title>
    </head>

    <body id="editCallFlowView">

        <section id='top-bar'>

            <div id="hamburger-container">
                <button class="hamburger" onclick="showMenu()">
                    <img id="menu-icon"src="../view/css_js_images/burgerMenuIcon.png" alt="">
                </button>
            </div>

            <h1 id="editingMainTitle">Editing <?= $departmentName ?>'s Call Flow </h1>
        
            <div id="side-menu">
                <ul id="unordered-list">
                    
                    <li class="list-items">
                        <button class="backButton" onclick="hideMenu()">
                            <img id="backbutton-icon"src="../view/css_js_images/backButtonIcon.png" alt="">
                        </button>
                    </li>

                    <?php foreach ($arrayOfDepartments as $index => $department): ?>   <!-- for every department, create links -->
                        <form method="POST" action="index.php">
                            <li class="list-items">
                                <button type="submit" name="dept" value="<?= $index; ?>" class="departmentButtons">
                                    <?= $department['name']; ?>
                                </button>
                            </li>
                        </form>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>   

        <div id="editingCanvasContainer">
            <div id="editingCanvas" onclick="fullView()"></div>
        </div>
        
        <div id="canvas-modal"> <!-- initially hidden -->
            <div id="bigCanvas">
            </div>
            <button id="close-modal" onclick="closeBigCanvas()">Close</button>
        </div>

        <form id="callQueueSelectorForm" method="post" action="editCallFlow.php"></form>
        <form id="editingCallFlowForm"  method="post" action="editCallFlow.php"></form>

        <div id="everything">

            <div class="topLevelContainer">

                <div class="title">
                    <p><b> Auto-Attendant Details: </b></p>
                </div>

                <div class="subLevelContainer">
                    <p class="editingTitle"> <b> Voicemail Members: </b> </p>
                    <textarea name="voicemailMember" rows="4" cols="50" class="textArea" form="editingCallFlowForm"><?= $voicemailMembers ?></textarea>
                </div>

                <div class="subLevelContainer">
                    <p class="editingTitle"> <b> Greeting Meessage: </b> </p>
                    <textarea name="greetingMessage" rows="4" cols="50" class="textArea" form="editingCallFlowForm"><?=$auto_attendantGreeting ?></textarea>
                </div>

                <div class="subLevelContainer">

                    <p class="editingTitle"><b>Business Hours:</b> </p>

                    <div class="businessHoursTopContainer">
                        <div class="businessHoursSubContainer">
                            <label for="businessStartDay">From:</label>
                            <select id="selectStyle" name="dayStart" class="selectionStyling" form="editingCallFlowForm">
                                <?php foreach ($optionDayStartArray as $option): ?>
                                    <?= $option ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="businessHoursSubContainer">
                            <label for="startTime">Start Time:</label>
                            <input type="time" name="fromTimeStart" id="startTime" value="<?= $fromTimeStart ?>" class="selectionStyling" form="editingCallFlowForm">
                        </div>

                        <div class="businessHoursSubContainer">
                            <label for="endTime">End Time:</label>
                            <input type="time" name="fromTimeEnd" id="endTime" value="<?= $fromTimeEnd ?>" class="selectionStyling" form="editingCallFlowForm">
                        </div>
                    </div>

                    <div class="businessHoursTopContainer">
                        <div class="businessHoursSubContainer">
                            <label for="businessEndDay">To:</label>
                            <select id="selectStyle" name="dayEnd" class="selectionStyling" form="editingCallFlowForm">
                                <?php foreach ($optionDayEndArray as $option): ?>
                                        <?= $option ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="businessHoursSubContainer">
                            <label for="endStartTime">Start Time:</label>
                            <input type="time" name="toTimeStart" id="endStartTime" value="<?= $toTimeStart ?>" class="selectionStyling" form="editingCallFlowForm">
                        </div>

                        <div class="businessHoursSubContainer">
                            <label for="endEndTime">End Time:</label>
                            <input type="time" name="toTimeEnd" id="endEndTime" value="<?= $toTimeEnd ?>" class="selectionStyling" form="editingCallFlowForm">
                        </div>
                    </div>
                </div>

            </div>

            <div class="topLevelContainer">

                <div class="title">
                    <p><b> Call Queue Details: </b></p>
                </div>

                <div class="subLevelContainer">

                    <p class="editingTitle"><b>Currently Viewing <?= $callQueueName ?> CQ</b> </p>

                    <p class="editingTitle"> Select which Call Queue you'd like to view </p>
                    
                    <div class="selectionContainer">
                        <select id="selectStyle" name="indexCallQueueSelection" form="callQueueSelectorForm">
                            <?php foreach ($optionCallQueueArray as $callQueue): ?>   <!-- for every call queue in the current department, create selection -->
                                    <?= $callQueue ?>   
                            <?php endforeach; ?>
                        </select>
                        <button id="buttonStyle" type="submit" form="callQueueSelectorForm"> Select </button>
                    </div>
                    
                </div>

                <div class="subLevelContainer">

                    <p class="editingTitle"><b><?= $callQueueName ?> CQ Members:</b> </p>

                    <textarea name="callQueueMembers" rows="2" cols="50"  class="textArea" form="editingCallFlowForm"><?= $callQueueMembers ?></textarea>
                </div>
                
                <div class="subLevelContainer">
                    <p class="editingTitle"> <b> Maximum Waiting Time for a Call: </b></p>
                    <div class="inputContainer">
                        <input name="waitingTimeAmount" class="inputStyling" value="<?= $waitingTimeAmount ?>" form="editingCallFlowForm">  </input>
                        <p class="inputText"> seconds </p>
                    </div>
                </div>
                
                <div class="subLevelContainer">
                    <p class="editingTitle"> <b> Maximum Call Queue Limit: </b></p>
                    <div class="inputContainer">
                        <input name="maximumCallQueueLimit" class="inputStyling" value="<?=$maxCallsAmount?>" form="editingCallFlowForm">  </input>
                        <p class="inputText"> calls </p>
                    </div>
                    
                </div>
            </div>
        </div>

        <div>
            <div id="editButtons">
                <button id="cancelEditButton" class="button" name="cancel" type="submit" form="editingCallFlowForm" > Cancel </button>

                <button id="saveEditButton" class="button" name="save" type="submit" form="editingCallFlowForm"> Save </button>
            </div>
        </div>
    </body>
</html>