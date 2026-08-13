<?php
$pdo = new PDO('sqlite:' . __DIR__ . '/database.db');
$pdo->exec("UPDATE settings SET value='' WHERE key='logo_image'");
echo "Logo setting reset successfully.\n";
