<?php

class pdoSingleton{

    private static $instance;
    private $pdo;

    // constructor for the pdoSingleton, it creates the pdo object

    private function __construct() {
        $db_name = "finalYearProjectDatabase";
        $username = "root";
        $password = "";
    
        $this->pdo = new PDO("mysql:host=localhost;dbname=$db_name", 
                                                       $username, 
                                                       $password, 
                                                       [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    // singleton setup: calls the constructor if it hasn't been initialised once before. If it has, then return itself

    public static function getInstance(){
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Employee access

    public function getAllEmployees($filterOptions){

        $baseQuery = "SELECT * FROM Employees";

        $orderByStatements = [];

        if($filterOptions["name"] == "asc"){
            $orderByStatements[] = " FirstName ASC ";
        }else if($filterOptions["name"] == "desc"){
            $orderByStatements[] = " FirstName DESC ";
        }

        if($filterOptions["email"] == "asc"){
            $orderByStatements[] = " Email ASC ";
        }else if($filterOptions["email"] == "desc"){
            $orderByStatements[] = " Email DESC ";
        }

        if($filterOptions["logIn"] == "asc"){
            $orderByStatements[] = " LastLogIn ASC ";
        }else if($filterOptions["logIn"] == "desc"){
            $orderByStatements[] = " LastLogIn DESC ";
        }

        if(!empty($orderByStatements)){
            $baseQuery .= " ORDER BY " . implode(", ", $orderByStatements);
        }

        $pdo = $this->pdo;
        $statement = $pdo->prepare($baseQuery); 
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_CLASS, "Employees");
        return $results;
    }

    public function updateLastLogInByID($employeeID){
        $pdo = $this->pdo;
        $statement = $pdo->prepare("UPDATE Employees SET LastLogIn = NOW() WHERE EmployeeID = ?");
        $statement->execute([$employeeID]);
    }

    public function deleteEmployeeFromID($employeeID){
        $pdo = $this->pdo;
        $statement = $pdo->prepare("DELETE FROM Employees WHERE EmployeeID = ?");
        $statement->execute([$employeeID]);
    }

    public function removeAdminFromID($employeeID){
        $pdo = $this->pdo;
        $statement = $pdo->prepare("UPDATE Employees SET isAdmin = 0 WHERE EmployeeID = ?");
        $statement->execute([$employeeID]);
    }

    public function addAdminFromID($employeeID){
        $pdo = $this->pdo;
        $statement = $pdo->prepare("UPDATE Employees SET isAdmin = 1 WHERE EmployeeID = ?");
        $statement->execute([$employeeID]);
    }

    public function addEmployee($employee){
        $pdo = $this->pdo;
        $statement = $pdo->prepare("INSERT INTO Employees (FirstName, LastName, Email, Password) VALUES (?,?,?,?)");
        $statement->execute([$employee->FirstName, $employee->LastName, $employee->Email, $employee->Password]);
        return $pdo->lastInsertId();
    }

    public function getEmployeeByID($employeeID){
        $pdo = $this->pdo;
        $statement = $pdo->prepare("SELECT * FROM Employees WHERE EmployeeID = ?");
        $statement->execute([$employeeID]);
        $results = $statement->fetch(PDO::FETCH_OBJ);
        return $results;
    }

    public function updateEmployeePasswordByID($employeeID, $password){
        $pdo = $this->pdo;
        $statement = $pdo->prepare("UPDATE Employees SET Password = ? WHERE EmployeeID = ?");
        $statement->execute([$password, $employeeID]);
    }



    // Role access

    public function getAllRoles(){
        $pdo = $this->pdo;
        $statement = $pdo->prepare("SELECT * FROM Roles");
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_CLASS, "Roles");
        return $results;
    }

    public function getRoleByID($roleID){
        $pdo = $this->pdo;
        $statement = $pdo->prepare("SELECT * FROM Roles WHERE RoleID = ?");
        $statement->execute([$roleID]);
        $results = $statement->fetch(PDO::FETCH_OBJ);
        return $results;
    }

    public function addNewRole($role){
        $pdo = $this->pdo;
        $statement = $pdo->prepare("INSERT INTO Roles (RoleName) VALUES (?)");
        $statement->execute([$role->RoleName]);
        return $pdo->lastInsertId();
    }

    public function deleteRoleById($roleID){
        $pdo = $this->pdo;
        $statement = $pdo->prepare("DELETE FROM Roles WHERE RoleID = ?");
        $statement->execute([$roleID]);
    }


    // EmployeeRole access

    public function getAllEmployeeRole(){
        $pdo = $this->pdo;
        $statement = $pdo->prepare("SELECT * FROM EmployeeRole");
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_CLASS, "EmployeeRole");
        return $results;
    }

    public function removeRoleFromEmployee($employeeID, $roleID){
        $pdo = $this->pdo;
        $statement = $pdo->prepare("DELETE FROM EmployeeRole WHERE EmployeeID = ? AND RoleID = ?");
        $statement->execute([$employeeID, $roleID]);
    }

    public function addRoleToEmployee($employeeID, $roleID){
        $pdo = $this->pdo;
        $statement = $pdo->prepare("INSERT INTO EmployeeRole (EmployeeID, RoleID) VALUES (?, ?)");
        $statement->execute([$employeeID, $roleID]);
    }

    public function removeAllInEmployeeRoleByID($employeeID){
        $pdo = $this->pdo;
        $statement = $pdo->prepare("DELETE FROM EmployeeRole WHERE EmployeeID = ?");
        $statement->execute([$employeeID]);
    }

    // AuditLogs access

    public function getAllAuditLogsWithEmployeeNames($filterOptions){

        $baseQuery = "SELECT AuditLogs.*, Employees.FirstName, Employees.LastName 
                    FROM AuditLogs 
                    JOIN Employees ON AuditLogs.EmployeeID = Employees.EmployeeID";

        $orderByStatements = [];

        if($filterOptions["date"] == "asc"){
            $orderByStatements[] = " Date ASC ";
        }else if($filterOptions["date"] == "desc"){
            $orderByStatements[] = " Date DESC ";
        }

        if($filterOptions["time"] == "asc"){
            $orderByStatements[] = " Time ASC ";
        }else if($filterOptions["time"] == "desc"){
            $orderByStatements[] = " Time DESC ";
        }

        if($filterOptions["nameLog"] == "asc"){
            $orderByStatements[] = " Employees.FirstName ASC ";
        }else if($filterOptions["nameLog"] == "desc"){
            $orderByStatements[] = " Employees.FirstName DESC ";
        }

        if($filterOptions["eventType"] == "asc"){
            $orderByStatements[] = " ActionPerformed ASC ";
        }else if($filterOptions["eventType"] == "desc"){
            $orderByStatements[] = " ActionPerformed DESC ";
        }

        if($filterOptions["details"] == "asc"){
            $orderByStatements[] = " Details ASC ";
        }else if($filterOptions["details"] == "desc"){
            $orderByStatements[] = " Details DESC ";
        }

        if(!empty($orderByStatements)){
            $baseQuery .= " ORDER BY " . implode(", ", $orderByStatements);
        }

        $pdo = $this->pdo;        
        $statement = $pdo->prepare($baseQuery);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_CLASS, "AuditLog");
    }
    

