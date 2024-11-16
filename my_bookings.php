<?php
session_start();
// Check if the user is logged in
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id']; // Get user_id from session

include('db_connection.php');

// Fetch user bookings
$sql = "SELECT reservations.*, rooms.room_type, rooms.room_number FROM reservations
JOIN rooms ON reservations.room_id = rooms.id
WHERE reservations.user_id = " . intval($user_id); // Use user_id for filtering

$result = mysqli_query($conn, $sql);

// Check for errors
if (!$result) {
  die('Error executing query: ' . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Bookings</title>
  <!-- <script src="https://cdn.tailwindcss.com"></script> -->
  <link href="public/style.css" rel="stylesheet">

  <style>
    .active {
      font-weight: bold;
      color: black;
    }
  </style>
</head>

<body class="bg-gray-100">
  <?php include 'navbar.php'; ?>
  <div class="container mx-auto p-8 text-center">
    <span class="border-b-4 text-3xl font-bold border-gray-300 pb-1 text-blue-600">Your Bookings</span>
    <?php if (mysqli_num_rows($result) > 0) { ?>
      <ul class="space-y-6 mt-10">
        <?php while ($booking = mysqli_fetch_assoc($result)) { ?>
          <li class="bg-white p-6 rounded-lg shadow-lg transition-transform hover:scale-105 duration-300">
            <div class="flex flex-col md:flex-row justify-between items-center">
              <!-- Room Information -->
              <div class="text-left md:w-2/3">
                <h2 class="text-2xl font-semibold text-gray-800 mb-2">
                  <?php echo htmlspecialchars(strtoupper($booking['room_type'])); ?>
                </h2>
                <p class="text-gray-600">
                  <strong>Room Number:</strong> <?php echo htmlspecialchars($booking['room_number']); ?>
                </p>
                <p class="text-gray-600">
                  <strong>Check-In Date:</strong> <?php echo htmlspecialchars($booking['check_in_date']); ?>
                </p>
                <p class="text-gray-600">
                  <strong>Check-Out Date:</strong> <?php echo htmlspecialchars($booking['check_out_date']); ?>
                </p>
              </div>

              <!-- Status Badge -->
              <div class="mt-4 md:mt-0 md:w-1/3 text-center flex items-center justify-center gap-4 ">
                <span
                  class="px-4 py-2 rounded-full text-sm font-medium <?php echo ($booking['status'] == 'Confirmed') ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>">
                  <?php echo htmlspecialchars($booking['status']); ?>
                </span>

                <!-- Cancel Booking Button -->
                <?php if ($booking['status'] == 'Pending') { ?>
                  <button type="button" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors "
                    onclick="confirmCancellation(<?php echo htmlspecialchars($booking['id']); ?>)">
                    Cancel Booking
                  </button>
                <?php } ?>
              </div>
            </div>
          </li>
        <?php } ?>
      </ul>
    <?php } else { ?>
      <p class="text-center text-xl mt-10 text-gray-500">You don't have any bookings yet.</p>
    <?php } ?>

    <!-- Modal for Cancellation Confirmation -->
    <div id="cancelModal" class="fixed z-50 inset-0 flex items-center justify-center hidden">
      <div class="bg-white p-8 rounded-lg shadow-lg text-center">
        <p class="text-xl font-semibold mb-6">Are you sure you want to cancel this booking?</p>
        <form method="POST" action="cancel_booking.php" id="cancelForm">
          <input type="hidden" name="booking_id" id="bookingId">
          <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg mr-4">Yes, Cancel</button>
          <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded-lg">No, Go Back</button>
        </form>
      </div>
    </div>

    <!-- Overlay for modal -->
    <div id="overlay" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden"></div>

  </div>

  <script>
    // Function to confirm cancellation and show modal
    function confirmCancellation(bookingId) {
      document.getElementById('bookingId').value = bookingId; // Set booking ID in form
      document.getElementById('cancelModal').classList.remove('hidden');
      document.getElementById('overlay').classList.remove('hidden');
    }

    // Function to close modal
    function closeModal() {
      document.getElementById('cancelModal').classList.add('hidden');
      document.getElementById('overlay').classList.add('hidden');
    }
  </script>
</body>


</html>