<?php
require_once 'config.php';

$filter = $_GET['filter'] ?? 'all';
$query = "SELECT * FROM tasks";

if ($filter === 'active') {
    $query .= " WHERE status = 'pending'";
} elseif ($filter === 'completed') {
    $query .= " WHERE status = 'completed'";
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->query($query);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if any completed exist to show clear button
$stmt2 = $pdo->query("SELECT COUNT(*) FROM tasks WHERE status = 'completed'");
$hasCompleted = $stmt2->fetchColumn() > 0;
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
        <div class="add-form-group">
            <input type="text" name="title" placeholder="What needs to be done?" required autocomplete="off">
            <input type="text" name="description" placeholder="Optional description..." autocomplete="off">
        </div>
        <button type="submit" class="btn btn-primary">
            <i class='bx bx-plus'></i> Add
        </button>
    </form>

    <div class="filters-container">
        <div class="filters">
            <a href="?filter=all" class="<?= $filter === 'all' ? 'active' : '' ?>">All</a>
            <a href="?filter=active" class="<?= $filter === 'active' ? 'active' : '' ?>">Active</a>
            <a href="?filter=completed" class="<?= $filter === 'completed' ? 'active' : '' ?>">Completed</a>
        </div>
        
        <?php if($hasCompleted): ?>
        <form action="clear_completed.php" method="POST" style="margin: 0;">
            <button type="submit" class="btn-clear" onclick="return confirm('Clear all completed tasks?');">
                Clear Completed
            </button>
        </form>
        <?php endif; ?>
    </div>

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
                        <div class="task-details">
                            <span class="task-text <?= $task['status'] === 'completed' ? 'completed' : '' ?>">
                                <?= htmlspecialchars($task['title']) ?>
                            </span>
                            <?php if(!empty($task['description'])): ?>
                                <p class="task-desc <?= $task['status'] === 'completed' ? 'completed' : '' ?>"><?= nl2br(htmlspecialchars($task['description'])) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="actions">
                        <a href="edit.php?id=<?= $task['id'] ?>" class="btn-icon" title="Edit Task">
                            <i class='bx bx-edit' style='font-size: 1.3rem;'></i>
                        </a>
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
