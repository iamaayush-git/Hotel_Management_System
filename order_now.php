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
$successMessage = "";
$errorMessage = "";
$orderList = [];

if (isset($_GET["cancel"])) {
  $id = $_GET["cancel"];

  // Update the order_status to 'Cancel' where the order ID matches
  $sql = "UPDATE food_order SET order_status = 'Cancelled' WHERE id = '$id'";
  $result = mysqli_query($conn, $sql);

  if ($result) {
    $successMessage = "Order status updated to Cancel";
    header("Location: order_now.php");
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
    header("Location: order_now.php");
    exit();
  } else {
    $successMessage = "Something went wrong: " . mysqli_error($conn);
  }
}

// Step 1: Fetch all cart items for the current user and insert them into food_order
$query = "SELECT * FROM cart WHERE user_id = '$user_id'";
$cartResult = mysqli_query($conn, $query);

if ($cartResult && mysqli_num_rows($cartResult) > 0) {
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

    $insertQuery = "INSERT INTO food_order (user_id, food_id, quantity, name, email, delivery_location, location_number, food_name, order_status, price, image_url, total_price)
                        VALUES ('$user_id', '$food_id', '$quantity', '$name', '$email', '$delivery_location', '$location_number', '$food_name', 'Pending', '$price', '$image_url', '$total_price')";

    if (mysqli_query($conn, $insertQuery)) {
      $successMessage = "Order placed successfully!";
    } else {
      $errorMessage = "Error: " . mysqli_error($conn);
    }
  }

  // Clear cart after successful insertion
  $deleteCartQuery = "DELETE FROM cart WHERE user_id = '$user_id'";
  mysqli_query($conn, $deleteCartQuery);
}

// Step 2: Fetch orders for the logged-in user
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
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
  <?php include "navbar.php" ?>

  <!-- Success Modal -->
  <?php if ($successMessage): ?>
    <div class="fixed inset-0 flex items-center justify-center bg-gray-700 bg-opacity-50">
      <div class="bg-white p-6 rounded shadow-lg text-center">
        <p class="text-green-500 font-bold"><?php echo $successMessage; ?></p>
        <button onclick="this.parentElement.parentElement.style.display='none';"
          class="mt-4 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Close</button>
      </div>
    </div>
  <?php endif; ?>

  <!-- Error Modal -->
  <?php if ($errorMessage): ?>
    <div class="fixed inset-0 flex items-center justify-center bg-gray-700 bg-opacity-50">
      <div class="bg-white p-6 rounded shadow-lg text-center">
        <p class="text-red-500 font-bold"><?php echo $errorMessage; ?></p>
        <button onclick="this.parentElement.parentElement.style.display='none';"
          class="mt-4 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Close</button>
      </div>
    </div>
  <?php endif; ?>

  <!-- Order List -->
  <div class="container mx-auto mt-10">
    <h2 class="text-4xl font-semibold mb-8 text-center text-gray-800 drop-shadow-sm tracking-wide">
      <span class="border-b-4 border-gray-300 pb-1 text-blue-600">Your Orders</span>
    </h2>

    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border border-gray-300 shadow-lg rounded-lg">
        <thead class="bg-gray-200">
          <tr>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Name</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Food Name</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Image</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Location</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Location No.</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Quantity</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Price</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Total Price</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Order Status</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orderList as $order): ?>
            <tr class="hover:bg-gray-100 transition-colors duration-200">
              <td class="border px-6 py-4"><?php echo $order['name']; ?></td>
              <td class="border px-6 py-4"><?php echo $order['food_name']; ?></td>
              <td class="py-2 px-4">
                <img src="<?= htmlspecialchars($order['image_url']) ?>" alt="<?= htmlspecialchars($order['food_name']) ?>"
                  class="w-16 h-16 object-cover">
              </td>
              <td class="border px-6 py-4"><?php echo $order['delivery_location']; ?></td>
              <td class="border px-6 py-4"><?php echo $order['location_number']; ?></td>
              <td class="border px-6 py-4"><?php echo $order['quantity']; ?></td>
              <td class="border px-6 py-4"><?php echo $order['price']; ?></td>
              <td class="border px-6 py-4"><?php echo $order['total_price']; ?></td>
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

  <!-- Remove Confirmation Modal -->
  <div id="confirmCancelModal" class="fixed inset-0 flex items-center justify-center bg-gray-700 bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded shadow-lg text-center">
      <p class="text-gray-700 font-semibold mb-4">Are you sure you want to cancel this order?</p>
      <button id="confirmCancelBtn" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 mr-2">Yes</button>
      <button onclick="closeConfirmCancelModal()"
        class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">No</button>
    </div>
  </div>

  <!-- Modal Scripts -->
  <script>
    let cancelOrderId = null;

    function openConfirmCancelModal(orderId) {
      cancelOrderId = orderId;
      console.log("running");
      document.getElementById('confirmCancelModal').classList.remove('hidden');
    }
    function closeConfirmCancelModal() {
      document.getElementById('confirmCancelModal').classList.add('hidden');
      document.getElementById('confirmCancelModal').classList.add('hidden');
      removeOrderId = null;
    }
    document.getElementById('confirmCancelBtn').addEventListener('click', function () {
      if (cancelOrderId) {
        window.location.href = "order_now.php?cancel=" + cancelOrderId;
      }
    });
    function removeItem(orderId) {
      window.location.href = "order_now.php?remove=" + orderId;
    }
  </script>

</body>

</html>