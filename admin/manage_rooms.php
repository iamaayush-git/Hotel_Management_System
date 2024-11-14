<?php
// Include database connection
include '../db_connection.php';

session_start(); // Start a session
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php"); // Redirect to login if not admin
  exit();
}

// Variables to control modal display
$modalMessage = '';
$modalType = ''; // success or error

// Handle adding a new room
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_room'])) {
  $room_number = $_POST['room_number'];
  $room_type = $_POST['room_type'];
  $price = $_POST['price'];
  $availability = $_POST['availability'];
  $image_url = $_POST['image_url'];

  // Prepare and execute the insert statement
  $sql = "INSERT INTO rooms (room_number, room_type, price, availability, image_url) VALUES ('$room_number', '$room_type', '$price', '$availability', '$image_url')";

  if (mysqli_query($conn, $sql)) {
    $modalMessage = 'Room added successfully!';
    $modalType = 'success';
  } else {
    $modalMessage = 'Error: ' . mysqli_error($conn);
    $modalType = 'error';
  }
}

// Handle deleting a room
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $sql = "DELETE FROM rooms WHERE id = '$id'";

  if (mysqli_query($conn, $sql)) {
    $modalMessage = 'Room deleted successfully!';
    $modalType = 'success';
    header("Location: manage_rooms.php");
    exit();
  } else {
    $modalMessage = 'Error: ' . mysqli_error($conn);
    $modalType = 'error';
  }
}

// Handle updating a room
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_room'])) {
  $id = $_POST['id'];
  $room_number = $_POST['room_number'];
  $room_type = $_POST['room_type'];
  $price = $_POST['price'];
  $availability = $_POST['availability'];
  $image_url = $_POST['image_url'];

  $sql = "UPDATE rooms SET room_number='$room_number', room_type='$room_type', price='$price', availability='$availability', image_url='$image_url' WHERE id='$id'";

  if (mysqli_query($conn, $sql)) {
    $modalMessage = 'Room updated successfully!';
    $modalType = 'success';
  } else {
    $modalMessage = 'Error: ' . mysqli_error($conn);
    $modalType = 'error';
  }
}

// Fetch existing rooms
$sql = "SELECT * FROM rooms";
$result = mysqli_query($conn, $sql);

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Rooms</title>
  <!-- <script src="https://cdn.tailwindcss.com"></script> -->
  <link href="../public/style.css" rel="stylesheet">
   
</head>

