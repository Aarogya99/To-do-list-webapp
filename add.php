<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['title'])) {
    $title = trim($_POST['title']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
    $priority = $_POST['priority'] ?? 'medium';
    $category = $_POST['category'] ?? 'Personal';
    
    $stmt = $pdo->prepare("INSERT INTO tasks (title, description, due_date, priority, category) VALUES (:title, :description, :due_date, :priority, :category)");
    $stmt->execute([
        'title' => $title, 
        'description' => $description,
        'due_date' => $due_date,
        'priority' => $priority,
        'category' => $category
    ]);
    
    // Log activity
    $taskId = $pdo->lastInsertId();
    $logStmt = $pdo->prepare("INSERT INTO activity_logs (task_id, action) VALUES (:task_id, 'created')");
    $logStmt->execute(['task_id' => $taskId]);
}

header('Location: index.php');
exit;
?>
