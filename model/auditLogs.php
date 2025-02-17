<?php 

class AuditLog {
    private $AuditLogID;
    private $EmployeeID;
    private $Date;
    private $Time;
    private $ActionPerformed;
    private $Details;
    private $FirstName;
    private $LastName;

    function __get($name) {
        return $this->$name;
    }

    function __set($name, $value) {
        $this->$name = $value;
    }
}
?>