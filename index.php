<?php
require_once 'config.php';

$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';
$query = "SELECT * FROM tasks WHERE 1=1";
$params = [];

if ($filter === 'active') {
    $query .= " AND status = 'pending'";
} elseif ($filter === 'completed') {
    $query .= " AND status = 'completed'";
}

if (!empty($search)) {
    $query .= " AND (title LIKE :search OR description LIKE :search)";
    $params['search'] = "%$search%";
}

$query .= " ORDER BY display_order ASC, created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all subtasks
$subtasksSql = "SELECT * FROM subtasks ORDER BY id ASC";
$subtasksStmt = $pdo->query($subtasksSql);
$allSubtasks = $subtasksStmt->fetchAll(PDO::FETCH_ASSOC);

// Group subtasks by task_id
$subtasksByTask = [];
foreach ($allSubtasks as $st) {
    if (!isset($subtasksByTask[$st['task_id']])) {
        $subtasksByTask[$st['task_id']] = [];
    }
    $subtasksByTask[$st['task_id']][] = $st;
}

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
    <div class="header-actions">
        <h1><i class='bx bx-check-double'></i> My Tasks</h1>
        <div class="top-buttons">
            <button id="themeToggle" class="btn-icon" title="Toggle Dark/Light Mode"><i class='bx bx-moon'></i></button>
            <a href="dashboard.php" class="btn-icon" title="View Dashboard"><i class='bx bx-bar-chart-alt-2'></i></a>
        </div>
    </div>
    
    <?php if(isset($_GET['error']) && $_GET['error'] === 'future_date'): ?>
        <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: var(--danger); padding: 1rem; border-radius: 12px; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class='bx bx-error-circle'></i> This task has a future due date and cannot be completed early.
        </div>
    <?php endif; ?>

    <form action="index.php" method="GET" class="search-form">
        <div class="search-input-wrapper">
            <i class='bx bx-search'></i>
            <input type="text" name="search" placeholder="Search tasks..." value="<?= htmlspecialchars($search) ?>">
            <?php if(isset($_GET['filter'])): ?>
                <input type="hidden" name="filter" value="<?= htmlspecialchars($_GET['filter']) ?>">
            <?php endif; ?>
        </div>
    </form>

    <form action="add.php" method="POST" class="add-form">
        <div class="add-form-group">
            <input type="text" name="title" placeholder="What needs to be done?" required autocomplete="off">
            <input type="text" name="description" placeholder="Optional description..." autocomplete="off">
        </div>
        <div class="add-form-actions">
            <div class="add-form-options">
                <input type="date" name="due_date" title="Due Date" min="<?= date('Y-m-d') ?>">
                <select name="priority" title="Task Priority">
                    <option value="low">Low Priority</option>
                    <option value="medium" selected>Medium Priority</option>
                    <option value="high">High Priority</option>
                </select>
                <select name="category" title="Task Category">
                    <option value="Personal" selected>Personal</option>
                    <option value="Work">Work</option>
                    <option value="Shopping">Shopping</option>
                    <option value="Health">Health</option>
                    <option value="Finance">Finance</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class='bx bx-plus'></i> Add
            </button>
        </div>
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

    <div class="task-list" id="sortable-list">
        <?php if(count($tasks) > 0): ?>
            <?php foreach($tasks as $task): ?>
                <div class="task-item" data-id="<?= $task['id'] ?>">
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
                            
                            <div class="task-meta">
                                <?php if($task['priority']): ?>
                                    <span class="badge priority-<?= $task['priority'] ?>">
                                        <?= ucfirst($task['priority']) ?>
                                    </span>
                                <?php endif; ?>

                                <?php if($task['category']): ?>
                                    <span class="badge category">
                                        <?= htmlspecialchars($task['category']) ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if($task['due_date']): ?>
                                    <span class="badge due-date <?= (strtotime($task['due_date']) < time() && $task['status'] === 'pending') ? 'overdue' : '' ?>">
                                        <i class='bx bx-calendar'></i> 
                                        <?= date('M j, Y', strtotime($task['due_date'])) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if(!empty($task['description'])): ?>
                                <p class="task-desc <?= $task['status'] === 'completed' ? 'completed' : '' ?>"><?= nl2br(htmlspecialchars($task['description'])) ?></p>
                            <?php endif; ?>

                            <div class="subtask-list">
                                <?php 
                                    $taskSubtasks = isset($subtasksByTask[$task['id']]) ? $subtasksByTask[$task['id']] : [];
                                    foreach($taskSubtasks as $st): 
                                ?>
                                    <div class="subtask-item">
                                        <form action="subtask_toggle.php" method="POST" class="btn-icon-form">
                                            <input type="hidden" name="id" value="<?= $st['id'] ?>">
                                            <div class="custom-checkbox-wrapper" style="transform: scale(0.8);">
                                                <input type="checkbox" class="checkbox" onChange="this.form.submit()" <?= $st['is_completed'] ? 'checked' : '' ?>>
                                            </div>
                                        </form>
                                        <span class="subtask-text <?= $st['is_completed'] ? 'completed' : '' ?>"><?= htmlspecialchars($st['title']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                                
                                <form action="subtask_add.php" method="POST" class="subtask-add-form">
                                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                    <input type="text" name="title" placeholder="Add subtask..." required autocomplete="off">
                                    <button type="submit"><i class='bx bx-plus'></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="actions">
                        <a href="edit.php?id=<?= $task['id'] ?>" class="btn-icon" title="Edit Task">
                            <i class='bx bx-edit' style='font-size: 1.3rem;'></i>
                        </a>
                        <form action="delete.php" method="POST" class="btn-icon-form">
                            <input type="hidden" name="id" value="<?= $task['id'] ?>">
                            <button type="submit" class="btn-icon btn-icon-danger" onclick="return confirm('Are you sure you want to delete this task?');" title="Delete Task">
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
<!-- SortableJS for Drag and Drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    // Theme toggling
    const themeBtn = document.getElementById('themeToggle');
    const body = document.body;
    
    // Check local storage for theme
    const savedTheme = localStorage.getItem('theme') || 'dark';
    if(savedTheme === 'light') {
        body.classList.add('light-theme');
        themeBtn.innerHTML = "<i class='bx bx-sun'></i>";
    }

    themeBtn.addEventListener('click', () => {
        body.classList.toggle('light-theme');
        const isLight = body.classList.contains('light-theme');
        localStorage.setItem('theme', isLight ? 'light' : 'dark');
        themeBtn.innerHTML = isLight ? "<i class='bx bx-sun'></i>" : "<i class='bx bx-moon'></i>";
    });

    // Drag and Drop using SortableJS
    const taskList = document.getElementById('sortable-list');
    if (taskList) {
        new Sortable(taskList, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function (evt) {
                const itemEl = evt.item;
                const newIndex = evt.newIndex;
                
                // Get all order IDs
                const order = [];
                taskList.querySelectorAll('.task-item').forEach(function(el) {
                    order.push(el.getAttribute('data-id'));
                });
                
                // Send AJAX request to update order
                fetch('reorder.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order: order })
                }).then(response => response.json()).then(data => {
                    console.log('Reordered', data);
                });
            }
        });
    }

    // Notifications API for reminders
    if ("Notification" in window) {
        if (Notification.permission !== 'denied' && Notification.permission !== 'granted') {
            Notification.requestPermission();
        }
    }
    
    // Trigger desktop notification if we have overdue or due soon items
    <?php
        $overdueCount = 0;
        foreach($tasks as $t) {
            if ($t['status'] === 'pending' && $t['due_date'] && strtotime($t['due_date']) <= time() + 86400) {
                $overdueCount++;
            }
        }
    ?>
    const urgentTasks = <?= $overdueCount ?>;
    if (urgentTasks > 0 && Notification.permission === "granted" && !sessionStorage.getItem('notified')) {
        new Notification("Todo App Reminder", {
            body: `You have ${urgentTasks} task(s) due soon or overdue!`,
            icon: "https://cdn-icons-png.flaticon.com/512/1828/1828640.png"
        });
        sessionStorage.setItem('notified', 'true');
    }
</script>
</html>
