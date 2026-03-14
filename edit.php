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
    
    if (!empty($title)) {
        $updateStmt = $pdo->prepare("UPDATE tasks SET title = :title, description = :description WHERE id = :id");
        $updateStmt->execute([
            'title' => $title,
            'description' => $description,
            'id' => $id
        ]);
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
