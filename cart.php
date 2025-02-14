<?php
session_start();
include "db_connection.php";

// Ensure no output before the header
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
$redirectToOrders = "";
$user_id = $_SESSION['user_id'];
$cartItems = [];
$totalBill = 0;

// Fetch cart items
$query = "SELECT *, (price * quantity) AS total_price FROM cart WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);

if ($result) {
  $cartItems = mysqli_fetch_all($result, MYSQLI_ASSOC);
  foreach ($cartItems as $item) {
    $totalBill += $item['total_price'];
  }
} else {
  $errorMessage = "Failed to fetch cart items: " . mysqli_error($conn);
}

// Handle Remove Item Action
if (isset($_GET['remove_id'])) {
  $removeId = $_GET['remove_id'];
  $removeQuery = "DELETE FROM cart WHERE id = $removeId AND user_id = $user_id";
  if (mysqli_query($conn, $removeQuery)) {
    // Ensure header is called before output
    header("Location: cart.php");
    exit;
  } else {
    echo "Error removing item: " . mysqli_error($conn);
  }
}

// Handle Update Item Action
if (isset($_POST['update_id'])) {
  $updateId = $_POST['update_id'];
  $updateQuantity = $_POST['quantity'];
  $updateLocation = $_POST['location'];
  $updateLocationNumber = $_POST['location_number'];

  $updateQuery = "UPDATE cart SET quantity = '$updateQuantity', delivery_location = '$updateLocation', location_number = '$updateLocationNumber' WHERE id = $updateId AND user_id = $user_id";

  if (mysqli_query($conn, $updateQuery)) {
    // Ensure header is called before output
    header("Location: cart.php");
    exit;
  } else {
    echo "Error updating item: " . mysqli_error($conn);
  }
}

