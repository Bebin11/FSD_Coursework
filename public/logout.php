<?php
require_once __DIR__ . '/../includes/functions.php'; // For session helper if needed (though mostly standard PHP)

session_start();
session_unset();
session_destroy();
header("Location: login.php");
exit;
?>