<body class="bg-gray-100">
  <div class="flex">
    <?php include 'sidebar.php'; ?>
    <div class="flex-1 p-10">
      <header class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-center w-full font-bold">Manage Rooms</h1>
      </header>

      <!-- Add Room Form -->
      <div class="mb-6">
        <h2 class="text-xl font-semibold mb-4">Add New Room</h2>
        <form method="POST" class="mb-6">
          <div class="mb-4">
            <label for="room_number" class="block">Room Number:</label>
            <input type="text" name="room_number" required class="w-full px-4 py-2 border rounded">
          </div>
          <div class="mb-4">
            <label for="room_type" class="block">Room Type:</label>
            <select name="room_type" required class="w-full px-4 py-2 border rounded">
              <option value="single">Single</option>
              <option value="double">Double</option>
              <option value="suite">Suite</option>
              <option value="deluxe">Deluxe</option>
            </select>
          </div>
          <div class="mb-4">
            <label for="price" class="block">Price:</label>
            <input type="text" name="price" required class="w-full px-4 py-2 border rounded">
          </div>
          <div class="mb-4">
            <label for="availability" class="block">Availability:</label>
            <select name="availability" class="w-full px-4 py-2 border rounded">
              <option value="available">Available</option>
              <option value="not available">Not Available</option>
            </select>
          </div>
          <div class="mb-4">
            <label for="image_url" class="block">Image URL:</label>
            <input type="text" name="image_url" required class="w-full px-4 py-2 border rounded">
          </div>
          <button type="submit" name="add_room" class="bg-blue-500 text-white px-4 py-2 rounded">Add Room</button>
        </form>
      </div>

      <!-- Existing Rooms Table -->
      <h2 class="text-lg font-semibold mb-4">Existing Rooms</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg shadow-md">
          <thead>
            <tr class="w-full bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
              <th class="py-3 px-6 text-left">Room Number</th>
              <th class="py-3 px-6 text-left">Room Type</th>
              <th class="py-3 px-6 text-left">Price</th>
              <th class="py-3 px-6 text-left">Availability</th>
              <th class="py-3 px-6 text-left">Actions</th>
            </tr>
          </thead>
          <tbody class="text-gray-600 text-sm font-light">
            <?php while ($room = mysqli_fetch_assoc($result)): ?>
              <tr class="border-b border-gray-200 hover:bg-gray-100 font-semibold">
                <td class="py-3 px-6 text-left"><?php echo $room['room_number']; ?></td>
                <td class="py-3 px-6 text-left"><?php echo $room['room_type']; ?></td>
                <td class="py-3 px-6 text-left">$<?php echo $room['price']; ?></td>
                <td class="py-3 px-6 text-left"><?php echo ucfirst($room['availability']); ?></td>
                <td class="py-3 px-6 text-left">
                  <button type="button" onclick="openUpdateModal(<?php echo htmlspecialchars(json_encode($room)); ?>)"
                    class="text-blue-500 hover:text-blue-700 font-semibold">Update</button>
                  <a href="#" onclick="openDeleteModal(<?php echo $room['id']; ?>)"
                    class="text-red-500 hover:text-red-700 font-semibold">Delete</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Success/Error Message Modal -->
  <?php if ($modalMessage): ?>
    <div id="messageModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white p-6 rounded-lg shadow-lg">
        <p class="text-<?php echo $modalType === 'success' ? 'green' : 'red'; ?>-500 font-semibold">
          <?php echo $modalMessage; ?>
        </p>
        <button onclick="closeMessageModal()" class="bg-blue-500 text-white px-4 py-2 rounded mt-4">Close</button>
      </div>
    </div>
  <?php endif; ?>

  <!-- Update Room Modal -->
  <div id="updateModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg">
      <form method="POST">
        <input type="hidden" id="update_id" name="id">
        <div class="mb-4">
          <label for="update_room_number" class="block">Room Number:</label>
          <input type="text" id="update_room_number" name="room_number" required
            class="w-full px-4 py-2 border rounded">
        </div>
        <div class="mb-4">
          <label for="update_room_type" class="block">Room Type:</label>
          <select id="update_room_type" name="room_type" required class="w-full px-4 py-2 border rounded">
            <option value="single">Single</option>
            <option value="double">Double</option>
            <option value="suite">Suite</option>
            <option value="deluxe">Deluxe</option>
          </select>
        </div>
        <div class="mb-4">
          <label for="update_price" class="block">Price:</label>
          <input type="text" id="update_price" name="price" required class="w-full px-4 py-2 border rounded">
        </div>
        <div class="mb-4">
          <label for="update_availability" class="block">Availability:</label>
          <select id="update_availability" name="availability" class="w-full px-4 py-2 border rounded">
            <option value="available">Available</option>
            <option value="not available">Not Available</option>
          </select>
        </div>
        <div class="mb-4">
          <label for="update_image_url" class="block">Image URL:</label>
          <input type="text" id="update_image_url" name="image_url" required class="w-full px-4 py-2 border rounded">
        </div>
        <button type="submit" name="update_room" class="bg-blue-500 text-white px-4 py-2 rounded">Update Room</button>
        <button type="button" onclick="closeUpdateModal()"
          class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div id="deleteModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg">
      <p class="font-semibold">Are you sure you want to delete this room?</p>
      <form method="GET">
        <input type="hidden" id="delete_room_id" name="delete">
        <div class="flex justify-between mt-4">
          <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Delete</button>
          <button type="button" onclick="closeDeleteModal()"
            class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function closeMessageModal() {
      document.getElementById('messageModal').style.display = 'none';
    }

    function openUpdateModal(room) {
      document.getElementById('update_id').value = room.id;
      document.getElementById('update_room_number').value = room.room_number;
      document.getElementById('update_room_type').value = room.room_type;
      document.getElementById('update_price').value = room.price;
      document.getElementById('update_availability').value = room.availability;
      document.getElementById('update_image_url').value = room.image_url;
      document.getElementById('updateModal').classList.remove('hidden');
    }

    function closeUpdateModal() {
      document.getElementById('updateModal').classList.add('hidden');
    }

    function openDeleteModal(roomId) {
      document.getElementById('delete_room_id').value = roomId;
      document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
      document.getElementById('deleteModal').classList.add('hidden');
    }
  </script>
</body>

</html>