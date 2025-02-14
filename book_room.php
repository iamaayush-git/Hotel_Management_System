<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
  header("Location: login.php");
  exit;
}

include "db_connection.php";


$user_id = $_SESSION['user_id'];
$banned_check_sql = "SELECT is_banned FROM users WHERE id = '$user_id'";
$banned_check_result = mysqli_query($conn, $banned_check_sql);
$user_data = mysqli_fetch_assoc($banned_check_result);


$room_id = $_GET['room_id'];
$sql = "SELECT * FROM rooms WHERE id = '$room_id'";
$result = mysqli_query($conn, $sql);
$room = mysqli_fetch_assoc($result);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $guest_name = $_POST['guest_name'];
  $check_in_date = $_POST['check_in_date'];
  $check_out_date = $_POST['check_out_date'];


  $room_query = "SELECT room_number FROM rooms WHERE id = '$room_id'";
  $room_result = mysqli_query($conn, $room_query);
  $room = mysqli_fetch_assoc($room_result);
  $room_number = $room['room_number'];


  $sql = "INSERT INTO reservations (guest_name, room_id, room_number, check_in_date, check_out_date, user_id, status) 
            VALUES ('$guest_name', '$room_id', '$room_number', '$check_in_date', '$check_out_date', '$user_id', 'Pending')";

  if (mysqli_query($conn, $sql)) {

    $_SESSION['booking_success'] = true;
    header("Location: book_room.php?room_id=$room_id");
    exit;
  } else {
    echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
  }
}


$booking_success = isset($_SESSION['booking_success']) ? $_SESSION['booking_success'] : false;


unset($_SESSION['booking_success']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Room</title>
  <link href="public/style.css" rel="stylesheet">

</head>

<body class="bg-gray-100">
  <div class="container mx-auto p-8">
    <h1 class="text-3xl font-bold text-center mb-8">Book Room</h1>
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg mx-auto">
      <h2 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($room['room_type']); ?></h2>
      <p>Room Number: <?php echo htmlspecialchars($room['room_number']); ?></p>
      <p>Price: Rs.<?php echo htmlspecialchars($room['price']); ?> per night</p>

      <form method="POST" class="space-y-4 mt-6">
        <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room['id']); ?>">
        <div>
          <label for="guest_name" class="block text-gray-700">Guest Name:</label>
          <input type="text" name="guest_name"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
        </div>
        <div>
          <label for="check_in_date" class="block text-gray-700">Check-In Date:</label>
          <input type="date" name="check_in_date" id="check_in_date"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
        </div>
        <div>
          <label for="check_out_date" class="block text-gray-700">Check-Out Date:</label>
          <input type="date" name="check_out_date" id="check_out_date"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
        </div>
        <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
          Confirm Booking
        </button>
      </form>
    </div>
  </div>


  <div id="bannedUserModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
    <div class="bg-white p-8 rounded-lg shadow-2xl max-w-sm text-center">
      <h2 class="text-2xl font-semibold text-red-600 mb-4">Access Denied</h2>
      <p class="mb-6 text-gray-700">You are temporarily banned from making bookings.</p>
      <button onclick="closeBanModal()"
        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-full shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1">OK</button>
    </div>
  </div>


  <div id="successModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-sm w-full text-center">
      <h2 class="text-xl font-bold text-green-600 mb-4">Booking Successful!</h2>
      <p class="text-gray-700 mb-6">Your reservation is pending confirmation.</p>
      <button onclick="closeModal('successModal')"
        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Close</button>
    </div>
  </div>

  <script>
    document.getElementById("check_in_date").addEventListener("change", function () {
      let checkIn = document.getElementById("check_in_date").value;
      document.getElementById("check_out_date").setAttribute("min", checkIn);
    });

    document.querySelector("form").addEventListener("submit", function (event) {
      let checkIn = document.getElementById("check_in_date").value;
      let checkOut = document.getElementById("check_out_date").value;

      if (checkOut < checkIn) {
        alert("Check-Out Date cannot be earlier than Check-In Date!");
        event.preventDefault();
      }
    });
    function showBannedUserModal() {
      document.getElementById('bannedUserModal').classList.remove('hidden');
    }

    function closeBanModal() {
      window.location.href = 'all_rooms.php';
    }

    function closeModal(modalId) {
      document.getElementById(modalId).classList.add('hidden');
      window.location.href = 'my_bookings.php';
    }


    <?php if ($user_data['is_banned']): ?>
      showBannedUserModal();
    <?php endif; ?>


    <?php if ($booking_success): ?>
      document.getElementById('successModal').classList.remove('hidden');

    <?php endif; ?>

    <?php if ($user_data['is_banned']): ?>
      showBannedUserModal();
    <?php endif; ?>
  </script>

</body>

</html>