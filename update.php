<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // First get current status
    $stmt = $pdo->prepare("SELECT status FROM tasks WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($task) {
        $new_status = $task['status'] === 'completed' ? 'pending' : 'completed';
        $updateStmt = $pdo->prepare("UPDATE tasks SET status = :status WHERE id = :id");
        $updateStmt->execute([
            'status' => $new_status,
            'id' => $id
        ]);
    }
}

header('Location: index.php');
exit;
?>