    public function addNewAuditLog($auditLog){
        $pdo = $this->pdo;
        $statement = $pdo->prepare("INSERT INTO AuditLogs (EmployeeID, Date, Time, ActionPerformed, Details) VALUES (?,?,?,?,?)");
        $statement->execute([$auditLog->EmployeeID, $auditLog->Date, $auditLog->Time, $auditLog->ActionPerformed, $auditLog->Details]);
        return $pdo->lastInsertId();
    }

    // Historical Call Flows Images access

    public function addDiagram($departmentID, $employeeID, $pngData, $date, $time){

        $imageData = str_replace("data:image/png;base64,", "", $pngData);
        $imageData = str_replace(" ", "+", $imageData);
        $decodedImage = base64_decode($imageData);

        $pdo = $this->pdo;
        $statement = $pdo->prepare("INSERT INTO `Historical Call Flow Images` (RoleID, EmployeeID, image, dateModified, timeModified) VALUES (?,?,?,?,?)");
        $statement->execute([$departmentID, $employeeID, $decodedImage, $date, $time]);
        return $pdo->lastInsertId();
    }


    public function getDiagramsByDepartmentID($filterOptions, $departmentID){

        $baseQuery = "SELECT `Historical Call Flow Images`.*, Employees.FirstName, Employees.LastName 
                    FROM `Historical Call Flow Images` 
                    JOIN Employees ON `Historical Call Flow Images`.EmployeeID = Employees.EmployeeID 
                    WHERE `Historical Call Flow Images`.RoleID = ?";

        $orderByStatements = [];

        if($filterOptions["historicalFlowDate"] == "asc"){
            $orderByStatements[] = " dateModified ASC ";
        }else if($filterOptions["historicalFlowDate"] == "desc"){
            $orderByStatements[] = " dateModified DESC ";
        }

        if($filterOptions["historicalFlowTime"] == "asc"){
            $orderByStatements[] = " timeModified ASC ";
        }else if($filterOptions["historicalFlowTime"] == "desc"){
            $orderByStatements[] = " timeModified DESC ";
        }

        if($filterOptions["historicalFlowModifiedBy"] == "asc"){
            $orderByStatements[] = " Employees.FirstName ASC ";
        }else if($filterOptions["historicalFlowModifiedBy"] == "desc"){
            $orderByStatements[] = "Employees.FirstName DESC ";
        }

        if(!empty($orderByStatements)){
            $baseQuery .= " ORDER BY " . implode(", ", $orderByStatements);
        }

        $pdo = $this->pdo;        
        $statement = $pdo->prepare($baseQuery);
        $statement->execute([$departmentID]);
        return $statement->fetchAll(PDO::FETCH_CLASS, "image");
    }
}

?>