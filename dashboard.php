<?php
require_once 'config.php';

// Fetch statistics
$stmt = $pdo->query("SELECT COUNT(*) FROM tasks");
$totalTasks = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM tasks WHERE status = 'completed'");
$completedTasks = $stmt->fetchColumn();

$pendingTasks = $totalTasks - $completedTasks;

// Fetch completion rate
$completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

// Fetch tasks by category
$categoryStmt = $pdo->query("SELECT category, COUNT(*) as count FROM tasks GROUP BY category");
$categoryData = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

$categories = [];
$categoryCounts = [];
foreach ($categoryData as $row) {
    $categories[] = $row['category'];
    $categoryCounts[] = $row['count'];
}

// Fetch Activity Logs
$logStmt = $pdo->query("
    SELECT l.*, t.title 
    FROM activity_logs l
    LEFT JOIN tasks t ON l.task_id = t.id
    ORDER BY l.created_at DESC 
    LIMIT 15
");
$logs = $logStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Dashboard - Professional Todo App</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="<?= isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'light' ? 'light-theme' : '' ?>">

<script>
    const savedTheme = localStorage.getItem('theme') || 'dark';
    if(savedTheme === 'light') document.body.classList.add('light-theme');
</script>

<div class="container" style="max-width: 900px;">
    <div class="header-actions" style="margin-bottom: 2rem;">
        <h1><i class='bx bx-bar-chart-alt-2'></i> Analytics Dashboard</h1>
        <div class="top-buttons">
            <a href="index.php" class="btn btn-secondary"><i class='bx bx-arrow-back'></i> Back to Tasks</a>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="stat-card">
            <h3>Total Tasks</h3>
            <p class="value"><?= $totalTasks ?></p>
        </div>
        <div class="stat-card">
            <h3>Completed</h3>
            <p class="value" style="color: var(--success);"><?= $completedTasks ?></p>
        </div>
        <div class="stat-card">
            <h3>In Progress</h3>
            <p class="value" style="color: #f59e0b;"><?= $pendingTasks ?></p>
        </div>
        <div class="stat-card">
            <h3>Completion Rate</h3>
            <p class="value" style="color: var(--primary);"><?= $completionRate ?>%</p>
        </div>
    </div>

    <div class="chart-container">
        <h3 style="margin-top: 0; color: var(--text-muted);">Tasks by Category</h3>
        <canvas id="categoryChart" style="max-height: 250px; width: 100%;"></canvas>
    </div>

    <div>
        <h3 style="color: var(--text-muted); margin-bottom: 1rem;">Recent Activity</h3>
        <div class="activity-log">
            <?php if(count($logs) > 0): ?>
                <?php foreach($logs as $log): ?>
                    <div class="log-item">
                        <span class="action">
                            Task <strong><?= htmlspecialchars($log['title'] ?? 'Deleted Task') ?></strong> 
                            was <?= htmlspecialchars($log['action']) ?>
                        </span>
                        <span class="time"><?= date('M j, Y g:i A', strtotime($log['created_at'])) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: var(--text-muted); text-align: center;">No activity recorded yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Initialize Chart.js
    const ctx = document.getElementById('categoryChart').getContext('2d');
    
    // Theme colors for chart
    const isLightTheme = document.body.classList.contains('light-theme');
    const textColor = isLightTheme ? '#64748b' : '#94a3b8';

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($categories) ?>,
            datasets: [{
                label: 'Tasks Count',
                data: <?= json_encode($categoryCounts) ?>,
                backgroundColor: 'rgba(99, 102, 241, 0.6)',
                borderColor: 'rgba(99, 102, 241, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, color: textColor }
                },
                x: {
                    ticks: { color: textColor }
                }
            }
        }
    });
</script>

</body>
</html>
