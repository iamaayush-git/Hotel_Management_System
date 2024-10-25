<!-- navbar.php -->
<style>
  #loginSuccessModal {
    animation: fadeInScale 0.4s ease-in-out;
  }

  @keyframes fadeInScale {
    from {
      opacity: 0;
      transform: scale(0.9);
    }

    to {
      opacity: 1;
      transform: scale(1);
    }
  }

  #logoutModal {
    animation: fadeIn 0.4s ease-in-out;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: scale(0.9);
    }

    to {
      opacity: 1;
      transform: scale(1);
    }
  }
</style>
<nav class="bg-white bg-opacity-85 shadow-md">
  <div class="container mx-auto p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold">Hotel Management</h1>
    <ul class="flex space-x-6">
      <li><a href="index.php"
          class="text-gray-700 hover:text-blue-500 <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">Home</a>
      </li>
      <li><a href="about.php"
          class="text-gray-700 hover:text-blue-500 <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'active' : ''; ?>">About</a>
      </li>
      <li><a href="all_rooms.php"
          class="text-gray-700 hover:text-blue-500 <?php echo (basename($_SERVER['PHP_SELF']) == 'all_rooms.php') ? 'active' : ''; ?>">Our
          Rooms</a>
      </li>
      <li><a href="contact.php"
          class="text-gray-700 hover:text-blue-500 <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>">Contact
          Us</a>
      </li>

      <?php if (isset($_SESSION['username'])): ?>
        <li><a href="my_bookings.php" class="text-gray-700 hover:text-blue-500">My Bookings</a></li>
        <li class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</li>
        <li>
          <button onclick="openLogoutModal()" class="text-red-500 hover:text-red-700">Logout</button>
        </li>
      <?php else: ?>
        <li><a href="register.php" class="text-gray-700 hover:text-blue-500">Register</a></li>
        <li><a href="login.php" class="text-gray-700 hover:text-blue-500">Login</a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
  <div
    class="bg-white p-8 rounded-lg shadow-2xl max-w-sm text-center transform transition-all duration-300 ease-in-out scale-95 hover:scale-100 border-t-4 border-red-600">
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

<!-- Login Success Modal -->
<?php if (isset($_SESSION['login_success']) && $_SESSION['login_success']): ?>
  <div id="loginSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
    <div
      class="bg-white p-10 rounded-lg shadow-xl max-w-sm text-center transform transition-all duration-300 ease-in-out scale-95 hover:scale-100 border-t-8 border-blue-500">
      <h2 class="text-3xl font-bold text-gray-800 mb-4">Welcome Back!</h2>
      <p class="mb-6 text-gray-600">Login was successful. Enjoy your session.</p>
      <button onclick="closeLoginSuccessModal()"
        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-md shadow-lg transition duration-300 ease-in-out transform hover:scale-105">Close</button>
    </div>
  </div>
  <?php unset($_SESSION['login_success']); // Unset the session variable after showing the message ?>
<?php endif; ?>

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
    window.location.href = 'logout.php';
  }

  // Close the login success modal
  function closeLoginSuccessModal() {
    document.getElementById('loginSuccessModal').style.display = 'none';
  }
</script>