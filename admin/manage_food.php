<?php
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit;
}

include "../db_connection.php";

// Handle Add Food Item
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_food'])) {
  $food_name = $_POST['food_name'];
  $description = $_POST['description'];
  $price = $_POST['price'];
  $image_url = $_POST['image_url'];

  $sql = "INSERT INTO food_items (name, description, image_url, price) VALUES ('$food_name', '$description', '$image_url', '$price')";
  $is_successInsert = mysqli_query($conn, $sql);

  if ($is_successInsert) {
    $modalTitle = "Success";
    $modalMessage = "The food item was added successfully!";
  } else {
    $modalTitle = "Error";
    $modalMessage = "There was an issue adding the food item.";
  }
}

// Handle Update Food Item
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_food'])) {
  $food_id = $_POST['food_id'];
  $food_name = $_POST['food_name'];
  $description = $_POST['description'];
  $price = $_POST['price'];
  $image_url = $_POST['image_url'];

  $update_sql = "UPDATE food_items SET name='$food_name', description='$description', image_url='$image_url', price='$price' WHERE id='$food_id'";
  $is_successUpdate = mysqli_query($conn, $update_sql);

  if ($is_successUpdate) {
    $modalTitle = "Success";
    $modalMessage = "The food item was updated successfully!";
  } else {
    $modalTitle = "Error";
    $modalMessage = "There was an issue updating the food item.";
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_food'])) {
  $food_id = $_POST['food_id'];

  $delete_sql = "DELETE FROM food_items WHERE id='$food_id'";
  $is_successDelete = mysqli_query($conn, $delete_sql);

  if ($is_successDelete) {
    $modalTitle = "Success";
    $modalMessage = "The food item was deleted successfully!";
  } else {
    $modalTitle = "Error";
    $modalMessage = "There was an issue deleting the food item.";
  }
}

