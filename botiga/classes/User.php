<?php
class User {
    public $username;
    public $password;
    public $email;
    public $name;

    public function __construct($username, $password, $email, $name) {
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->name = $name;
    }
}
?>
