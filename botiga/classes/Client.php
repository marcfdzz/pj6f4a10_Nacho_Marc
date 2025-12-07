<?php
require_once 'User.php';

class Client extends User {
    private $address;
    private $phone;
    private $id;

    public function __construct($username, $password, $email, $name, $address, $phone, $id = null) {
        parent::__construct($username, $password, 'client', $email, $name);
        $this->address = $address;
        $this->phone = $phone;
        $this->id = $id;
    }

    public function getAddress() { return $this->address; }
    public function getPhone() { return $this->phone; }
    public function getId() { return $this->id; }

    public function setAddress($address) { $this->address = $address; }
    public function setPhone($phone) { $this->phone = $phone; }

    public function toArray() {
        $data = parent::toArray();
        $data['address'] = $this->address;
        $data['phone'] = $this->phone;
        $data['id'] = $this->id;
        return $data;
    }
}
?>
