<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../view/css_js_images/style.css"> <!-- linking to style sheet -->
        <script src="../@joint/core/dist/joint.js"></script> <!-- linking to JointJS library -->
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script> <!-- linking to j query -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script> <!-- linking to chart.js -->
        <script type="text/javascript" src="../view/css_js_images/javascript.js"></script> <!-- linking to javascript -->
        <title> Index </title>
    </head>

    <body id="indexView">
        <!-- top bar of the screen -->
        <section id="topBar">

            <div id="hamburgerContainer">
                <button class="hamburger" onclick="showMenu()">
                    <img id="menuIcon"src="../view/css_js_images/burgerMenuIcon.png" alt="">
                </button>
            </div>

            <div id="helpIconContainer">
                <form method="post" action="../controller/helpPage.php">
                    <button type="submit" id="helpPageButton">
                        <img id="helpIcon" src="../view/css_js_images/helpIcon.png" alt="Help">
                    </button>
                </form>
            </div>
            

            <div id="topBarTitleContainer">
                <h1 id="topBarTitle"><?= $departmentName ?> </h1>
            </div>

            <div id="topBarButtonContainer">

                <form method="post" action="index.php"> 
                    <input type="hidden" name="signOut" value="true">
                    <button type="submit" id="signOut"> Sign Out </button>
                </form>

                <?php if ($_SESSION['loggedInEmployee']->isAdmin == 1):   ?>
                    <a href="../controller/admin.php" id="adminLink"> Admin Area </a>
                <?php endif; ?>  

            </div>

            <div id="sideMenu">
                <ul id="unorderedList">
                    <li class="listItems">
                        <button id="sideMenuBackButton" onclick="hideMenu()">
                            <img class="backbuttonIcon"src="../view/css_js_images/backButtonIcon.png" alt="">
                        </button>
                    </li>

                    <?php foreach ($arrayOfDepartments as $index => $department): ?>   <!-- for every department, create links -->
                        <form method="POST" action="index.php">
                            <li class="listItems">
                                <button type="submit" name="dept" value="<?= $index; ?>" class="departmentButtons">
                                    <?= $department['name']; ?>
                                </button>
                            </li>
                        </form>
                    <?php endforeach; ?>
                </ul>
            </div>    
        </section>   

        <div id="container">

            <div id="topRowContainer">

                <div id="smallCanvasContainer">
                    <div id="callFlowTitleContainer">
                        <h3 id="callFlowDiagramTitle"> <?= $departmentName . " Call Flow Diagram:" ?> </h3>
                    </div>
                    
                    <div id="smallCanvas" onclick="fullView()"></div>
                </div>

                <div id="topCallersContainer">

                    <div id="topCallerTitleContainer">
                        <h3 id="topCallersTitle"> Top 5 Callers </h3>
                    </div>
                    
                    <div id="topCallersSubContainer">
                        <table id="topCallersTable">
                            <thead>
                                <tr>
                                    <th> <p> Number </p> </th>
                                    <th> <p> Amount </p> </th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                <?php foreach ($topNumbers as $number => $totalCalls): ?>
                                    <tr>
                                        <td> <?= $number ?> </td>
                                        <td> <?= $totalCalls ?> </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>    
            </div>

            <div id="bottomRowContainer">

                <div id="callMetricsContainer">

                    <div id="callMetricsTitleContainer">
                        <h3 id="callMetricsTitle"> Call Metrics (All Time) </h3>
                    </div>

                    <div id="metricLayout">
                        <div class="callMetricsSubContainer">
                            <div class="metricBox">
                                <p class="metricTitle"> Incoming Calls </p>
                                <div class="metricItem">
                                    <p>Total</p>
                                    <p class="metricValue"> <?= $totalCallers ?> calls </p>
                                </div>
                                <div class="metricItem">
                                    <p> Avg Wait Time </p>
                                    <p class="metricValue"> <?= round($averageWaitTime) ?> seconds </p>
                                </div>
                            </div>

                            <div class="metricBox">
                                <p class="metricTitle"> Abandoned Calls </p>
                                <div class="metricItem">
                                    <p>Total</p>
                                    <p class="metricValue"> <?= $totalAbandondedCalls ?> calls </p>
                                </div>
                                <div class="metricItem">
                                    <p>Abandoned Rate</p>
                                    <p class="metricValue"> <?= round($abandondedRate) ?>% </p>
                                </div>
                            </div>
                        </div>

                        <canvas id="callMetricsBarChart" style="max-width:500px"></canvas>

                        <div class="callMetricsSubContainer">
                            <div class="metricBox">
                                <p class="metricTitle"> Service Target Level </p>
                                <div class="metricItem">
                                    <p>Target</p>
                                    <p class="metricValue" id="smallFont"> 80%</p>
                                </div>
                                <div class="metricItem">
                                    <p> Actual </p>
                                    <p class="metricValue"> <?= $actualServiceLevelPercentage ?>% </p>
                                </div>
                            </div>

                            <div class="metricBox">
                                <p class="metricTitle"> Extra Metrics </p>
                                <div class="metricItem">
                                    <p>Answered Calls</p>
                                    <p class="metricValue"> <?= $totalAnsweredCalls ?> </p>
                                </div>
                                <div class="metricItem">
                                    <p> Longest Waiting Time </p>
                                    <p class="metricValue"> <?= $longestWaitTime ?> sec </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="additionalFuncContainer">

                    <div id="additionalFuncTitleContainer">
                        <h3 id="addtionalFuncTitle"> Additional Functionality </h3>
                    </div>
                        
                    <div id="buttons">
                        <a href="../controller/historicalCallFlows.php"> <button class="button"> View Historical Call Flows </button>  </a>
                        <button class="button" onclick="downloadDiagramAsPNG()"> Download Call Diagram </button>
                        <button class="button" onclick="downloadCallMetrics()"> Download Metrics </button>
                        <?php if ($_SESSION["loggedInEmployee"]->isAdmin == 1): ?>
                            <div>   
                                <a href="../controller/editCallFlow.php"> <button id="editButton"> Edit Call Flow </button> </a>
                            </div>
                        <?php endif; ?>  
                    </div>
                </div>
            </div>

            <div id="canvasModal"> <!-- initially hidden -->
                <div id="bigCanvas">
                </div>
                <button id="zoomIn">
                    <img id="zoomInImg" src="../view/css_js_images/zoomInIcon.png">
                </button>
                <button id="zoomOut">
                    <img id="zoomOutImg" src="../view/css_js_images/zoomOuticon.png">
                </button>
                <button id="closeModal" onclick="closeBigCanvas()"> Close </button>
                <button id="resetModal" onclick="resetModal()" > Reset </button>
            </div>
        </div>
    </body>
</html>