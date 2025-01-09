<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../view/css_js_images/style.css"> <!-- linking to style sheet -->
        <script src="../@joint/core/dist/joint.js"></script> <!-- linking to JointJS library -->
        <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script> <!-- linking to j query -->
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

            <h1>Editing <?= $departmentName ?>'s Call Flow </h1>
        
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

        <div id="smallCanvas" onclick="fullView()"></div>

        <div id="canvas-modal"> <!-- initially hidden -->
            <div id="bigCanvas">
            </div>
            <button id="close-modal" onclick="closeBigCanvas()">Close</button>
        </div>
    </body>
</html>