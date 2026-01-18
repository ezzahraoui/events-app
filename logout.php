<?php
require_once 'src/Database.php';
require_once 'src/models/User.php';
require_once 'src/services/AuthService.php';
require_once 'src/services/EmailService.php';

session_start();

AuthService::logout();
header('Location: index.php');
exit;
