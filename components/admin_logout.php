<?php

include 'connect.php';

if (!isset($_SESSION)) {
    session_start();
}
session_unset();
session_destroy();

header('location:../admin/admin_login.php');
