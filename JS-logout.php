<?php
session_start();
session_destroy();
header("Location: jobseeker_login.php");
exit();
?>
