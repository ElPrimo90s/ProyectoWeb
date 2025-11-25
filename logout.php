<?php
session_start();
session_unset();
session_destroy();
header("Location: RegistroTest.html"); // Redirige al login
exit();
?>
