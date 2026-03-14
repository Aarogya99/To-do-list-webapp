<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE status = 'completed'");
    $stmt->execute();
}

header('Location: index.php');
exit;
?>
