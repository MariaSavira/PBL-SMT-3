<?php
session_start();

$scriptPath = $_SERVER['SCRIPT_NAME'];

$isAdminPage = strpos($scriptPath, '/Admin/') !== false;
$isUserPage  = strpos($scriptPath, '/User/')  !== false;

if ($isAdminPage) {
    if (empty($_SESSION['id_anggota'])) {
        
        header('Location: /PBL-SMT-3/User/Login.php');
        exit;
    }
}

if ($isUserPage) {
    if (!empty($_SESSION['id_anggota'])) {
        
        header('Location: /PBL-SMT-3/Admin/Dashboard.php'); 
        exit;
    }
}
