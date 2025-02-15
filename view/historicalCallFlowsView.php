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

        <table class="table">
            <thead>
                <tr class="tableRow">
                    <th> 
                        <p> Date </p> 
                    </th>
                    <th> 
                        <p> Time </p> 
                    </th>
                    <th> 
                        <p> File Name (Download Image) </p> 
                    </th>
                    <th> 
                        <p> Modified By </p> 
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
    </body>
</html>