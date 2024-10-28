<?php
// Start session and check if user is logged in
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}

// Database connection
require 'db_connection.php';

// Check if the user is banned
$username = $_SESSION['username'];
$user_check_query = "SELECT is_banned FROM users WHERE username = '" . mysqli_real_escape_string($conn, $username) . "'";
$user_result = mysqli_query($conn, $user_check_query);
$user = mysqli_fetch_assoc($user_result);

// Check if user is banned
$is_banned = $user['is_banned'] == 1;

// Initialize search query
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch food items from the database based on search input
$query = "SELECT * FROM food_items WHERE name LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'";
$result = mysqli_query($conn, $query);

// Check if any foods were found
$food_items_found = mysqli_num_rows($result) > 0;

// Handle form submission for order confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = $_SESSION['user_id']; // Ensure the user is logged in and you have their ID
  $food_id = mysqli_real_escape_string($conn, $_POST['food_id']);
  $quantity = mysqli_real_escape_string($conn, $_POST['quantity']); // Assuming quantity is part of the form
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $location_type = mysqli_real_escape_string($conn, $_POST['location_type']);
  $location_number = mysqli_real_escape_string($conn, $_POST['location_number']);
  $order_status = 'Pending';

  // Fetch the price of the food from the food table
  $food_query = "SELECT * FROM food_items WHERE id = '$food_id'";
  $food_result = mysqli_query($conn, $food_query);

  if ($food_row = mysqli_fetch_assoc($food_result)) {
    $price = $food_row['price'];
    $food_name = $food_row['name'];
    $image_url = $food_row['image_url'];

    // Insert order into the cart table
    $insert_query = "INSERT INTO cart (user_id, food_id, quantity, order_status, `name`, email, delivery_location, location_number, food_name, price, image_url) 
                     VALUES ('$user_id', '$food_id', '$quantity', '$order_status', '$name', '$email', '$location_type', '$location_number', '$food_name', '$price','$image_url')";

    if (mysqli_query($conn, $insert_query)) {
      $_SESSION['success_message'] = "Items added to cart!";
    } else {
      $_SESSION['modal_message'] = "Error placing order: " . mysqli_error($conn);
    }

  }

  // Redirect to refresh the page and display message
  header("Location: food.php");
  exit();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Food</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
  <script>
    // Function to close the message modal
    function closeModal() {
      document.getElementById('banModal').classList.add('hidden');
    }

    // Function to handle adding to cart or showing ban modal
    function handleAddToCart(foodId, foodName) {
      // Check if the user is banned
      <?php if ($is_banned): ?>
        // If banned, show the ban modal
        document.getElementById('banModal').classList.remove('hidden');
      <?php else: ?>
        // If not banned, open the order modal
        openOrderModal(foodId, foodName);
      <?php endif; ?>
    }

    function searchFood(event) {
      if (event.key === 'Enter') {
        document.getElementById('searchForm').submit();
      }
    }

    function openOrderModal(foodId, foodName) {
      document.getElementById('food_id').value = foodId;
      document.getElementById('food_name').value = foodName;
      document.getElementById('orderModal').classList.remove('hidden');
    }

    function closeOrderModal() {
      document.getElementById('orderModal').classList.add('hidden');
    }

    function clearSearchValue() {
      document.getElementById('searchInput').value = '';
      document.getElementById('searchForm').submit();
    }
  </script>
  <style>
    .active {
      font-weight: bold;
      color: black;
    }
  </style>
</head>

