<?php 

class EmployeeRole {
    private $EmployeeRoleID;
    private $EmployeeID;
    private $RoleID;

    function __get($name) {
        return $this->$name;
    }

    function __set($name, $value) {
        $this->$name = $value;
    }
}
?>