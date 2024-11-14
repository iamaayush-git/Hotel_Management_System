<?php
session_start();

// Check if the user is logged in as an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit;
}

include "../db_connection.php";

// Fetch all users from the database
$sql = "SELECT * FROM users";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Users</title>
  <!-- <script src="https://cdn.tailwindcss.com"></script> -->
  <link href="../public/style.css" rel="stylesheet">

</head>

<body class="bg-gray-100">
  <div class="flex justify-center">
    <?php include "sidebar.php" ?>
    <div class="container mx-auto p-8">
      <h1 class="text-3xl font-bold text-center mb-8 text-blue-600">Manage Users</h1>

      <div class="bg-white p-6 rounded-lg shadow-lg">
        <table class="w-full table-auto border-collapse">
          <thead>
            <tr>
              <th class="border p-4">Username</th>
              <th class="border p-4">Email</th>
              <th class="border p-4">Role</th>
              <th class="border p-4">Status</th>
              <th class="border p-4">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($user = mysqli_fetch_assoc($result)) { ?>
              <tr class="text-center">
                <td class="border p-4"><?php echo htmlspecialchars($user['username']); ?></td>
                <td class="border p-4"><?php echo htmlspecialchars($user['email']); ?></td>
                <td class="border p-4"><?php echo htmlspecialchars($user['role']); ?></td>
                <td class="border p-4" id="status-<?php echo $user['id']; ?>">
                  <?php echo ($user['is_banned'] ?? 0) ? 'Banned' : 'Active'; ?>
                </td>
                <td class="border p-4 space-x-2">
                  <button onclick="openBanModal(<?php echo $user['id']; ?>)"
                    class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                    <?php echo $user['is_banned'] ? 'Unban' : 'Ban'; ?>
                  </button>
                  <button onclick="openDeleteModal(<?php echo $user['id']; ?>)"
                    class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">Delete</button>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
    <!-- Ban Confirmation Modal -->
    <div id="banModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
      <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm text-center">
        <p class="mb-4">Are you sure you want to <span id="banAction"></span> this user?</p>
        <button id="banConfirmBtn" class="bg-blue-500 text-white px-4 py-2 rounded-lg mr-2">Yes</button>
        <button onclick="closeModal('banModal')" class="bg-gray-300 text-black px-4 py-2 rounded-lg">Cancel</button>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
      <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm text-center">
        <p class="mb-4">Are you sure you want to delete this user?</p>
        <button id="deleteConfirmBtn" class="bg-red-500 text-white px-4 py-2 rounded-lg mr-2">Delete</button>
        <button onclick="closeModal('deleteModal')" class="bg-gray-300 text-black px-4 py-2 rounded-lg">Cancel</button>
      </div>
    </div>

    <script>
      let selectedUserId;

      function openBanModal(userId) {
        selectedUserId = userId;
        const isBanned = document.getElementById(`status-${userId}`).innerText === 'Banned';
        document.getElementById('banAction').innerText = isBanned ? 'unban' : 'ban';
        document.getElementById('banModal').classList.remove('hidden');
        document.getElementById('banConfirmBtn').onclick = function () { banUser(userId, isBanned) };
      }

      function openDeleteModal(userId) {
        selectedUserId = userId;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteConfirmBtn').onclick = function () { deleteUser(userId) };
      }

      function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
      }

      function banUser(userId, isBanned) {
        const action = isBanned ? 'unban' : 'ban';
        fetch('manage_users_action.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `action=${action}&user_id=${userId}`
        }).then(response => response.json())
          .then(data => {
            if (data.success) {
              // Update the status text
              const statusCell = document.getElementById(`status-${userId}`);
              statusCell.innerText = isBanned ? 'Active' : 'Banned';

              // Update the button text
              const banButton = statusCell.parentElement.querySelector('button');
              banButton.innerText = isBanned ? 'Ban' : 'Unban';

              closeModal('banModal');
            }
          });
      }

      function deleteUser(userId) {
        fetch('manage_users_action.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `action=delete&user_id=${userId}`
        }).then(response => response.json())
          .then(data => {
            if (data.success) {
              location.reload();
            }
          });
      }
    </script>
  </div>
</body>

</html>