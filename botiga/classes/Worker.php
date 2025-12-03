<?php
class Worker {
    public $username;
    public $password;
    public $role;
    public $name;

    public function __construct($username, $password, $role, $name) {
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
        $this->name = $name;
    }
}
?>
