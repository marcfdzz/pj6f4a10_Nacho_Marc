<?php
require_once 'User.php';

class Treballador extends User {
    public function __construct($username, $password, $email, $name) {
        parent::__construct($username, $password, 'treballador', $email, $name);
    }
}
?>
