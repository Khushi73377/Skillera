<?php
session_start();

if (!isset($_SESSION['test'])) {
    $_SESSION['test'] = "Session is working!";
} 

echo "Session Test: " . $_SESSION['test'];
?>
