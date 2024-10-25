<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include "../db_connection.php";

// Fetch all bookings
$sql = "SELECT * FROM reservations";
$result = mysqli_query($conn, $sql);

// Handle booking confirmation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_booking_id'])) {
    $booking_id = $_POST['confirm_booking_id'];

    // Update booking status to 'Confirmed'
    $update_booking_sql = "UPDATE reservations SET status = 'Confirmed' WHERE id = '$booking_id'";
    mysqli_query($conn, $update_booking_sql);

    // Respond to AJAX request
    echo json_encode(['status' => 'success']);
    exit;
}

// Handle booking deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_booking_id'])) {
    $delete_booking_id = $_POST['delete_booking_id'];

    // Delete the reservation
    $delete_booking_sql = "DELETE FROM reservations WHERE id = '$delete_booking_id'";
    mysqli_query($conn, $delete_booking_sql);

    // Respond to AJAX request
    echo json_encode(['status' => 'deleted']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Send AJAX request to confirm booking
        function confirmBooking(bookingId, roomId) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "<?php echo $_SERVER['PHP_SELF']; ?>", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        document.getElementById(`status-${bookingId}`).innerText = 'Confirmed';
                        document.getElementById(`status-${bookingId}`).classList.add('text-green-500');
                        document.getElementById(`confirm-btn-${bookingId}`).disabled = true;
                    }
                }
            };
            xhr.send(`confirm_booking_id=${bookingId}&room_id=${roomId}`);
        }

        // Send AJAX request to delete booking
        function deleteBooking(bookingId) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "<?php echo $_SERVER['PHP_SELF']; ?>", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.status === 'deleted') {
                        document.getElementById(`booking-${bookingId}`).remove();
                    }
                }
            };
            xhr.send(`delete_booking_id=${bookingId}`);
        }

        // Function to handle modal display and actions
        function openConfirmModal(bookingId, roomId) {
            confirmBooking(bookingId, roomId);  // Directly call confirmBooking
        }

        function openDeleteModal(bookingId) {
            const modal = document.getElementById('deleteModal');
            modal.classList.remove('hidden');
            const confirmButton = document.getElementById('confirmDeleteButton');
            confirmButton.onclick = function () {
                deleteBooking(bookingId);
                closeModal('deleteModal');
            };
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
    </script>
</head>

<body class="bg-gray-100 flex justify-center">
    <?php include "./sidebar.php" ?>
    <div class="container mx-auto p-8">
        <h1 class="text-3xl font-bold text-center mb-8 text-blue-600">Booking Management</h1>

        <!-- Booking List -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr>
                        <th class="border p-4">Guest Name</th>
                        <th class="border p-4">Room Number</th>
                        <th class="border p-4">Check-In</th>
                        <th class="border p-4">Check-Out</th>
                        <th class="border p-4">Status</th>
                        <th class="border p-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($booking = mysqli_fetch_assoc($result)) { ?>
                        <tr class="text-center" id="booking-<?php echo $booking['id']; ?>">
                            <td class="border p-4"><?php echo htmlspecialchars($booking['guest_name']); ?></td>
                            <td class="border p-4"><?php echo htmlspecialchars($booking['room_number']); ?></td>
                            <td class="border p-4"><?php echo htmlspecialchars($booking['check_in_date']); ?></td>
                            <td class="border p-4"><?php echo htmlspecialchars($booking['check_out_date']); ?></td>
                            <td class="border p-4 font-semibold" id="status-<?php echo $booking['id']; ?>">
                                <?php echo htmlspecialchars($booking['status']); ?>
                            </td>
                            <td class="border p-4 space-x-2">
                                <button
                                    onclick="openConfirmModal(<?php echo $booking['id']; ?>, <?php echo $booking['room_id']; ?>)"
                                    id="confirm-btn-<?php echo $booking['id']; ?>"
                                    class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-300"
                                    <?php echo $booking['status'] !== 'Pending' ? 'disabled' : ''; ?>>
                                    Confirm
                                </button>
                                <button onclick="openDeleteModal(<?php echo $booking['id']; ?>)"
                                    class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-300">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Delete Modal -->
        <div id="deleteModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-lg p-8 max-w-sm w-full">
                <h2 class="text-2xl font-bold mb-4">Delete Booking</h2>
                <p>Are you sure you want to delete this booking?</p>
                <div class="flex justify-end space-x-4 mt-4">
                    <button type="button" onclick="closeModal('deleteModal')"
                        class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg">Cancel</button>
                    <button id="confirmDeleteButton"
                        class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">Delete</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>