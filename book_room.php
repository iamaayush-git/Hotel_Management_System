<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
  // Redirect to the login page if not logged in
  header("Location: login.php"); 
  exit;
}
// Connect to the database
$conn = mysqli_connect('localhost', 'root', '', 'hotel_management');
if (!$conn) {
  die('Connection failed: ' . mysqli_connect_error());
}

// Handle room booking
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $guest_name = $_POST['guest_name'];
  $room_id = $_POST['room_id'];
  $check_in_date = $_POST['check_in_date'];
  $check_out_date = $_POST['check_out_date'];

  // Insert reservation into the database
  $sql = "INSERT INTO reservations (guest_name, room_id, check_in_date, check_out_date) VALUES ('$guest_name', '$room_id', '$check_in_date', '$check_out_date')";
  if (mysqli_query($conn, $sql)) {
    // Mark room as unavailable
    mysqli_query($conn, "UPDATE rooms SET availability = 0 WHERE id = '$room_id'");
    echo "Booking successful!";
  } else {
    echo "Error: " . mysqli_error($conn);
  }
}

// Fetch room details for the selected room
$room_id = $_GET['room_id'];
$sql = "SELECT * FROM rooms WHERE id = '$room_id'";
$result = mysqli_query($conn, $sql);
$room = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Room</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
  <div class="container mx-auto p-8">
    <h1 class="text-3xl font-bold text-center mb-8">Book Room</h1>
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg mx-auto">
      <h2 class="text-xl font-bold mb-2"><?php echo $room['room_type']; ?></h2>
      <p>Room Number: <?php echo $room['room_number']; ?></p>
      <p>Price: $<?php echo $room['price']; ?> per night</p>

      <form method="POST" class="space-y-4 mt-6">
        <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
        <div>
          <label for="guest_name" class="block text-gray-700">Guest Name:</label>
          <input type="text" name="guest_name"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
        </div>
        <div>
          <label for="check_in_date" class="block text-gray-700">Check-In Date:</label>
          <input type="date" name="check_in_date"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
        </div>
        <div>
          <label for="check_out_date" class="block text-gray-700">Check-Out Date:</label>
          <input type="date" name="check_out_date"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
        </div>
        <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">Confirm
          Booking</button>
      </form>
    </div>
  </div>
</body>

</html>