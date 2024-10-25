<?php
session_start();

// Only allow admin access
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit;
}

include "../db_connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'];
  $user_id = intval($_POST['user_id']);

  if ($action === 'ban') {
    // Ban the user
    $sql = "UPDATE users SET is_banned = 1 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);

  } elseif ($action === 'unban') {
    // Unban the user
    $sql = "UPDATE users SET is_banned = 0 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);

  } elseif ($action === 'delete') {
    // Delete the user
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);

  } else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
  }

  if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Failed to execute action']);
  }

  mysqli_stmt_close($stmt);
} else {
  echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

mysqli_close($conn);
?>