<?php
session_start();
session_destroy();
header('Location: inici_sessio.php');
exit;
?>
