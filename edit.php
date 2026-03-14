<?php
require_once 'config.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = :id");
$stmt->execute(['id' => $id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
    $priority = $_POST['priority'] ?? 'medium';
    $category = $_POST['category'] ?? 'Personal';
    
    if (!empty($title)) {
        $updateStmt = $pdo->prepare("UPDATE tasks SET title = :title, description = :description, due_date = :due_date, priority = :priority, category = :category WHERE id = :id");
        $updateStmt->execute([
            'title' => $title,
            'description' => $description,
            'due_date' => $due_date,
            'priority' => $priority,
            'category' => $category,
            'id' => $id
        ]);
        
        $logStmt = $pdo->prepare("INSERT INTO activity_logs (task_id, action) VALUES (:task_id, 'updated')");
        $logStmt->execute(['task_id' => $id]);
        
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task - Professional Todo App</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="container">
    <h1><i class='bx bx-edit'></i> Edit Task</h1>

    <form action="" method="POST" class="edit-form">
        <div class="form-group">
            <label for="title">Task Title</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($task['title']) ?>" required>
        </div>
        
        <div class="form-row">
            <div class="form-group" style="flex: 1;">
                <label for="due_date">Due Date</label>
                <?php $min_date = ($task['due_date'] && $task['due_date'] < date('Y-m-d')) ? $task['due_date'] : date('Y-m-d'); ?>
                <input type="date" id="due_date" name="due_date" value="<?= $task['due_date'] ? htmlspecialchars($task['due_date']) : '' ?>" min="<?= $min_date ?>">
            </div>
            
            <div class="form-group" style="flex: 1;">
                <label for="priority">Priority</label>
                <select id="priority" name="priority">
                    <option value="low" <?= $task['priority'] === 'low' ? 'selected' : '' ?>>Low Priority</option>
                    <option value="medium" <?= $task['priority'] === 'medium' ? 'selected' : '' ?>>Medium Priority</option>
                    <option value="high" <?= $task['priority'] === 'high' ? 'selected' : '' ?>>High Priority</option>
                </select>
            </div>
            
            <div class="form-group" style="flex: 1;">
                <label for="category">Category</label>
                <select id="category" name="category">
                    <option value="Personal" <?= $task['category'] === 'Personal' ? 'selected' : '' ?>>Personal</option>
                    <option value="Work" <?= $task['category'] === 'Work' ? 'selected' : '' ?>>Work</option>
                    <option value="Shopping" <?= $task['category'] === 'Shopping' ? 'selected' : '' ?>>Shopping</option>
                    <option value="Health" <?= $task['category'] === 'Health' ? 'selected' : '' ?>>Health</option>
                    <option value="Finance" <?= $task['category'] === 'Finance' ? 'selected' : '' ?>>Finance</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description (Optional)</label>
            <textarea id="description" name="description" rows="4"><?= htmlspecialchars($task['description'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <a href="index.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class='bx bx-save'></i> Save Changes
            </button>
        </div>
    </form>
</div>

</body>
</html>
