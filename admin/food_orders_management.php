<?php
// Start session and include database connection
session_start();
include '../db_connection.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: login.php"); // Redirect to login if not logged in as admin
  exit();
}

$orders = [];

// Fetch all food orders
$orderQuery = "SELECT id, name, email, delivery_location, location_number, food_name, quantity, price, order_status, order_time FROM food_order";
$orderResult = mysqli_query($conn, $orderQuery);
while ($row = mysqli_fetch_assoc($orderResult)) {
  $orders[] = $row;
}

// Handle AJAX request for updating order status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['action'])) {
  $orderId = $_POST['order_id'];
  $action = $_POST['action'];
  $newStatus = $action === 'confirm' ? 'Confirmed' : 'Cancelled';

  $updateQuery = "UPDATE food_order SET order_status = '$newStatus' WHERE id = '$orderId'";
  if (mysqli_query($conn, $updateQuery)) {
    echo json_encode(['status' => 'success', 'newStatus' => $newStatus]);
  } else {
    echo json_encode(['status' => 'error', 'message' => "Failed to update order: " . mysqli_error($conn)]);
  }
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Food Orders</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex justify-center">
  <?php include "sidebar.php" ?>
  <div class="container mx-auto p-8">
    <h2 class="text-4xl font-semibold mb-6 text-center text-gray-800">Manage Food Orders</h2>

    <!-- Order Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-lg">
        <thead class="bg-gray-200">
          <tr>
            <th class="border px-4 py-3 text-left font-semibold">Name</th>
            <th class="border px-4 py-3 text-left font-semibold">Email</th>
            <th class="border px-4 py-3 text-left font-semibold">Delivery Location</th>
            <th class="border px-4 py-3 text-left font-semibold">Location No.</th>
            <th class="border px-4 py-3 text-left font-semibold">Food Name</th>
            <th class="border px-4 py-3 text-left font-semibold">Quantity</th>
            <th class="border px-4 py-3 text-left font-semibold">Order Status</th>
            <th class="border px-4 py-3 text-left font-semibold">Order Time</th>
            <th class="border px-4 py-3 text-left font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $order): ?>
            <tr class="hover:bg-gray-100 transition duration-150"
              data-order-id="<?= htmlspecialchars($order['id'], ENT_QUOTES, 'UTF-8'); ?>">
              <td class="border px-4 py-3"><?= htmlspecialchars($order['name']); ?></td>
              <td class="border px-4 py-3"><?= htmlspecialchars($order['email']); ?></td>
              <td class="border px-4 py-3"><?= htmlspecialchars($order['delivery_location']); ?></td>
              <td class="border px-4 py-3"><?= htmlspecialchars($order['location_number']); ?></td>
              <td class="border px-4 py-3"><?= htmlspecialchars($order['food_name']); ?></td>
              <td class="border px-4 py-3"><?= htmlspecialchars($order['quantity']); ?></td>
              <td class="border px-4 py-3">
                <span
                  class="<?= $order['order_status'] === 'Pending' ? 'text-yellow-500' : ($order['order_status'] === 'Confirmed' ? 'text-green-500' : 'text-red-500') ?>">
                  <?= htmlspecialchars($order['order_status']); ?>
                </span>
              </td>
              <?php $formattedTime = (new DateTime($order['order_time']))->format('F j, Y, g:i A'); ?>
              <td class="border px-4 py-3"><?= htmlspecialchars($formattedTime); ?></td>
              <td class="border px-4 py-3">
                <button class="text-blue-500 hover:underline"
                  onclick="openConfirmModal('confirm', <?= htmlspecialchars($order['id'], ENT_QUOTES, 'UTF-8'); ?>)">
                  Confirm
                </button>
                <button class="text-red-500 hover:underline ml-2"
                  onclick="openConfirmModal('cancel', <?= htmlspecialchars($order['id'], ENT_QUOTES, 'UTF-8'); ?>)">
                  Cancel
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Centered Confirmation Modal -->
  <div id="confirmModal" class="fixed inset-0 flex items-center justify-center bg-gray-700 bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded shadow-lg text-center">
      <p class="text-gray-800 font-semibold mb-4">Are you sure you want to <span id="modalActionText"></span> this
        order?</p>
      <input type="hidden" id="modalOrderId">
      <input type="hidden" id="modalAction">
      <button type="button" onclick="submitOrderAction()"
        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Yes</button>
      <button type="button" onclick="closeModal()"
        class="ml-2 bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">No</button>
    </div>
  </div>

  <script>
    function openConfirmModal(action, orderId) {
      document.getElementById('modalOrderId').value = orderId;
      document.getElementById('modalAction').value = action;
      document.getElementById('modalActionText').innerText = action === 'confirm' ? 'confirm' : 'cancel';
      document.getElementById('confirmModal').style.display = 'flex';
    }

    function closeModal() {
      document.getElementById('confirmModal').style.display = 'none';
    }

    function submitOrderAction() {
      const orderId = document.getElementById('modalOrderId').value;
      const action = document.getElementById('modalAction').value;

      // Perform AJAX request to update order status
      const xhr = new XMLHttpRequest();
      xhr.open('POST', '', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onload = function () {
        if (xhr.status === 200) {
          const response = JSON.parse(xhr.responseText);
          if (response.status === 'success') {
            // Update the order status in the table
            const statusCell = document.querySelector(`tr[data-order-id="${orderId}"] td:nth-child(7) span`);
            statusCell.innerText = response.newStatus;
            statusCell.className = response.newStatus === 'Confirmed' ? 'text-green-500' : 'text-red-500';
          } else {
            alert('Error: ' + response.message);
          }
          closeModal();
        } else {
          alert('Request failed. Status: ' + xhr.status);
        }
      };

      xhr.onerror = function () {
        alert('Request error.');
      };

      xhr.send(`order_id=${orderId}&action=${action}`);
    }
  </script>
</body>

</html>