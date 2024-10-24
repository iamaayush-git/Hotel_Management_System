<!-- navbar.php -->
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
        <!-- Show 'My Bookings' only if user is logged in -->
        <li><a href="my_bookings.php" class="text-gray-700 hover:text-blue-500">My Bookings</a></li>
        <li class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</li>
        <li><a href="logout.php" class="text-red-500 hover:text-red-700">Logout</a></li>
      <?php else: ?>
        <li><a href="register.php" class="text-gray-700 hover:text-blue-500">Register</a></li>
        <li><a href="login.php" class="text-gray-700 hover:text-blue-500">Login</a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>