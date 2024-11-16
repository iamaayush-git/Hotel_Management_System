<?php
// Start session and include database connection
session_start();
include './db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php"); // Redirect to login if not logged in
  exit();
}

$user_id = $_SESSION['user_id'];
$orderList = [];

if (isset($_GET["cancel"])) {
  $id = $_GET["cancel"];

  // Update the order_status to 'Cancel' where the order ID matches
  $sql = "UPDATE food_order SET order_status = 'Cancelled' WHERE id = '$id'";
  $result = mysqli_query($conn, $sql);

  if ($result) {
    $successMessage = "Order status updated to Cancel";
    header("Location: my_orders.php");
    exit();
  } else {
    $successMessage = "Something went wrong: " . mysqli_error($conn);
  }
}
if (isset($_GET["remove"])) {
  $id = $_GET["remove"];

  // Remove the order where the order ID matches
  $sql = "DELETE FROM food_order WHERE id= '$id'";
  $result = mysqli_query($conn, $sql);

  if ($result) {
    $successMessage = "Order Removed Successfully";
    header("Location: my_orders.php");
    exit();
  } else {
    $successMessage = "Something went wrong: " . mysqli_error($conn);
  }
}

//  Fetch all cart items for the current user and insert them into food_order
$query = "SELECT * FROM cart WHERE user_id = '$user_id'";
$cartResult = mysqli_query($conn, $query);

// Fetch orders for the logged-in user
$orderQuery = "SELECT * FROM food_order WHERE user_id = '$user_id'";
$orderResult = mysqli_query($conn, $orderQuery);
while ($row = mysqli_fetch_assoc($orderResult)) {
  $orderList[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Order Now</title>
  <!-- <script src="https://cdn.tailwindcss.com"></script> -->
  <link href="public/style.css" rel="stylesheet">
  <style>
    .active {
      font-weight: bold;
      color: black;
    }
  </style>
</head>

<body>
  <?php include "navbar.php" ?>

  <!-- Order List -->
  <div class="container mx-auto mt-10">
    <h2 class="text-4xl font-semibold mb-8 text-center text-gray-800 drop-shadow-sm tracking-wide">
      <span class="border-b-4 border-gray-300 pb-1 text-3xl font-bold text-blue-600">Your Orders</span>
    </h2>
    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
      <div class="bg-white p-6 rounded-lg shadow-lg text-center">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Confirm Order</h2>
        <p class="text-gray-700">Are you sure you want to cancel this order?</p>
        <div class="mt-4 flex justify-center space-x-4">
          <button id="confirmCancelBtn"
            class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Yes</button>
          <button onclick="closeConfirmationModal()"
            class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400">No</button>
        </div>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border border-gray-300 shadow-lg rounded-lg">
        <thead class="bg-gray-200">
          <tr>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Image</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Name</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Food Name</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Location</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Location No.</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Quantity</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Price</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Total Price</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Time</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Order Status</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orderList as $order): ?>
            <tr class="hover:bg-gray-100 transition-colors duration-200">
              <td class="py-2 px-4">
                <img src="<?= htmlspecialchars($order['image_url']) ?>" alt="<?= htmlspecialchars($order['food_name']) ?>"
                  class="w-16 h-16 object-cover">
              </td>
              <td class="border px-6 py-4"><?php echo $order['name']; ?></td>
              <td class="border px-6 py-4"><?php echo $order['food_name']; ?></td>
              <td class="border px-6 py-4"><?php echo $order['delivery_location']; ?></td>
              <td class="border px-6 py-4"><?php echo $order['location_number']; ?></td>
              <td class="border px-6 py-4"><?php echo $order['quantity']; ?></td>
              <td class="border px-6 py-4"><?php echo $order['price']; ?></td>
              <td class="border px-6 py-4"><?php echo $order['total_price']; ?></td>
              <?php $formattedTime = (new DateTime($order['order_time']))->format('F j, Y, g:i A'); ?>
              <td class="border px-4 py-3"><?= htmlspecialchars($formattedTime); ?></td>
              <td class="border px-6 py-4">
                <span class="<?php echo $order['order_status'] === 'Pending' ? 'text-yellow-500' : 'text-gray-400'; ?>">
                  <?php echo $order['order_status']; ?>
                </span>
              </td>
              <td class="border px-6 py-4">
                <?php if ($order['order_status'] === 'Pending'): ?>
                  <button class="text-red-500 hover:underline"
                    onclick="openConfirmCancelModal(<?php echo $order['id']; ?>)">Cancel</button>
                <?php elseif ($order['order_status'] === 'Confirmed' || $order['order_status'] == 'Cancelled'): ?>
                  <button class="text-red-500 hover:underline"
                    onclick="removeItem(<?php echo $order['id']; ?>)">Remove</button>
                <?php else: ?>
                  <span class="text-gray-400">N/A</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <script>
    let cancelOrderId = null;

    function openConfirmCancelModal(orderId) {
      cancelOrderId = orderId;
      document.getElementById('confirmationModal').classList.remove('hidden');
    }
    function closeConfirmationModal() {
      document.getElementById('confirmationModal').classList.add('hidden');
      removeOrderId = null;
    }
    document.getElementById('confirmCancelBtn').addEventListener('click', function () {
      if (cancelOrderId) {
        window.location.href = "my_orders.php?cancel=" + cancelOrderId;
      }
    });
    function removeItem(orderId) {
      window.location.href = "my_orders.php?remove=" + orderId;
    }
  </script>

</body>

</html>