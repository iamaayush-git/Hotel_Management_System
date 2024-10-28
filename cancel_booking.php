<?php
session_start();
include 'db_connection.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['booking_id'])) {
  $booking_id = $_POST['booking_id'];

  // Update reservation status to 'Cancelled' (or delete it if you prefer)
  $sql = "DELETE FROM reservations WHERE id = ?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "i", $booking_id);

  if (mysqli_stmt_execute($stmt)) {
    echo "<script>window.location.href='my_bookings.php';</script>";
  }

  mysqli_stmt_close($stmt);
  mysqli_close($conn);
}
?>