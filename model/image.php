<?php 

class image {
    private $imageID;
    private $RoleID;
    private $EmployeeID;
    private $image;
    private $dateModified;
    private $timeModified;
    private $base64Image;
    private $FirstName;
    private $LastName;

    function __get($name) {
        return $this->$name;
    }

    function __set($name, $value) {
        $this->$name = $value;
    }

    public function getBase64Image() {
        return base64_encode($this->image);
    }
}
?>