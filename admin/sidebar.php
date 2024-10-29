<div class="bg-blue-800 w-64 min-h-screen text-white">
  <a href="admin_dashboard.php">
    <div class="p-4 text-center font-bold text-xl">Admin Dashboard</div>
  </a>
  <nav class="mt-6">
    <a href="manage_rooms.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">Manage
      Rooms</a>
    <a href="manage_users.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">Manage
      Users</a>
    <a href="booking_management.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">Booking
      Management</a>
    <a href="food_orders_management.php"
      class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">Food Orders</a>
    <a href="manage_food.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">Manage
      Food</a>
    <button onclick="openLogoutModal()"
      class="block w-full py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700 text-left">Logout</button>
  </nav>
</div>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
  <div class="bg-white p-8 rounded-lg shadow-2xl max-w-sm text-center">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Confirm Logout</h2>
    <p class="mb-6 text-gray-700">Are you sure you want to log out?</p>
    <div class="flex justify-center">
      <button onclick="confirmLogout()"
        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-full shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1 mr-2">Yes</button>
      <button onclick="closeLogoutModal()"
        class="bg-gray-300 hover:bg-gray-400 text-black px-6 py-2 rounded-full shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1">Cancel</button>
    </div>
  </div>
</div>

<script>
  // Open the logout modal
  function openLogoutModal() {
    document.getElementById('logoutModal').classList.remove('hidden');
  }

  // Close the logout modal
  function closeLogoutModal() {
    document.getElementById('logoutModal').classList.add('hidden');
  }

  // Confirm logout and redirect to the logout script
  function confirmLogout() {
    window.location.href = '../logout.php';
  }
</script>