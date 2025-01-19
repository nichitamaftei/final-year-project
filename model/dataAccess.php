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

    // singleton setup, calls the constructor if it hasn't been initialised once before. If it has then return itself.

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // employee access

    public function getAllEmployees(){
        $pdo = $this->pdo;
        $statement = $pdo->prepare("SELECT * FROM Employees");
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_CLASS, "Employees");
        return $results;
    }

    public function updateLastLogInByID($employeeID){
        $pdo = $this->pdo;
        $statement = $pdo->prepare("UPDATE Employees SET LastLogIn = NOW() WHERE EmployeeID = ?");
        $statement->execute([$employeeID]);
    }

    // role access

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


    // employeeRole access

    public function getAllEmployeeRole(){
        $pdo = $this->pdo;
        $statement = $pdo->prepare("SELECT * FROM EmployeeRole");
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_CLASS, "EmployeeRole");
        return $results;
    }

}

?>