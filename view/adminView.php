<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <link rel="icon" href="../view/css_js_images/Phone_icon.png">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../view/css_js_images/style.css"> <!-- linking to style sheet -->
        <script src="../@joint/core/dist/joint.js"></script> <!-- linking to JointJS library -->
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script> <!-- linking to j query -->
        <script type="text/javascript" src="../view/css_js_images/adminJavascript.js"></script> <!-- linking to javascript -->
        <title>Admin Page</title>
    </head>

    <body id="adminView">
        <!-- top bar of the screen -->
        <section id='topBar'>
            <h1 id="topBarTitleAdmin">Admin Settings</h1>

            <form method="post" action="../controller/admin.php"> 
                <button name="adminGoBackButton" type="submit" id="adminBackButton"> <img class="backbuttonIcon"src="../view/css_js_images/backButtonIcon.png" alt=""> </button>
            </form>
        </section>   

        <div id="tabsContainer">
            <div id="tabs">
                <div class="tabButtons">
                    <form action="admin.php" id="tabForm">
                        <button type="submit" name="users" id="usersTabButton" value="users"> Users </button>
                        <button type="submit" name="logs" id="logsTabButton" value="logs"> Logs </button>
                    </form>
                </div>

                <div id="users" class="content">
                    <table class="table">
                        <thead>
                            <tr class="tableRow">
                                <th> 
                                    <form method="post" id="nameFilteringForm" action="../controller/admin.php">
                                        <input type="hidden" name="nameFilterForm" value="toggle">
                                        <div id="UsersNameFilteringContainer" onclick="submitNameFilterForm()">
                                            <p> Name </p> 
                                            <img src="../view/css_js_images/filterOffIcon.png" id="nameFilterOffIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/filterIcon.png" id="nameFilterIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/upArrowIcon.png" id="nameUpIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/downArrowIcon.png" id="nameDownIcon" class="filterIcon" alt="">
                                        </div>
                                    </form>
                                </th>
                                <th> 
                                    <form method="post" id="emailFilteringForm" action="../controller/admin.php">
                                        <input type="hidden" name="emailFilterForm" value="toggle">
                                        <div id="UsersEmailFilteringContainer" onclick="submitEmailFilterForm()">
                                            <p> E-mail </p> 
                                            <img src="../view/css_js_images/filterOffIcon.png" id="emailFilterOffIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/filterIcon.png" id="emailFilterIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/upArrowIcon.png" id="emailUpIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/downArrowIcon.png" id="emailDownIcon" class="filterIcon" alt="">
                                        </div>
                                    </form>
                                </th>
                                <th>
                                    <form method="post" id="logInFilteringForm" action="../controller/admin.php"> 
                                        <input type="hidden" name="logInFilterForm" value="toggle">
                                        <div id="UsersLogInFilteringContainer" onclick="submitLogInFilterForm()">
                                            <p> Last-log in</p> 
                                            <img src="../view/css_js_images/filterOffIcon.png" id="logInFilterOffIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/filterIcon.png" id="logInFilterIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/upArrowIcon.png" id="logInUpIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/downArrowIcon.png" id="logInDownIcon" class="filterIcon" alt="">
                                        </div>
                                    </form>
                                </th>
                                <th> <p> Security Role/s </p> </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($employeeArray as $employee): ?>
                                <tr>
                                    <th class="selectedTH"> <p> <?= $employee['employee']->FirstName ?> <?= $employee['employee']->LastName ?> </p> </th>
                                    <th class="selectedTH"> <p> <?= $employee['employee']->Email ?> </p> </th>
                                    <th class="selectedTH"> 
                                        <p> 
                                            <?php if ($employee['employee']->LastLogIn == null): ?>
                                            Never Logged In
                                            <?php else: ?>
                                                <?= $employee['employee']->LastLogIn ?> 
                                            <?php endif; ?>
                                        </p> 
                                    </th>
                                    <th class="selectedTH"> 
                                        <ul id="roleList">
                                        
                                            <?php if ($employee['employee']->isAdmin == 1): ?>
                                                <li class="roleList"> 
                                                    Admin 
                                                    <form method="post" action="../controller/admin.php" style="display:inline;">
                                                        <input type="hidden" name="removeAdminFromThisEmployeeID" value=" <?= $employee['employee']->EmployeeID ?>">
                                                        <button class="roleRemoveButton" type="submit"> X </button>
                                                    </form>
                                                </li>
                                            <?php elseif (empty($employee['roles'])): ?>
                                                <li>
                                                    No assigned Roles
                                                </li>
                                            
                                            <?php else: ?>
                                                <?php foreach ($employee['roles'] as $role): ?>
                                                <li class="roleList"> 
                                                    <?= $role->RoleName ?> 
                                                    <form method="post" action="../controller/admin.php" style="display:inline;">
                                                        <input type="hidden" name="employeeID" value=" <?= $employee['employee']->EmployeeID ?>">
                                                        <input type="hidden" name="roleID" value=" <?= $role->RoleID ?> ">
                                                        <button class="roleRemoveButton" type="submit"> X </button>
                                                    </form>
                                                </li>
                                            <?php endforeach ?>

                                            <?php endif; ?>
                                        </ul> 
                                    </th>
                                    <th class="addRoleButtonTH"> 
                                        <div>
                                            <?php if ($employee['employee']->isAdmin == 1): ?>
                                                <div class="placeHolderTextRoleContainer">
                                                    <div class="placeHolderTextRole">
                                                        <p id="removeAdminToAddRoleText"> (Remove Admin to Add a Role) </p>
                                                    </div>

                                                </div>
                                               
                                            <?php else: ?>
                                                <form method="post" action="../controller/admin.php" id="roleSelectionForm">
                                                    <input type="hidden" name="addEmployeeID" value=" <?= $employee['employee']->EmployeeID ?>">

                                                    <div id="formGroup">
                                                        <label id="addARoleLabel" for="role"> Add a Role: </label> <br>

                                                        <select name="addRoleID" id="roleSelect">

                                                            <?php foreach ($employee['availableRoles'] as $role): ?>
                                                                <option value=" <?= $role->RoleID ?>"><?= $role->RoleName ?></option>
                                                            <?php endforeach; ?>

                                                            <option value="admin">Admin</option>

                                                        </select>
                                                        <button id="assignRoleButton" type="submit">Assign Role</button>
                                                    </div>
                                                </form>

                                            <?php endif; ?>
                                        </div>
                                    </th>
                                    <th class="deleteUserButtonTH"> 
                                        <form method="post" onclick="return confirm('Are you sure you want to DELETE the User: \'<?= $employee['employee']->FirstName . ' ' . $employee['employee']->LastName  ?>\' ');" action="../controller/admin.php"> 
                                            <input type="hidden" name="employeeToDeleteID" value=" <?= $employee['employee']->EmployeeID ?> ">
                                            <button type="submit" id="deleteEmployeeButton"> Delete Employee </button>
                                        </form>
                                    </th>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>   

                    <div id="addUserButtonContainer">
                        <button id="addUserButton" onclick="openAddUserModal()"> Add a new User </button>
                    </div>
                </div>


                <div id="logs" class="content">

                    <table class="table">
                        <thead>
                            <tr class="tableRow">
                                <th> 
                                    <form method="post" id="dateFilteringForm" action="../controller/admin.php">
                                        <input type="hidden" name="dateFilterForm" value="toggle">
                                        <div id="LogsDateFilteringContainer" onclick="submitDateFilterForm()">
                                            <p> Date </p> 
                                            <img src="../view/css_js_images/filterOffIcon.png" id="dateFilterOffIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/filterIcon.png" id="dateFilterIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/upArrowIcon.png" id="dateUpIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/downArrowIcon.png" id="dateDownIcon" class="filterIcon" alt="">
                                        </div>
                                    </form>
                                </th>
                                <th> 
                                    <form method="post" id="timeFilteringForm" action="../controller/admin.php">
                                        <input type="hidden" name="timeFilterForm" value="toggle">
                                        <div id="LogsTimeFilteringContainer" onclick="submitTimeFilterForm()">
                                            <p> Time </p> 
                                            <img src="../view/css_js_images/filterOffIcon.png" id="timeFilterOffIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/filterIcon.png" id="timeFilterIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/upArrowIcon.png" id="timeUpIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/downArrowIcon.png" id="timeDownIcon" class="filterIcon" alt="">
                                        </div>
                                    </form> 
                                </th>
                                <th> 
                                    <form method="post" id="nameLogFilteringForm" action="../controller/admin.php">
                                        <input type="hidden" name="nameLogFilterForm" value="toggle">
                                        <div id="LogsNameFilteringContainer" onclick="submitNameLogFilterForm()">
                                            <p> Name </p> 
                                            <img src="../view/css_js_images/filterOffIcon.png" id="nameLogFilterOffIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/filterIcon.png" id="nameLogFilterIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/upArrowIcon.png" id="nameLogUpIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/downArrowIcon.png" id="nameLogDownIcon" class="filterIcon" alt="">
                                        </div>
                                    </form> 
                                </th>
                                <th> 
                                    <form method="post" id="eventTypeFilteringForm" action="../controller/admin.php">
                                        <input type="hidden" name="eventTypeFilterForm" value="toggle">
                                        <div id="LogsEventTypeFilteringContainer" onclick="submitEventTypeFilterForm()">
                                            <p> Event Type </p> 
                                            <img src="../view/css_js_images/filterOffIcon.png" id="eventTypeFilterOffIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/filterIcon.png" id="eventTypeFilterIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/upArrowIcon.png" id="eventTypeUpIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/downArrowIcon.png" id="eventTypeDownIcon" class="filterIcon" alt="">
                                        </div>
                                    </form>
                                </th>
                                <th> 
                                    <form method="post" id="detailsFilteringForm" action="../controller/admin.php">
                                        <input type="hidden" name="detailsFilterForm" value="toggle">
                                        <div id="LogsDetailsFilteringContainer" onclick="submitDetailsFilterForm()">
                                            <p> Details </p> 
                                            <img src="../view/css_js_images/filterOffIcon.png" id="detailsFilterOffIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/filterIcon.png" id="detailsFilterIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/upArrowIcon.png" id="detailsUpIcon" class="filterIcon" alt="">
                                            <img src="../view/css_js_images/downArrowIcon.png" id="detailsDownIcon" class="filterIcon" alt="">
                                        </div>
                                    </form>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($auditLogs as $auditLog): ?>
                                <tr class="tableRowLogs"> 
                                    <th class="selectedLogsTH"> <p> <?= $auditLog->Date ?> </th>
                                    <th class="selectedLogsTH"> <p> <?= $auditLog->Time ?> </p> </th>
                                    <th class="selectedLogsTH"> <?= $auditLog->FirstName ?> <?= $auditLog->LastName ?></th>
                                    <th class="selectedLogsTH"> <p> <?= $auditLog->ActionPerformed ?> </p> </th>
                                    <th class="selectedLogsTH"> <p> <?= $auditLog->Details ?> </p> </th>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!-- addUser Modal (initially hidden) -->
        <div id="modalContainer">
            <div id="modal">

                <div id="modalTitleContainer">
                    <h2 id="modalTitle">Fill Out User Details</h2>
                </div>
                
                <form id="addUserForm" method="post" action="../controller/admin.php">

                    <div class="modalFlexContainer">
                        <label for="firstName">First Name:</label>
                        <input type="text" id="firstName" name="firstName" required><br>
                    </div>

                    <div class="modalFlexContainer">
                        <label for="lastName">Last Name:</label>
                        <input type="text" id="lastName" name="lastName" required><br>
                    </div>

                    <div class="modalFlexContainer">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required><br>
                    </div>

                    <div class="modalFlexContainer">
                        <label for="password">Temporary Password:</label>
                        <input type="password" id="password" name="password" required><br>
                    </div>

                    <div id="modalButtonContainer">
                        <button class="formButton" id="cancelFormButton" onclick="closeAddUserModal()"> Cancel </button>
                        <button class="formButton" id="addUserFormButton" type="submit">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>