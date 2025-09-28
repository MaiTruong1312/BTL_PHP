<?php
session_start();
header('Content-Type: application/json');

// Bật lỗi khi dev, nhớ tắt khi lên production
error_reporting(E_ALL);
ini_set('display_errors', 1);

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$action = $_POST['action'] ?? '';

if (!$id || !in_array($action, ['add', 'remove'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

// Khởi tạo session wishlist nếu chưa có
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

if ($action === 'add') {
    if (!in_array($id, $_SESSION['wishlist'])) {
        $_SESSION['wishlist'][] = $id;
    }
} else { // remove
    $_SESSION['wishlist'] = array_filter($_SESSION['wishlist'], function($pid) use ($id) {
        return $pid != $id;
    });
    // array_filter có thể để key rỗng, dùng array_values để reset key
    $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
}

echo json_encode([
    'success' => true,
    'wishlist' => $_SESSION['wishlist'] // trả về luôn để debug
]);
?>