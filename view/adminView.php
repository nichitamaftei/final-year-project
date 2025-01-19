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

    <body id="adminView">
        <!-- top bar of the screen -->
        <section id='top-bar'>
            <h1 id="top-bar-title">Admin Settings</h1>
        </section>   


        <table id="listOfEmployeesTable">
            <thead>
                <tr class="tableRow">
                    <th> <p> Name </p> </th>
                    <th> <p> E-mail </p> </th>
                    <th> <p> Last-log in</p> </th>
                    <th> <p> Security Role/s </p> </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employeeArray as $employee): ?>
                    <tr class="tableRow">
                        <th> <p> <?= $employee['employee']->FirstName ?> <?= $employee['employee']->LastName ?> </p> </th>
                        <th> <p> <?= $employee['employee']->Email ?> </p> </th>
                        <th> 
                            <p> 
                                <?php if ($employee['employee']->LastLogIn == '0000-00-00 00:00:00'):   ?>
                                   Never Logged In
                                <?php else: ?>
                                    <?= $employee['employee']->LastLogIn ?> 
                                <?php endif; ?>
                            </p> 
                        </th>
                        <th> 
                            <ul>
                                <?php foreach ($employee['roles'] as $role): ?>
                                    <li> <?= $role ?> </li>
                                <?php endforeach ?>
                            </ul> 
                        </th>
                        <th> 
                            <form method="post" action="../controller/basket.php"> 
                                <input type="hidden" name="cheeseIdToRemove" value=" <?= $orderCheese->orderCheeseId ?> ">
                                <button type="submit" id="remove"> Delete </button>
                            </form>
                        </th>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>   
    </body>
</html>