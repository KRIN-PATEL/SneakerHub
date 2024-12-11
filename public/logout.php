<?php
session_start();
session_unset(); 
session_destroy(); 

// Redirect to thee login page
header('Location: index.php');
exit();
?>