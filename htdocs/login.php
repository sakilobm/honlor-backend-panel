<?php
require_once 'libs/load.php';
use Aether\Session;

if(Session::isAuthenticated()){
    header('Location: ' . Session::url('admin'));
    exit;
}
Session::renderPageLogin();
?>
