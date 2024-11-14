<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$orderSuccess = false; // Track success of order placement
$errorMessage = "";
$orderList = [];

// Fetch all cart items for the current user and insert them into food_order if "Order Now" is clicked
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
      if (!$deleteCartResult) {
        $errorMessage = "Failed to clear cart after order. Please contact support.";
      }
    }
  }
}

// Handle deletion
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $query = "DELETE FROM cart WHERE id = $id AND user_id = $user_id";
  mysqli_query($conn, $query);
  header("Location: cart.php");
  exit();
}

// Handle update
if (isset($_POST['update'])) {
  $id = intval($_POST['id']);
  $quantity = intval($_POST['quantity']);

  if ($quantity > 0) {
    $query = "UPDATE cart SET quantity = $quantity, total_price = (price * $quantity) WHERE id = $id AND user_id = $user_id";
    mysqli_query($conn, $query);
    echo json_encode(['status' => 'success', 'message' => 'Quantity updated successfully!']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Quantity must be greater than zero!']);
  }
  exit();
}

// Fetch cart items for logged-in user with calculated total_price
$query = "SELECT *, (price * quantity) AS total_price FROM cart WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);
$cartItems = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Calculate total bill
$totalBill = 0;
foreach ($cartItems as $item) {
  $totalBill += $item['total_price'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cart</title>
  <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> -->
  <link href="public/style.css" rel="stylesheet">

  <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
</head>

<body class="bg-gray-100">
  <?php include "navbar.php"; ?>

  <div class="container mx-auto p-5">
    <h1 class="text-3xl font-bold mb-5">Your Cart</h1>

    <?php if ($errorMessage): ?>
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
      <!-- Cart Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white shadow rounded-lg">
          <thead>
            <tr class="bg-gray-200">
              <th class="py-3 px-4 text-left">Food Item</th>
              <th class="py-3 px-4 text-left">Quantity</th>
              <th class="py-3 px-4 text-left">Price</th>
              <th class="py-3 px-4 text-left">Total Price</th>
              <th class="py-3 px-4 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cartItems as $item): ?>
              <tr class="border-b hover:bg-gray-100">
                <td class="py-2 px-4"><?= htmlspecialchars($item['food_name']) ?></td>
                <td class="py-2 px-4"><?= htmlspecialchars($item['quantity']) ?></td>
                <td class="py-2 px-4">$<?= number_format($item['price'], 2) ?></td>
                <td class="py-2 px-4">$<?= number_format($item['total_price'], 2) ?></td>
                <td class="py-2 px-4 text-center">
                  <button class="bg-red-500 rounded-md text-white px-2 py-1 ml-2 delete-button"
                    data-id="<?= $item['id'] ?>">Remove</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Total Bill Section -->
      <div class="mt-4 text-right">
        <div class="bg-white p-6 rounded-lg shadow-md mt-6">
          <h2 class="text-xl font-bold text-gray-800">Grand Total:</h2>
          <p id="totalBillAmount" class="text-2xl font-semibold text-green-600">$<?= number_format($totalBill, 2) ?></p>
        </div>
        <!-- Order Now Button -->
        <a href="cart.php?order=true"
          class="mt-4 inline-block bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
          Order Now
        </a>
      </div>
    <?php endif; ?>
  </div>
  <!-- Success Modal -->
  <div id="successModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg text-center">
      <h2 class="text-green-500 text-xl font-bold mb-4">Order Placed Successfully!</h2>
      <p class="text-gray-700">Your order has been added successfully. Thank you for your purchase!</p>
      <button onclick="closeSuccessModal()"
        class="mt-4 bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Close</button>
    </div>
  </div>

  <script>
    // Function to show the success modal
    function openSuccessModal() {
      document.getElementById('successModal').classList.remove('hidden');
    }

    // Function to close the success modal
    function closeSuccessModal() {
      document.getElementById('successModal').classList.add('hidden');
      window.location.href = "my_orders.php"; // Redirect if needed
    }

    <?php if ($insertResult && $deleteCartResult) { ?>
      openSuccessModal()
    <?php } ?>
  </script>
</body>

</html>