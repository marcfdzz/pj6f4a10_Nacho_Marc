<?php
class User {
    protected $username;
    protected $password;
    protected $role;
    protected $email;
    protected $name;

    public function __construct($username, $password, $role, $email, $name) {
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
        $this->email = $email;
        $this->name = $name;
    }

    public function getUsername() { return $this->username; }
    public function getPassword() { return $this->password; }
    public function getRole() { return $this->role; }
    public function getEmail() { return $this->email; }
    public function getName() { return $this->name; }

    public function setPassword($password) { $this->password = $password; }
    public function setEmail($email) { $this->email = $email; }
    public function setName($name) { $this->name = $name; }

    public function toArray() {
        return [
            'username' => $this->username,
            'password' => $this->password,
            'role' => $this->role,
            'email' => $this->email,
            'name' => $this->name
        ];
    }
}
?>
