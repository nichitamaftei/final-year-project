<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../view/css_js_images/style.css"> <!-- linking to style sheet -->
        <script src="../@joint/core/dist/joint.js"></script> <!-- linking to JointJS library -->
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script> <!-- linking to j query -->
        <script type="text/javascript" src="../view/css_js_images/javascript.js"></script> <!-- linking to javascript -->
        <title> Historical Call Flows </title>
    </head>

    <body id="historicalCallFlowsView">
        <!-- top bar of the screen -->
        <section id="topBar">
            <h1 id="topBarTitleHistoricalCallFlows"> <?= $departmentName ?> Historical Call Flows</h1>

            <form method="post" action="../controller/historicalCallFlows.php"> 
                <button name="historicalCallFlowGoBackButton" type="submit" id="historicalCallFlowGoBackButton"> <img class="backbuttonIcon"src="../view/css_js_images/backButtonIcon.png" alt=""> </button>
            </form>
        </section>   

        <table class="table" id="historicalCallFlowsTable">
            <thead>
                <tr class="tableRow">
                    <th> 
                        <form method="post" id="historicalFlowDateFilteringForm" action="../controller/historicalCallFlows.php">
                            <input type="hidden" name="historicalFlowDateFilterForm" value="toggle">
                            <div id="historicalFlowDateFilteringContainer" onclick="submitHistoricalFlowDateFilterForm()">
                                <p> Date </p> 
                                <img src="../view/css_js_images/filterOffIcon.png" id="historicalFlowDateFilterOffIcon" class="filterIcon" alt="">
                                <img src="../view/css_js_images/filterIcon.png" id="historicalFlowDateFilterIcon" class="filterIcon" alt="">
                                <img src="../view/css_js_images/upArrowIcon.png" id="historicalFlowDateUpIcon" class="filterIcon" alt="">
                                <img src="../view/css_js_images/downArrowIcon.png" id="historicalFlowDateDownIcon" class="filterIcon" alt="">
                            </div>
                        </form>
                    </th>
                    <th> 
                        <form method="post" id="historicalFlowTimeFilteringForm" action="../controller/historicalCallFlows.php">
                            <input type="hidden" name="historicalFlowTimeFilterForm" value="toggle">
                            <div id="historicalFlowTimeFilteringContainer" onclick="submitHistoricalFlowTimeFilterForm()">
                                <p> Time </p> 
                                <img src="../view/css_js_images/filterOffIcon.png" id="historicalFlowTimeFilterOffIcon" class="filterIcon" alt="">
                                <img src="../view/css_js_images/filterIcon.png" id="historicalFlowTimeFilterIcon" class="filterIcon" alt="">
                                <img src="../view/css_js_images/upArrowIcon.png" id="historicalFlowTimeUpIcon" class="filterIcon" alt="">
                                <img src="../view/css_js_images/downArrowIcon.png" id="historicalFlowTimeDownIcon" class="filterIcon" alt="">
                            </div>
                        </form>
                    </th>
                    <th> 
                        <p> File Name (Download Image) </p> 
                    </th>
                    <th> 
                        <form method="post" id="historicalFlowModifiedByFilteringForm" action="../controller/historicalCallFlows.php">
                            <input type="hidden" name="historicalFlowModifiedByFilterForm" value="toggle">
                            <div id="historicalFlowModifiedByFilteringContainer" onclick="submitHistoricalFlowModifiedByFilterForm()">
                                <p> ModifiedBy </p> 
                                <img src="../view/css_js_images/filterOffIcon.png" id="historicalFlowModifiedByFilterOffIcon" class="filterIcon" alt="">
                                <img src="../view/css_js_images/filterIcon.png" id="historicalFlowModifiedByFilterIcon" class="filterIcon" alt="">
                                <img src="../view/css_js_images/upArrowIcon.png" id="historicalFlowModifiedByUpIcon" class="filterIcon" alt="">
                                <img src="../view/css_js_images/downArrowIcon.png" id="historicalFlowModifiedByDownIcon" class="filterIcon" alt="">
                            </div>
                        </form>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($images as $image): ?>
                    <?php $employee = $pdoSingleton->getEmployeeByID($image->EmployeeID) ?>
                    <tr class="tableRowLogs"> 
                        <th class="selectedLogsTH"> <?= $image->dateModified ?> </th>
                        <th class="selectedLogsTH"> <?= $image->timeModified ?> </th>
                        <th class="selectedLogsTH"> <a href="data:image/png;base64,<?= $image->getBase64Image() ?>" download="<?= $departmentName ?>_call_flow_<?= $image->imageID ?>.png" id="historicalCallFlowDownloadLink"> <?= $departmentName . "_call_flow_" . $image->imageID . ".png" ?> </a> </th>
                        <th class="selectedLogsTH"> <?= $employee->FirstName ?>  <?= $employee->LastName ?></th>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div id="addingMarginToBottom">

        </div>
    </body>
</html>