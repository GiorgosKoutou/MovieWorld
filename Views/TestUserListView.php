<?php 

namespace Views;

require_once("../Services/UserService.php");

use Model\UserService;

$service = new UserService();

$users = $service->getUsers();

foreach($users as $user){

    echo "<li>".$user."</li>";
    echo "<br>";
}