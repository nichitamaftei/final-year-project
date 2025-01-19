<?php 

class Employees {
    private $EmployeeID;
    private $FirstName;
    private $LastName;
    private $Email;
    private $Password;
    private $LastLogIn;
    private $isAdmin;

    function __get($name) {
        return $this->$name;
    }

    function __set($name, $value) {
        $this->$name = $value;
    }
}
?>