<?php
session_start(); // Start the session

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
  // Redirect to the login page if not logged in
  header("Location: ../login.php"); // Adjust the path to your login page
  exit; // Ensure that no further code is executed
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
    <div class="bg-blue-800 w-64 min-h-screen text-white">
      <div class="p-4 text-center font-bold text-xl">Admin Dashboard</div>
      <nav class="mt-6">
        <a href="manage_rooms.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">Manage
          Rooms</a>
        <a href="manage_users.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">Manage
          Users</a>
        <a href="booking_management.php"
          class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">Booking Management</a>
        <a href="/hotel_management/logout.php"
          class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">Logout</a>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-10">
      <header class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Dashboard</h1>
        <div>
          <span class="text-gray-700">Welcome, Admin!</span>
        </div>
      </header>

      <main>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Card 1 -->
          <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="font-bold text-lg mb-2">Total Rooms</h2>
            <p class="text-gray-700">20 Rooms</p>
          </div>
          <!-- Card 2 -->
          <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="font-bold text-lg mb-2">Total Users</h2>
            <p class="text-gray-700">100 Users</p>
          </div>
          <!-- Card 3 -->
          <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="font-bold text-lg mb-2">Total Bookings</h2>
            <p class="text-gray-700">50 Bookings</p>
          </div>
        </div>
      </main>
    </div>
  </div>
</body>

</html>