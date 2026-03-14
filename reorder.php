<?php
require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['order']) && is_array($data['order'])) {
    $order = $data['order'];
    $pdo->beginTransaction();
    
    try {
        $stmt = $pdo->prepare("UPDATE tasks SET display_order = :display_order WHERE id = :id");
        foreach ($order as $index => $id) {
            $stmt->execute([
                'display_order' => $index,
                'id' => $id
            ]);
        }
        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch(Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
}
?>
