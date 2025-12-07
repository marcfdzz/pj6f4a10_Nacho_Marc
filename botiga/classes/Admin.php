<?php
require_once 'Treballador.php';

class Admin extends Treballador {
    public function __construct($username, $password, $email, $name) {
        parent::__construct($username, $password, $email, $name);
        $this->role = 'admin';
    }
}
?>
