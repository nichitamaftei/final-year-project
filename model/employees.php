<?php 

class Employees {
    private $EmpoyeID;
    private $FirstName;
    private $LastName;
    private $Email;
    private $Password;

    function __get($name) {
        return $this->$name;
    }

    function __set($name, $value) {
        $this->$name = $value;
    }
}
?>