<body class="bg-gray-100">

  <!-- Navbar Include -->
  <?php include 'navbar.php'; ?>

  <!-- Modal for banned user (initially hidden) -->
  <div id="banModal" class="hidden fixed inset-0 bg-gray-300 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-8 rounded-md shadow-md max-w-lg w-full text-left">
      <h2 class="text-xl font-bold mb-4">Access Denied</h2>
      <p>Your account is temporarily banned. Please contact support for more information.</p>
      <button type="button" onclick="closeModal()"
        class="bg-red-500 hover:bg-red-700 text-white py-2 px-4 rounded mt-4">Close</button>
    </div>
  </div>

  <!-- Container with padding on the left and right -->
  <div class="container mx-auto px-4 py-4">

    <!-- Search bar -->
    <div class="my-4">
      <form method="GET" id="searchForm" class="flex relative">
        <input type="text" name="search" placeholder="Search food items..."
          class="flex-grow p-2 border rounded-l-md h-14" value="<?php echo htmlspecialchars($search); ?>"
          onkeypress="searchFood(event)" id="searchInput">
        <button type="submit" class="bg-blue-500 text-white px-4 rounded-r-md">Search</button>

        <!-- Clear Button -->
        <button type="button" id="clearSearch" class="absolute right-20 top-1/2 transform -translate-y-1/2 p-2"
          onclick="clearSearchValue()">
          <span class="text-xl w-14 font-bold h-full flex justify-center items-center text-gray-600">X</span>
        </button>
      </form>
    </div>

    <!-- Food Items Display -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <?php if ($food_items_found): ?>
        <?php while ($food = mysqli_fetch_assoc($result)): ?>
          <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <img src="<?php echo $food['image_url']; ?>" alt="<?php echo htmlspecialchars($food['name']); ?>"
              class="w-full h-48 object-cover">
            <div class="p-4">
              <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($food['name']); ?></h3>
              <p class="text-gray-600"><?php echo htmlspecialchars($food['description']); ?></p>
              <p class="text-green-600 font-bold mt-2">$<?php echo htmlspecialchars($food['price']); ?></p>
              <button
                onclick="handleAddToCart(<?php echo $food['id']; ?>, '<?php echo htmlspecialchars($food['name']); ?>')"
                class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add to Cart</button>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="text-red-600">No food items found for your search:
          <strong><?php echo htmlspecialchars($search); ?></strong>
        </p>
      <?php endif; ?>
    </div>

    <!-- Order Modal (hidden initially) -->
    <div id="orderModal" class="hidden fixed inset-0 bg-gray-700 bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-8 rounded-md shadow-md max-w-lg w-full text-left">
        <h2 class="text-xl font-bold mb-4">Order Details</h2>
        <form method="POST" action="">
          <input type="hidden" id="food_id" name="food_id">
          <input type="hidden" id="food_name" name="food_name">
          <div class="mb-4">
            <label for="name" class="block mb-2">Name:</label>
            <input type="text" name="name" id="name" required class="w-full border rounded p-2" placeholder="Your Name">
          </div>
          <div class="mb-4">
            <label for="email" class="block mb-2">Email:</label>
            <input type="email" name="email" id="email" required class="w-full border rounded p-2"
              placeholder="Your Email">
          </div>
          <div class="mb-4">
            <label for="quantity" class="block mb-2">Quantity:</label>
            <input type="number" name="quantity" id="quantity" required class="w-full border rounded p-2"
              placeholder="Quantity">
          </div>
          <div class="mb-4">
            <label for="location_type" class="block mb-2">Location:</label>
            <select name="location_type" id="location_type" class="w-full border rounded p-2">
              <option value="Room">Room</option>
              <option value="Table">Table</option>
            </select>
          </div>
          <div class="mb-4">
            <label for="location_number" class="block mb-2">Room/Table Number:</label>
            <input type="text" name="location_number" id="location_number" required class="w-full border rounded p-2">
          </div>
          <button type="submit" class="bg-green-500 hover:bg-green-700 text-white py-2 px-4 rounded">Confirm
            Order</button>
          <button type="button" onclick="closeOrderModal()"
            class="bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded ml-2">Cancel</button>
        </form>
      </div>
    </div>

    <!-- Success Modal (hidden initially) -->
    <div id="successModal" class="hidden fixed inset-0 bg-gray-700 bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-8 rounded-md shadow-md max-w-lg w-full text-left">
        <h2 class="text-xl font-bold mb-1">Success!</h2>
        <p><?php echo isset($_SESSION['success_message']) ? htmlspecialchars($_SESSION['success_message']) : ''; ?></p>
        <button type="button" onclick="closeSuccessModal()"
          class="bg-green-500 hover:bg-green-700 text-white py-2 px-4 mt-2 rounded">Close</button>
      </div>
    </div>


    <?php
    // Clear modal message session variable after showing it
    unset($_SESSION['modal_message']);
    ?>

  </div>
  <script>
    // Function to close the success modal
    function closeSuccessModal() {
      document.getElementById('successModal').classList.add('hidden');
    }

    // Show the success modal if the session variable is set
    window.onload = function () {
      <?php if (isset($_SESSION['success_message'])): ?>
        document.getElementById('successModal').classList.remove('hidden');
        <?php unset($_SESSION['success_message']); // Clear the message after displaying ?>
      <?php endif; ?>
    };
  </script>
</body>

</html>