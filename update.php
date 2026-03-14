<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // First get current status and due_date
    $stmt = $pdo->prepare("SELECT status, due_date FROM tasks WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($task) {
        $new_status = $task['status'] === 'completed' ? 'pending' : 'completed';
        
        // Prevent completing if due date is in the future
        if ($new_status === 'completed' && !empty($task['due_date']) &&  strtotime($task['due_date']) > strtotime(date('Y-m-d'))) {
            header('Location: index.php?error=future_date');
            exit;
        }

        $updateStmt = $pdo->prepare("UPDATE tasks SET status = :status WHERE id = :id");
        $updateStmt->execute([
            'status' => $new_status,
            'id' => $id
        ]);
        
        $action = $new_status === 'completed' ? 'marked as completed' : 'marked as pending';
        $logStmt = $pdo->prepare("INSERT INTO activity_logs (task_id, action) VALUES (:task_id, :action)");
        $logStmt->execute(['task_id' => $id, 'action' => $action]);
    }
}

header('Location: index.php');
exit;
?>