// Place order logic
if (isset($_GET["order"]) && $_GET["order"] === "true") {
  $query = "SELECT * FROM cart WHERE user_id = '$user_id'";
  $cartResult = mysqli_query($conn, $query);

  if ($cartResult && mysqli_num_rows($cartResult) > 0) {
    $orderSuccess = true;
    while ($item = mysqli_fetch_assoc($cartResult)) {
      $food_id = $item['food_id'];
      $quantity = $item['quantity'];
      $name = $item['name'];
      $email = $item['email'];
      $delivery_location = $item['delivery_location'];
      $location_number = $item['location_number'];
      $food_name = $item['food_name'];
      $image_url = $item['image_url'];
      $price = $item['price'];
      $total_price = $price * $quantity;

      $insertQuery = "INSERT INTO food_order 
                    (user_id, food_id, quantity, name, email, delivery_location, location_number, food_name, order_status, price, image_url, total_price)
                    VALUES 
                    ('$user_id', '$food_id', '$quantity', '$name', '$email', '$delivery_location', '$location_number', '$food_name', 'Pending', '$price', '$image_url', '$total_price')";

      $insertResult = mysqli_query($conn, $insertQuery);

      // Check for insertion success
      if (!$insertResult) {
        $orderSuccess = false;
        $errorMessage = "Failed to place order. Please try again.";
        break;
      }
    }

    // Delete items from cart if order placement was successful

    if ($orderSuccess) {
      $deleteCartQuery = "DELETE FROM cart WHERE user_id = '$user_id'";
      $deleteCartResult = mysqli_query($conn, $deleteCartQuery);
      if ($deleteCartResult) {
        $redirectToOrders = true; // Set flag to show the success modal and redirect
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cart</title>
  <link href="public/style.css" rel="stylesheet">

  <style>
    .active {
      font-weight: bold;
      color: black;
    }
  </style>

</head>

<body class="bg-gray-100">
  <?php
  include "navbar.php"; ?>
  <div class="container mx-auto p-5 text-center">
    <span class="border-b-4 border-gray-300 pb-1 text-3xl font-bold text-blue-600">Your Cart</span>
    <?php if (!empty($errorMessage)): ?>
      <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" id="message" role="alert">
        <span><?= htmlspecialchars($errorMessage) ?></span>
      </div>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>
      <div class="flex items-center justify-center h-40 bg-gray-100">
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg text-center shadow-md"
          role="alert">
          <span class="font-semibold">Your cart is empty. Add items to get started!</span>
        </div>
      </div>
    <?php else: ?>
      <div class="overflow-x-auto mt-6">
        <table class="min-w-full bg-white shadow rounded-lg">
          <thead>
            <tr class="bg-gray-200">
              <th class="py-3 px-4 text-left">Food Item</th>
              <th class="py-3 px-4 text-left">Quantity</th>
              <th class="py-3 px-4 text-left">Price</th>
              <th class="py-3 px-4 text-left">Total Price</th>
              <th class="py-3 px-4 text-left">Location</th>
              <th class="py-3 px-4 text-left">Location Number</th>
              <th class="py-3 px-4 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cartItems as $item): ?>
              <tr class="border-b hover:bg-gray-100">
                <td class="py-2 px-4"><?= htmlspecialchars($item['food_name']) ?></td>
                <td class="py-2 px-4"><?= htmlspecialchars($item['quantity']) ?></td>
                <td class="py-2 px-4">Rs.<?= number_format($item['price'], 2) ?></td>
                <td class="py-2 px-4">Rs.<?= number_format($item['total_price'], 2) ?></td>
                <td class="py-2 px-4"><?= htmlspecialchars($item['delivery_location']) ?></td>
                <td class="py-2 px-4"><?= htmlspecialchars($item['location_number']) ?></td>
                <td class="py-2 px-4 text-center">
                  <a href="cart.php?remove_id=<?= $item['id'] ?>" class="bg-red-500 rounded-md text-white px-2 py-1 ml-2">
                    Remove
                  </a>
                  <button class="bg-blue-500 rounded-md text-white px-2 py-1 ml-2 update-button"
                    onclick="openUpdateModal(<?= $item['id'] ?>, '<?= $item['delivery_location'] ?>', <?= $item['location_number'] ?>, <?= $item['quantity'] ?>)">
                    Update
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="mt-4 text-right">
        <div class="bg-white p-6 rounded-lg shadow-md mt-6">
          <h2 class="text-xl font-bold text-gray-800">Grand Total:</h2>
          <p id="totalBillAmount" class="text-2xl font-semibold text-green-600">Rs.<?= number_format($totalBill, 2) ?></p>
        </div>
        <a href="cart.php?order=true"
          class="mt-4 inline-block bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
          Order Now
        </a>
      </div>
    <?php endif; ?>
  </div>

  <!-- Update Modal -->
  <div id="updateModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg">
      <h2 class="text-blue-500 text-xl font-bold mb-4">Update Item</h2>
      <form action="cart.php" method="POST">
        <input type="hidden" name="update_id" id="updateId">
        <div class="mb-4">
          <label for="updateQuantity" class="block text-gray-700">Quantity:</label>
          <input type="number" id="updateQuantity" name="quantity" min="1"
            class="w-full p-2 border border-gray-300 rounded">
        </div>
        <div class="mb-4">
          <label for="updateLocation" class="block text-gray-700">Location:</label>
          <select id="updateLocation" name="location" class="w-full p-2 border border-gray-300 rounded">
            <option value="" disabled selected>Select a location</option>
            <option value="Room">Room</option>
            <option value="Table">Table</option>
          </select>
        </div>
        <div class="mb-4">
          <label for="updateLocationNumber" class="block text-gray-700">Location Number:</label>
          <input type="text" id="updateLocationNumber" name="location_number"
            class="w-full p-2 border border-gray-300 rounded">
        </div>
        <button type="submit"
          class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 w-full">Update</button>
      </form>
      <button id="closeModalButton"
        class="mt-4 bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 w-full">Cancel</button>
    </div>
  </div>

  <!-- Success Message Modal -->
  <?php if ($redirectToOrders): ?>
    <div id="successModal" class="fixed inset-0 flex items-center justify-center z-50 bg-gray-800 bg-opacity-50">
      <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-semibold text-green-500">Order Successful!</h2>
        <p class="mt-4 text-gray-700">Your order has been placed successfully. Click the button below to view your orders.
        </p>
        <div class="mt-6 flex justify-center">
          <a href="my_orders.php">
            <button class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none">
              View My Orders
            </button>
          </a>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <script>
    function openUpdateModal(id, location, locationNumber, quantity) {
      document.getElementById("updateModal").style.display = "flex";
      document.getElementById("updateId").value = id;
      document.getElementById("updateLocation").value = location;
      document.getElementById("updateLocationNumber").value = locationNumber;
      document.getElementById("updateQuantity").value = quantity;
    }

    document.getElementById("closeModalButton").onclick = function () {
      document.getElementById("updateModal").style.display = "none";
    }

    // Optional: Automatically close the modal after a few seconds
    setTimeout(() => {
      const modal = document.getElementById('successModal');
      if (modal) {
        modal.style.display = 'none';  // Hide the modal after 5 seconds
      }
    }, 5000); // 5 seconds
  </script>
</body>

</html>