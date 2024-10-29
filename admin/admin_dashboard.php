<?php
session_start(); // Start the session
include '../db_connection.php';
// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
  // Redirect to the login page if not logged in
  header("Location: ../login.php"); // Adjust the path to your login page
  exit; // Ensure that no further code is executed
}


$totalRoomsQuery = "SELECT COUNT(*) as total FROM rooms";
$totalRoomsResult = mysqli_query($conn, $totalRoomsQuery);
$totalRooms = mysqli_fetch_assoc($totalRoomsResult)['total'];

// Fetch total users
$totalUsersQuery = "SELECT COUNT(*) as total FROM users"; // Adjust table name as needed
$totalUsersResult = mysqli_query($conn, $totalUsersQuery);
$totalUsers = mysqli_fetch_assoc($totalUsersResult)['total'];

// Fetch total bookings
$totalBookingsQuery = "SELECT COUNT(*) as total FROM reservations";
$totalBookingsResult = mysqli_query($conn, $totalBookingsQuery);
$totalBookings = mysqli_fetch_assoc($totalBookingsResult)['total'];

$totalFoodItemsQuery = "SELECT * FROM `food_items` WHERE 1";
$totalFoodItemsResult = mysqli_query($conn, $totalFoodItemsQuery);
if ($totalFoodItemsResult) {
  // Count the total number of rows in the result
  $totalFoodItems = mysqli_num_rows($totalFoodItemsResult);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
  <div class="flex">
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 p-10">
      <header class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Dashboard</h1>
        <div>
          <span class="text-gray-700">Welcome, Admin!</span>
        </div>
      </header>

      <main>
        <div class="container mx-auto p-8">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Card 1 -->
            <div class="bg-white p-6 rounded-lg shadow-md">
              <h2 class="font-bold text-lg mb-2">Total Rooms</h2>
              <p class="text-gray-700"><?php echo htmlspecialchars($totalRooms); ?> </p>
            </div>
            <!-- Card 2 -->
            <div class="bg-white p-6 rounded-lg shadow-md">
              <h2 class="font-bold text-lg mb-2">Total Users</h2>
              <p class="text-gray-700"><?php echo htmlspecialchars($totalUsers); ?></p>
            </div>
            <!-- Card 3 -->
            <div class="bg-white p-6 rounded-lg shadow-md">
              <h2 class="font-bold text-lg mb-2">Total Bookings</h2>
              <p class="text-gray-700"><?php echo htmlspecialchars($totalBookings); ?></p>
            </div>
            <!-- Card 4 -->
            <div class="bg-white p-6 rounded-lg shadow-md">
              <h2 class="font-bold text-lg mb-2">Food Items</h2>
              <p class="text-gray-700"><?php echo htmlspecialchars($totalFoodItems); ?></p>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</body>

</html>