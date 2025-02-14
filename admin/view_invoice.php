<?php
include('../db_connection.php');

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
  echo "Invalid user ID.";
  exit;
}

$user_query = "SELECT id, username, email FROM users WHERE id = $user_id AND role != 'admin'";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

if (!$user) {
  echo "User not found or is an admin.";
  exit;
}

$reservation_query = "SELECT r.room_type, r.price AS room_price, res.check_in_date, res.check_out_date, res.room_number 
                      FROM reservations res
                      JOIN rooms r ON res.room_id = r.id
                      WHERE res.user_id = $user_id AND res.status = 'Confirmed'";
$reservation_result = mysqli_query($conn, $reservation_query);

$reservations = [];
$room_charge = 0;
while ($reservation = mysqli_fetch_assoc($reservation_result)) {
  $check_in = new DateTime($reservation['check_in_date']);
  $check_out = new DateTime($reservation['check_out_date']);
  $days_stayed = $check_in->diff($check_out)->days;

  $reservation['days_stayed'] = $days_stayed;
  $reservation['total_price'] = $reservation['room_price'] * $days_stayed;

  $reservations[] = $reservation;
  $room_charge += $reservation['total_price'];
}

$food_order_query = "SELECT food_name, price, quantity, total_price 
                     FROM food_order
                     WHERE user_id = $user_id AND order_status = 'Confirmed'";
$food_order_result = mysqli_query($conn, $food_order_query);

$food_orders = [];
$total_food_charge = 0;
while ($food_order = mysqli_fetch_assoc($food_order_result)) {
  $food_orders[] = $food_order;
  $total_food_charge += $food_order['total_price'];
}

$total_bill = $room_charge + $total_food_charge;

if (isset($_POST['mark_paid'])) {
  $delete_reservations_query = "DELETE FROM reservations WHERE user_id = $user_id AND status = 'Confirmed'";
  $delete_food_orders_query = "DELETE FROM food_order WHERE user_id = $user_id AND order_status = 'Confirmed'";

  mysqli_query($conn, $delete_reservations_query);
  mysqli_query($conn, $delete_food_orders_query);

  header("Location: billing.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice Details</title>
  <link href="../public/style.css" rel="stylesheet">

</head>

<body class="bg-gray-100">
  <div class="container mx-auto p-6 bg-white shadow-md rounded-lg">
    <h2 class="text-2xl font-bold mb-4">Invoice for <?php echo htmlspecialchars($user['username']); ?></h2>

    <!-- User Details -->
    <div class="mb-6">
      <h3 class="text-xl font-semibold">User Details</h3>
      <p><strong>Name:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
      <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    </div>

    <div class="mb-6">
      <h3 class="text-xl font-semibold">Room Reservations</h3>
      <?php if (count($reservations) > 0): ?>
        <table class="min-w-full bg-gray-100 rounded-md">
          <thead>
            <tr class="bg-gray-300">
              <th class="px-4 py-2">Room Number</th>
              <th class="px-4 py-2">Room Type</th>
              <th class="px-4 py-2">Check-in Date</th>
              <th class="px-4 py-2">Check-out Date</th>
              <th class="px-4 py-2">Days Stayed</th>
              <th class="px-4 py-2">Total Price</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reservations as $reservation): ?>
              <tr>
                <td class="px-4 py-2"><?php echo htmlspecialchars($reservation['room_number']); ?></td>
                <td class="px-4 py-2"><?php echo htmlspecialchars($reservation['room_type']); ?></td>
                <td class="px-4 py-2"><?php echo htmlspecialchars($reservation['check_in_date']); ?></td>
                <td class="px-4 py-2"><?php echo htmlspecialchars($reservation['check_out_date']); ?></td>
                <td class="px-4 py-2"><?php echo $reservation['days_stayed']; ?></td>
                <td class="px-4 py-2">$<?php echo number_format($reservation['total_price'], 2); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No confirmed room reservations found.</p>
      <?php endif; ?>
    </div>

    <div class="mb-6">
      <h3 class="text-xl font-semibold">Food Orders</h3>
      <?php if (count($food_orders) > 0): ?>
        <table class="min-w-full bg-gray-100 rounded-md">
          <thead>
            <tr class="bg-gray-300">
              <th class="px-4 py-2">Food Name</th>
              <th class="px-4 py-2">Price</th>
              <th class="px-4 py-2">Quantity</th>
              <th class="px-4 py-2">Total</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($food_orders as $order): ?>
              <tr>
                <td class="px-4 py-2"><?php echo htmlspecialchars($order['food_name']); ?></td>
                <td class="px-4 py-2">$<?php echo number_format($order['price'], 2); ?></td>
                <td class="px-4 py-2"><?php echo $order['quantity']; ?></td>
                <td class="px-4 py-2">$<?php echo number_format($order['total_price'], 2); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No confirmed food orders found.</p>
      <?php endif; ?>
    </div>

    <!-- Total Bill -->
    <div class="mb-6">
      <h3 class="text-xl font-semibold">Total Bill</h3>
      <p><strong>Total Amount:</strong> $<?php echo number_format($total_bill, 2); ?></p>
    </div>

    <!-- Mark as Paid -->
    <?php if ($total_bill > 0): ?>
      <form method="POST">
        <button type="submit" name="mark_paid" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-700">
          Mark as Paid
        </button>
      </form>
      <div class="text-center">
        <button onclick="window.print()" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-700">
          Print Invoice
        </button>
      </div>
    <?php else: ?>
      <p>No outstanding balance.</p>
    <?php endif; ?>
    <div class="w-full text-center">
      <a href="billing.php">
        <button class="px-6 py-2 mt-4 text-white bg-blue-500 rounded-md">Go
          Back
        </button>
      </a>
    </div>
  </div>
</body>

</html>