// Fetch food items from the database
$foodItemsQuery = "SELECT * FROM food_items";
$foodItemsResult = mysqli_query($conn, $foodItemsQuery);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Food Items</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex">
  <!-- Sidebar -->
  <?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <div class="w-3/4 p-8">
    <h2 class="text-3xl font-bold mb-4 w-full text-center">Manage Food Items</h2>
    <!-- Add Food Item Form -->
    <div class="mb-6">
      <h2 class="text-xl font-semibold mb-4">Add New Food</h2>
      <form method="POST" class="mb-6">
        <div class="mb-4">
          <label for="food_name" class="block">Food Name:</label>
          <input type="text" name="food_name" required class="w-full px-4 py-2 border rounded">
        </div>
        <div class="mb-4">
          <label for="description" class="block">Description</label>
          <input type="text" name="description" required class="w-full px-4 py-2 border rounded">
        </div>
        <div class="mb-4">
          <label for="price" class="block">Price:</label>
          <input type="text" name="price" required class="w-full px-4 py-2 border rounded">
        </div>
        <div class="mb-4">
          <label for="image_url" class="block">Image URL:</label>
          <input type="text" name="image_url" required class="w-full px-4 py-2 border rounded">
        </div>
        <button type="submit" name="add_food" class="bg-blue-500 text-white px-4 py-2 rounded">Add Food</button>
      </form>
    </div>

    <!-- Food Items Table -->
    <div class="bg-white p-6 rounded-lg shadow-md">
      <h3 class="text-2xl font-semibold mb-4">All Food Items</h3>
      <table class="w-full text-left">
        <thead>
          <tr>
            <th class="border-b-2 p-4">Image</th>
            <th class="border-b-2 p-4">Name</th>
            <th class="border-b-2 p-4">Description</th>
            <th class="border-b-2 p-4">Price</th>
            <th class="border-b-2 p-4">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($food = mysqli_fetch_assoc($foodItemsResult)) { ?>
            <tr>
              <td class="border-b p-4">
                <img src="<?php echo htmlspecialchars($food['image_url']); ?>" alt="Food Image" class="w-16 h-16 rounded">
              </td>
              <td class="border-b p-4"><?php echo htmlspecialchars($food['name']); ?></td>
              <td class="border-b p-4"><?php echo htmlspecialchars($food['description']); ?></td>
              <td class="border-b p-4">$<?php echo htmlspecialchars(number_format($food['price'], 2)); ?></td>
              <td class="border-b p-4">
                <button
                  onclick="showUpdateModal(<?php echo $food['id']; ?>, '<?php echo htmlspecialchars($food['name']); ?>', '<?php echo htmlspecialchars($food['description']); ?>', '<?php echo htmlspecialchars($food['price']); ?>', '<?php echo htmlspecialchars($food['image_url']); ?>')"
                  class="text-yellow-500 hover:text-yellow-700">Edit</button>
                <button onclick="showDeleteModal(<?php echo $food['id']; ?>)"
                  class="text-red-500 hover:text-red-700 ml-2">Delete</button>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Update Food Modal -->
  <div id="updateModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-lg w-full">
      <h2 class="text-xl font-bold mb-4">Update Food Item</h2>
      <form id="updateFoodForm" method="POST">
        <input type="hidden" name="food_id" id="updateFoodId">
        <div class="mb-4">
          <label for="food_name" class="block">Food Name:</label>
          <input type="text" name="food_name" id="updateFoodName" required class="w-full px-4 py-2 border rounded">
        </div>
        <div class="mb-4">
          <label for="description" class="block">Description:</label>
          <input type="text" name="description" id="updateDescription" required class="w-full px-4 py-2 border rounded">
        </div>
        <div class="mb-4">
          <label for="price" class="block">Price:</label>
          <input type="text" name="price" id="updatePrice" required class="w-full px-4 py-2 border rounded">
        </div>
        <div class="mb-4">
          <label for="image_url" class="block">Image URL:</label>
          <input type="text" name="image_url" id="updateImageUrl" required class="w-full px-4 py-2 border rounded">
        </div>
        <button type="submit" name="update_food" class="bg-green-500 text-white px-4 py-2 rounded">Update Food</button>
        <button type="button" onclick="closeModal('updateModal')"
          class="bg-gray-500 text-white px-4 py-2 rounded ml-2">Cancel</button>
      </form>
    </div>
  </div>

  <!-- Delete Food Modal -->
  <div id="deleteModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-lg w-full">
      <h2 class="text-xl font-bold mb-4">Confirm Delete</h2>
      <p>Are you sure you want to delete this food item?</p>
      <form id="deleteFoodForm" method="POST">
        <input type="hidden" name="food_id" id="deleteFoodId">
        <button type="submit" name="delete_food" class="bg-red-500 text-white px-4 py-2 rounded mt-4">Delete</button>
        <button type="button" onclick="closeModal('deleteModal')"
          class="bg-gray-500 text-white px-4 py-2 rounded ml-2 mt-4">Cancel</button>
      </form>
    </div>
  </div>

  <!-- Success Modal -->
  <div id="successModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-sm w-full text-center">
      <h2 class="text-xl font-bold text-green-600 mb-4">Success!</h2>
      <p id="successMessage" class="text-gray-700 mb-6"></p>
      <button onclick="closeModal('successModal')"
        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Close</button>
    </div>
  </div>


  <script>
    function showSuccessModal(message) {
      document.getElementById('successModal').classList.remove('hidden');
      document.getElementById('successMessage').textContent = message;
    }

    function closeModal(modalId) {
      document.getElementById(modalId).classList.add('hidden');
    }

    function showUpdateModal(id, name, description, price, imageUrl) {
      document.getElementById('updateModal').classList.remove('hidden');
      document.getElementById('updateFoodId').value = id;
      document.getElementById('updateFoodName').value = name;
      document.getElementById('updateDescription').value = description;
      document.getElementById('updatePrice').value = price;
      document.getElementById('updateImageUrl').value = imageUrl;
    }

    function showDeleteModal(id) {
      document.getElementById('deleteModal').classList.remove('hidden');
      document.getElementById('deleteFoodId').value = id;
    }

    // Check for PHP success condition and show modal if true
    <?php if (isset($modalMessage)) { ?>
      showSuccessModal('<?php echo $modalMessage; ?>');
    <?php } ?>
  </script>


</body>

</html>