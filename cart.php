<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

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
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    .active {
      font-weight: bold;
      color: black;
    }
  </style>
</head>

<body class="bg-gray-100">
  <?php include "navbar.php"; ?>

  <div class="container mx-auto p-5">
    <h1 class="text-3xl font-bold mb-5">Your Cart</h1>

    <?php if (isset($message)): ?>
      <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" id="message" role="alert">
        <span><?= htmlspecialchars($message) ?></span>
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
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white shadow rounded-lg">
          <thead>
            <tr class="bg-gray-200">
              <th class="py-3 px-4 text-left">Food Item</th>
              <th class="py-3 px-4 text-left">Image</th>
              <th class="py-3 px-4 text-left">Room/Table</th>
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
                <td class="py-2 px-4">
                  <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['food_name']) ?>"
                    class="w-16 h-16 object-cover">
                </td>
                <td class="py-2 px-4"><?= htmlspecialchars($item['location_number']) ?></td>
                <td class="py-2 px-4">
                  <span id="quantity-<?= $item['id'] ?>"><?= $item['quantity'] ?></span>
                  <button class="bg-blue-500 rounded-md text-white px-2 py-1 ml-2 update-button"
                    data-id="<?= $item['id'] ?>">Update</button>
                </td>
                <td class="py-2 px-4 item-price">$<?= number_format($item['price'], 2) ?></td>
                <td class="py-2 px-4 item-total">$<?= number_format($item['total_price'], 2) ?></td>
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
          <h2 class="text-xl font-bold text-gray-800">Total Bill:</h2>
          <p id="totalBillAmount" class="text-2xl font-semibold text-green-600">$<?= number_format($totalBill, 2) ?></p>
        </div>
        <!-- Order Now Button -->
        <a href="order_now.php" class="mt-4 inline-block bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
          Order Now
        </a>
      </div>
    <?php endif; ?>
  </div>

  <!-- Update Quantity Modal -->
  <div id="updateModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden flex justify-center items-center">
    <div class="bg-white p-5 rounded shadow-lg w-1/3">
      <h2 class="text-xl mb-4">Update Quantity</h2>
      <form id="updateForm" method="POST" action="cart.php">
        <input type="hidden" name="id" id="updateItemId">
        <label for="quantity" class="block text-sm mb-1">New Quantity:</label>
        <input type="number" name="quantity" id="updateQuantity" min="1" class="border p-2 w-full mb-4">
        <div class="flex justify-end">
          <button type="submit" name="update" class="bg-blue-500 text-white px-4 py-2">Update</button>
          <button type="button" id="closeUpdateModal" class="ml-2 bg-gray-300 text-gray-800 px-4 py-2">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div id="deleteModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden flex justify-center items-center">
    <div class="bg-white p-5 rounded shadow-lg w-1/3">
      <h2 class="text-xl mb-4">Delete Item</h2>
      <p class="mb-4">Are you sure you want to remove this item from your cart?</p>
      <div class="flex justify-end">
        <a href="#" id="confirmDelete" class="bg-red-500 text-white px-4 py-2">Yes, Remove</a>
        <button id="closeDeleteModal" class="ml-2 bg-gray-300 text-gray-800 px-4 py-2">Cancel</button>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function () {
      function calculateTotalBill() {
        let totalBill = 0;
        $('.item-total').each(function () {
          totalBill += parseFloat($(this).text().replace('$', ''));
        });
        $('#totalBillAmount').text(`$${totalBill.toFixed(2)}`);
      }

      // Open Update Modal
      $('.update-button').click(function () {
        const itemId = $(this).data('id');
        const currentQuantity = $(`#quantity-${itemId}`).text(); // Get current quantity

        $('#updateItemId').val(itemId);
        $('#updateQuantity').val(currentQuantity); // Set current quantity in input
        $('#updateModal').removeClass('hidden');
      });

      // Close Update Modal
      $('#closeUpdateModal').click(function () {
        $('#updateModal').addClass('hidden');
      });

      // Update quantity via AJAX
      $('#updateForm').submit(function (e) {
        e.preventDefault();
        const id = $('#updateItemId').val();
        const quantity = $('#updateQuantity').val();
        $.ajax({
          url: 'cart.php',
          type: 'POST',
          data: { update: true, id, quantity },
          dataType: 'json',
          success: function (response) {
            if (response.status === 'success') {
              location.reload();
            } else {
              alert(response.message);
            }
          }
        });
      });

      // Open Delete Modal
      $('.delete-button').click(function () {
        const itemId = $(this).data('id');
        $('#confirmDelete').attr('href', `cart.php?delete=${itemId}`);
        $('#deleteModal').removeClass('hidden');
      });

      // Close Delete Modal
      $('#closeDeleteModal').click(function () {
        $('#deleteModal').addClass('hidden');
      });
    });
  </script>
</body>

</html>