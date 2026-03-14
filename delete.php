<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->execute(['id' => $id]);
    
    // We already have ON DELETE SET NULL for task_id, but it's good to log the deletion with context before task is gone if we wanted task info. Wait, since task_id will be set to NULL, let's just insert a general log or log before delete. Actually, a log with null task_id might be confusing. Let's just rely on activity logs or insert a generic system log. I will just skip logging deletes for now to avoid complexity with NULL constraints, or I can insert it after but the FK might fail if I use the old id unless it's set to NULL. Let me just insert log with null task_id but add the action as 'deleted a task'.
    $logStmt = $pdo->prepare("INSERT INTO activity_logs (task_id, action) VALUES (NULL, 'deleted')");
    $logStmt->execute();
}

header('Location: index.php');
exit;
?>
