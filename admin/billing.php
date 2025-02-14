<?php
include('../db_connection.php');

$users_query = "
    SELECT u.id, u.username, u.email, 
           IFNULL(SUM(r.price * DATEDIFF(res.check_out_date, res.check_in_date)), 0) AS total_room_charge,
           IFNULL(SUM(fo.total_price), 0) AS total_food_charge
    FROM users u
    LEFT JOIN reservations res ON u.id = res.user_id AND res.status = 'Confirmed'
    LEFT JOIN rooms r ON res.room_id = r.id
    LEFT JOIN food_order fo ON u.id = fo.user_id AND fo.order_status = 'Confirmed'
    WHERE u.role != 'admin'
    GROUP BY u.id
";
$users_result = mysqli_query($conn, $users_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Users and Invoices</title>

  <link href="../public/style.css" rel="stylesheet">

</head>

<body class="bg-gray-100 flex justify-center">
  <?php include "sidebar.php" ?>
  <div class="container mx-auto p-6 bg-white shadow-md rounded-lg">
    <h2 class="text-2xl font-bold mb-4">Users and Invoices</h2>

    <table class="min-w-full bg-gray-100 rounded-md">
      <thead>
        <tr class="bg-gray-300">
          <th class="px-4 py-2">Username</th>
          <th class="px-4 py-2">Email</th>
          <th class="px-4 py-2">Total Room Charge</th>
          <th class="px-4 py-2">Total Food Charge</th>
          <th class="px-4 py-2">Total Bill</th>
          <th class="px-4 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
          <?php
          $total_room_charge = $user['total_room_charge'];
          $total_food_charge = $user['total_food_charge'];
          $total_bill = $total_room_charge + $total_food_charge;
          ?>
          <tr class="text-center">
            <td class="px-4 py-2"><?php echo htmlspecialchars($user['username']); ?></td>
            <td class="px-4 py-2"><?php echo htmlspecialchars($user['email']); ?></td>
            <td class="px-4 py-2">Rs.<?php echo number_format($total_room_charge, 2); ?></td>
            <td class="px-4 py-2">Rs.<?php echo number_format($total_food_charge, 2); ?></td>
            <td class="px-4 py-2 font-bold">Rs.<?php echo number_format($total_bill, 2); ?></td>
            <td class="px-4 py-2">
              <a href="view_invoice.php?user_id=<?php echo $user['id']; ?>"
                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">
                View Invoice
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>

</html>