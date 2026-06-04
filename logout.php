<?php
session_start();

// 1. Xác định link quay lại: Ưu tiên tham số GET 'from', tiếp theo là SESSION 'back_url', cuối cùng mới là 'index.php'
$redirect_to = 'index.php';

if (isset($_GET['from']) && !empty($_GET['from'])) {
    $redirect_to = $_GET['from'];
} elseif (isset($_SESSION['back_url']) && !empty($_SESSION['back_url'])) {
    $redirect_to = $_SESSION['back_url'];
}

// 2. Xóa toàn bộ dữ liệu trong Session
$_SESSION = array();

// 3. Hủy Session hoàn toàn khỏi hệ thống
session_destroy();

// 4. Xóa Cookie session trên trình duyệt (Tăng tính bảo mật)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 5. Chuyển hướng người dùng về đúng trang trước khi bấm Đăng xuất
header("Location: " . $redirect_to);
exit();
?>
