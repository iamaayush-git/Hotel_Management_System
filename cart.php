<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  echo "
    <div class='flex items-center justify-center min-h-screen bg-gray-100'>
        <div class='bg-white p-6 rounded-lg shadow-lg text-center'>
            <h2 class='text-2xl font-semibold text-red-500 mb-4'>Access Denied</h2>
            <p class='text-gray-700'>Please log in to view your cart.</p>
            <a href='login.php' class='mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600'>Log In</a>
        </div>
    </div>";
  exit;
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

  // Fetch the current status of the order
  $status_query = "SELECT order_status FROM cart WHERE id = $id AND user_id = $user_id";
  $status_result = mysqli_query($conn, $status_query);
  $status_row = mysqli_fetch_assoc($status_result);
  $order_status = $status_row['order_status'];

  if ($order_status !== 'Confirmed' && $order_status !== 'Cancelled') {
    if ($quantity > 0) {
      $query = "UPDATE cart SET quantity = $quantity, total_price = (price * $quantity) WHERE id = $id AND user_id = $user_id";
      mysqli_query($conn, $query);
      $message = "Quantity updated successfully!";
    } else {
      $message = "Quantity must be greater than zero!";
    }
  } else {
    $message = "Cannot update quantity for orders with status: $order_status";
  }
}

// Fetch cart items for logged-in user
$query = "SELECT * FROM cart WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);
$cartItems = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
              <th class="py-3 px-4 text-left">Status</th>
              <th class="py-3 px-4 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cartItems as $item):
              $total_price = $item['price'] * $item['quantity'];
              ?>

              <tr class="border-b hover:bg-gray-100">
                <td class="py-2 px-4"><?= htmlspecialchars($item['food_name']) ?></td>
                <td class="py-2 px-4">
                  <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['food_name']) ?>"
                    class="w-16 h-16 object-cover">
                </td>
                <td class="py-2 px-4"><?= htmlspecialchars($item['location_number']) ?></td>
                <td class="py-2 px-4">
                  <span id="quantity-<?= $item['id'] ?>"><?= $item['quantity'] ?></span>
                  <?php if ($item['order_status'] !== 'Confirmed' && $item['order_status'] !== 'Cancelled'): ?>
                    <button class="bg-blue-500 rounded-md text-white px-2 py-1 ml-2 update-button"
                      data-id="<?= $item['id'] ?>">Update</button>
                  <?php endif; ?>
                </td>
                <td class="py-2 px-4">$<?= number_format($item['price'], 2) ?></td>
                <td class="py-2 px-4">$<?= number_format($total_price, 2) ?></td>
                <td class="py-2 px-4">
                  <?php
                  if ($item['order_status'] == 'Confirmed') {
                    echo "<span class='text-green-500 font-semibold'>Confirmed</span>";
                  } else if ($item['order_status'] == 'Cancelled') {
                    echo "<span class='text-red-500 font-semibold'>Cancelled</span>"; // Change text based on your logic
                  } else if ($item['order_status'] == 'Pending') {
                    echo "<span class='text-yellow-500 font-semibold'>Pending</span>";
                  }

                  ?>
                </td>

                <td class="py-2 px-4 text-center">

                  <?php if ($item['order_status'] !== 'Confirmed' && $item['order_status'] !== 'Cancelled'): ?>
                    <button class="bg-red-500 rounded-md text-white px-2 py-1 ml-2 delete-button"
                      data-id="<?= $item['id'] ?>">Cancel Order</button>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
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
      <h2 class="text-xl mb-4">Confirm Cancel</h2>
      <p>Are you sure you want to cancel your order?</p>
      <div class="flex justify-end mt-4">
        <a id="confirmDeleteLink" class="bg-red-500 text-white px-4 py-2 rounded">Yes, Cancel</a>
        <button type="button" id="closeDeleteModal" class="ml-2 bg-gray-300 text-gray-800 px-4 py-2">No</button>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function () {
      // Show update modal
      $('.update-button').on('click', function () {
        const itemId = $(this).data('id');
        const currentQuantity = $('#quantity-' + itemId).text();
        $('#updateItemId').val(itemId);
        $('#updateQuantity').val(currentQuantity);
        $('#updateModal').removeClass('hidden');
      });

      // Close update modal
      $('#closeUpdateModal').on('click', function () {
        $('#updateModal').addClass('hidden');
      });

      // Show delete confirmation modal
      $('.delete-button').on('click', function () {
        const itemId = $(this).data('id');
        $('#confirmDeleteLink').attr('href', 'cart.php?delete=' + itemId);
        $('#deleteModal').removeClass('hidden');
      });

      // Close delete confirmation modal
      $('#closeDeleteModal').on('click', function () {
        $('#deleteModal').addClass('hidden');
      });

      // Hide message after 3 seconds
      setTimeout(function () {
        $('#message').fadeOut();
      }, 3000);
    });
  </script>

</body>

</html>

<?php
mysqli_close($conn);
?>