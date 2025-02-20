<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <link rel="icon" href="../view/css_js_images/Phone_icon.png">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../view/css_js_images/style.css"> <!-- linking to style sheet -->
        <script src="../@joint/core/dist/joint.js"></script> <!-- linking to JointJS library -->
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script> <!-- linking to j query -->
        <script type="text/javascript" src="../view/css_js_images/javascript.js"></script> <!-- linking to javascript -->
        <title> Help Page</title>
    </head>

    <body id="helpPageView">
        <!-- top bar of the screen -->
        <section id="topBar">
            
        
            <form method="post" action="../controller/helpPage.php"> 
                <button name="helpPageGoBackButton" type="submit" id="helpPageGoBackButton"> 
                    <img class="backbuttonIcon"src="../view/css_js_images/backButtonIcon.png" alt=""> 
                </button>
            </form>

            <h1 id="topBarTitleHelpPage"> Help Page</h1>
        </section>   


        <div id="helpContainer">

            <div class="helpSection">

                <h2 class="helpCategory"> Call Metrics </h2>

                <div class="helpContent">
                    <div class="helpTitle">
                        <p> Service Target Level </p>
                    </div>

                    <div class="helpExplanation">
                        <p> A performance goal specifying the percentage of calls that should be answered within a set timeframe. The default target set for every department is 80% of calls answered within 30 seconds </p>
                    </div>
                </div>

                <div class="helpContent">

                    <div class="helpTitle">
                        <p> Abandonment Rate </p>
                    </div>

                    <div class="helpExplanation">
                        <p> The percentage of callers who hang up before reaching an agent. This is often due to long wait times. </p>
                    </div>

                </div>

            </div>


            <div class="helpSection">

                <h2 class="helpCategory"> Call Flow </h2>

                <div class="helpContent">
                    <div class="helpTitle">
                        <p> Call Routing </p>
                    </div>

                    <div class="helpExplanation">
                        <p> The process of directing incoming calls to the most appropriate agent or department based on predefined rules, such as skill-based routing, time of day, or caller input.  </p>
                    </div>
                </div>

                <div class="helpContent">

                    <div class="helpTitle">
                        <p> Call Queue </p>
                    </div>

                    <div class="helpExplanation">
                        <p> A system that places incoming calls in a virtual line until an available agent can answer. Call queues help manage high call volumes by ensuring callers are connected in the order they arrive. </p>
                    </div>
                </div>

                <div class="helpContent">

                    <div class="helpTitle">
                        <p> Auto Attendant </p>
                    </div>

                    <div class="helpExplanation">
                        <p> This is an automated system that provides a list of Call Wueues for the user to proceed with in the form of a number input </p>
                    </div>
                </div>


                <div class="helpContent">

                    <div class="helpTitle">
                        <p> Voicemail Group </p>
                    </div>

                    <div class="helpExplanation">
                        <p> This is Microsft 365 group which contains members that have access to receieve voicemails left by callers  </p>
                    </div>
                </div>

            </div>





            <div class="helpSection">

                <h2 class="helpCategory"> Routing Types </h2>

                <div class="helpContent">

                    <div class="helpTitle">
                        <p> Round Robin</p>
                    </div>

                    <div class="helpExplanation">
                        <p> A call distribution method where incoming calls are assigned to agents in a rotational order. This ensures calls are distributed evenly between employees. </p>
                    </div>
                </div>


                <div class="helpContent">

                    <div class="helpTitle">
                        <p> Attendant Routing </p>
                    </div>

                    <div class="helpExplanation">
                        <p> Rings all agents in the queue simultaneously. The first Employee to answer handles the call. </p>
                    </div>
                </div>


                <div class="helpContent">

                    <div class="helpTitle">
                        <p> Serial Routing </p>
                    </div>

                    <div class="helpExplanation">
                        <p> Calls are routed in a sequential in a sort of top-down approach. If an agent doesn't answer, the call moves to the next agent in the list. </p>
                    </div>
                </div>


                <div class="helpContent">

                    <div class="helpTitle">
                        <p> Longest Idle </p>
                    </div>

                    <div class="helpExplanation">
                        <p> Routes calls to the agent who has been idle (available and not on a call) for the longest period of time. </p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>