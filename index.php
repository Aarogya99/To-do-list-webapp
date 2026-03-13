<?php
require_once 'config.php';

$stmt = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Todo App</title>
    <link rel="stylesheet" href="style.css">
    <!-- Boxicons for icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="container">
    <h1><i class='bx bx-check-double'></i> My Tasks</h1>

    <form action="add.php" method="POST" class="add-form">
        <input type="text" name="title" placeholder="What needs to be done?" required autocomplete="off">
        <button type="submit" class="btn btn-primary">
            <i class='bx bx-plus'></i> Add
        </button>
    </form>

    <div class="task-list">
        <?php if(count($tasks) > 0): ?>
            <?php foreach($tasks as $task): ?>
                <div class="task-item">
                    <div class="task-content">
                        <form action="update.php" method="POST" class="btn-icon-form">
                            <input type="hidden" name="id" value="<?= $task['id'] ?>">
                            <div class="custom-checkbox-wrapper">
                                <input type="checkbox" class="checkbox" onChange="this.form.submit()" <?= $task['status'] === 'completed' ? 'checked' : '' ?>>
                            </div>
                        </form>
                        <span class="task-text <?= $task['status'] === 'completed' ? 'completed' : '' ?>">
                            <?= htmlspecialchars($task['title']) ?>
                        </span>
                    </div>
                    <div class="actions">
                        <form action="delete.php" method="POST" class="btn-icon-form">
                            <input type="hidden" name="id" value="<?= $task['id'] ?>">
                            <button type="submit" class="btn-icon" onclick="return confirm('Are you sure you want to delete this task?');" title="Delete Task">
                                <i class='bx bx-trash' style='font-size: 1.3rem;'></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class='bx bx-task' style='font-size: 4rem; color: var(--border);'></i>
                <p>No tasks yet. Add one above to get started!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
