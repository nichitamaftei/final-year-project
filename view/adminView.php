<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../view/css_js_images/style.css"> <!-- linking to style sheet -->
        <script src="../@joint/core/dist/joint.js"></script> <!-- linking to JointJS library -->
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script> <!-- linking to j query -->
        <script type="text/javascript" src="../view/css_js_images/adminJavascript.js"></script> <!-- linking to javascript -->
        <title>Admin Page</title>
    </head>

    <body id="adminView">
        <!-- top bar of the screen -->
        <section id='top-bar'>
            <h1 id="top-bar-title">Admin Settings</h1>
        </section>   

        <div class="tabs">
       
            <div class="tab-links">
                <button class="tab-link active" onclick="showTab('users')">Users</button>
                <button class="tab-link" onclick="showTab('logs')">Logs</button>
            </div>

            <div id="users" class="content">

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
                                            <li> 
                                                <?= $role->RoleName ?> 
                                                <form method="post" action="../controller/admin.php" style="display:inline;">
                                                    <input type="hidden" name="employeeID" value=" <?= $employee['employee']->EmployeeID ?>">
                                                    <input type="hidden" name="roleID" value=" <?= $role->RoleID ?> ">
                                                    <button type="submit"> X </button>
                                                </form>
                                            </li>
                                        <?php endforeach ?>

                                        <?php if ($employee['employee']->isAdmin == 1):   ?>
                                            <li> 
                                                Admin 
                                                <form method="post" action="../controller/admin.php" style="display:inline;">
                                                    <input type="hidden" name="removeAdminFromThisEmployeeID" value=" <?= $employee['employee']->EmployeeID ?>">
                                                    <button type="submit"> X </button>
                                                </form>
                                            </li>

                                        <?php endif; ?>
                                    </ul> 
                                </th>
                                <th> 
                                    <div>
                                        <?php if ($employee['employee']->isAdmin == 1):   ?>
                                            (Remove admin to add roles)
                                        <?php else: ?>
                                            <form method="post" action="../controller/admin.php">
                                                <input type="hidden" name="addEmployeeID" value=" <?= $employee['employee']->EmployeeID ?>">
                                                <label for="role"> Add Role: </label>

                                                <select name="addRoleID" id="role">

                                                    <?php foreach ($employee['availableRoles'] as $role): ?>
                                                        <option value=" <?= $role->RoleID ?>"><?= $role->RoleName ?></option>
                                                    <?php endforeach; ?>

                                                    <option value="admin">Admin</option>

                                                </select>
                                                <button type="submit">Assign Role</button>
                                            </form>

                                        <?php endif; ?>
                                    </div>
                                </th>
                                <th> 
                                    <form method="post" action="../controller/admin.php"> 
                                        <input type="hidden" name="employeeToDeleteID" value=" <?= $employee['employee']->EmployeeID ?> ">
                                        <button type="submit" id="remove"> Delete Employee </button>
                                    </form>
                                </th>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>   


                <button onclick="openAddUserModal()"> Add a new User </button>

                <!-- AddUser Modal (initially hidden) -->
                <div id="addUserModal" id="modalContainer">
                    <div id="modal">
    
                        <h2>Fill Out User Details</h2>
                        
                        <form id="addUserForm" method="post" action="../controller/admin.php">
                            <label for="firstName">First Name:</label>
                            <input type="text" id="firstName" name="firstName" required><br>

                            <label for="lastName">Last Name:</label>
                            <input type="text" id="lastName" name="lastName" required><br>

                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required><br>

                            <label for="password">Password:</label>
                            <input type="text" id="password" name="password" required><br>

                            <button onclick="closeAddUserModal()"> Cancel </button>
                            <button type="submit">Add User</button>
                        </form>
                    </div>
                </div>

            </div>


            <div id="logs" class="content">

                <table id="listOfEmployeesTable">
                    <thead>
                        <tr class="tableRow">
                            <th> <p> Date </p> </th>
                            <th> <p> Time </p> </th>
                            <th> <p> Name </p> </th>
                            <th> <p> Event Type </p> </th>
                            <th> <p> Details </p> </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($auditLogs as $auditLog): ?>
                            <tr class="tableRow"> 
                                <th> <p> <?= $auditLog->Date ?> </th>
                                <th> <p> <?= $auditLog->Time ?> </p> </th>
                                <th> <?= $auditLog->FirstName ?> <?= $auditLog->LastName ?></th>
                                <th> <p> <?= $auditLog->ActionPerformed ?> </p> </th>
                                <th> <p> <?= $auditLog->Details ?> </p> </th>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>