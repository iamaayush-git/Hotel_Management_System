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

// Step 2: Fetch all cart items for the current user and insert them into food_order
$query = "SELECT * FROM cart WHERE user_id = '$user_id'";
$cartResult = mysqli_query($conn, $query);

if ($cartResult && mysqli_num_rows($cartResult) > 0) {
  while ($item = mysqli_fetch_assoc($cartResult)) {
    $food_id = $item['food_id'];
    $user_id = $item['user_id'];
    $quantity = $item['quantity'];
    $name = $item['name'];
    $email = $item['email'];
    $delivery_location = $item['delivery_location'];
    $location_number = $item['location_number'];
    $food_name = $item['food_name'];
    $food_name = $item['image_url'];
    $delivery_location = $item['delivery_location'];

    $insertQuery = "INSERT INTO food_order (user_id, food_id, quantity, name, email, delivery_location, location_number, order_time, food_name, order_status)
                        VALUES ('$user_id', '$food_id', '$quantity', '$name', '$email', '$delivery_location', '$location_number', '$order_time','$food_name', 'Pending')";
    // food fetched and inserted into food_orders table                         

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

// Step 4: Fetch orders for the logged-in user
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

  <!-- Order List -->
  <div class="container mx-auto mt-10">
    <h2 class="text-3xl font-bold mb-6 text-center">Your Orders</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border border-gray-300 shadow-lg rounded-lg">
        <thead class="bg-gray-200">
          <tr>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">ID</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Food ID</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Quantity</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Order Status</th>
            <th class="border px-6 py-3 text-left text-gray-700 uppercase font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orderList as $order): ?>
            <tr class="hover:bg-gray-100 transition-colors duration-200">
              <td class="border px-6 py-4"><?php echo $order['id']; ?></td>
              <td class="border px-6 py-4"><?php echo $order['food_id']; ?></td>
              <td class="border px-6 py-4"><?php echo $order['quantity']; ?></td>
              <td class="border px-6 py-4">
                <span class="<?php echo $order['order_status'] === 'Pending' ? 'text-yellow-500' : 'text-gray-400'; ?>">
                  <?php echo $order['order_status']; ?>
                </span>
              </td>
              <td class="border px-6 py-4">
                <?php if ($order['order_status'] === 'Pending'): ?>
                  <button class="text-red-500 hover:underline"
                    onclick="confirmRemove(<?php echo $order['id']; ?>)">Remove</button>
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


  <!-- Modal Scripts -->
  <script>
    function confirmRemove(orderId) {
      if (confirm("Are you sure you want to remove this order?")) {
        // Add AJAX call or redirect to PHP script to handle removal
        window.location.href = "remove_order.php?id=" + orderId;
      }
    }

    function confirmUpdate(orderId) {
      // Implement the modal for updating the order (add modal logic here)
      alert("Update logic for order ID: " + orderId);
    }
  </script>

</body>

</html>