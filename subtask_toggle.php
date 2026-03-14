<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    
    $stmt = $pdo->prepare("SELECT is_completed, task_id FROM subtasks WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $subtask = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($subtask) {
        $newState = $subtask['is_completed'] ? 0 : 1;
        $updateStmt = $pdo->prepare("UPDATE subtasks SET is_completed = :state WHERE id = :id");
        $updateStmt->execute(['state' => $newState, 'id' => $id]);
        
        $action = $newState ? 'completed subtask' : 'uncompleted subtask';
        $logStmt = $pdo->prepare("INSERT INTO activity_logs (task_id, action) VALUES (:task_id, :action)");
        $logStmt->execute(['task_id' => $subtask['task_id'], 'action' => $action]);
    }
}

header('Location: index.php');
exit;
?>
