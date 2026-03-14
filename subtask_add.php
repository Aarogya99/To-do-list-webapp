<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['title']) && !empty($_POST['task_id'])) {
    $title = trim($_POST['title']);
    $taskId = (int)$_POST['task_id'];
    
    $stmt = $pdo->prepare("INSERT INTO subtasks (task_id, title) VALUES (:task_id, :title)");
    $stmt->execute(['task_id' => $taskId, 'title' => $title]);
    
    // Log
    $logStmt = $pdo->prepare("INSERT INTO activity_logs (task_id, action) VALUES (:task_id, 'added subtask')");
    $logStmt->execute(['task_id' => $taskId]);
}

header('Location: index.php');
exit;
?>
