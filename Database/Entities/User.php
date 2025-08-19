<?php 

namespace Database\Entities;

class User {

    public $id;
    public $name;
    public $username;
    public $password;

    public function __construct(){}

    public function __toString(){

        return $this->name.'-'.$this->username.'-'.$this->password;
    }
}