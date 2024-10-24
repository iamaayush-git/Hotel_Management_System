<?php
session_start();
// Connect to the database
include 'db_connection.php';

// Fetch all rooms
$sql = "SELECT * FROM rooms";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Rooms</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <?php include 'navbar.php'; ?>
    <div class="container mx-auto p-8">
        <h1 class="text-3xl font-bold text-center mb-8">All Rooms</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php while ($room = mysqli_fetch_assoc($result)) { ?>
                <div
                    class="bg-white p-6 rounded-lg shadow-lg <?php echo ($room['availability'] == 'available') ? 'transform transition duration-300 hover:scale-105' : ''; ?>">
                    <img src="<?php echo htmlspecialchars($room['image_url']); ?>"
                        alt="<?php echo htmlspecialchars($room['room_type']); ?>" class="w-full h-40 object-cover">
                    <h2 class="text-xl font-bold mb-2"><?php echo htmlspecialchars(strtoupper($room['room_type'])); ?></h2>

                    <p>Room Number: <?php echo htmlspecialchars($room['room_number']); ?></p>
                    <p>Price: $<?php echo htmlspecialchars($room['price']); ?> per night</p>

                    <?php if ($room['availability'] == 'available') { ?>
                        <a href="book_room.php?room_id=<?php echo $room['id']; ?>"
                            class="bg-blue-500 text-white px-4 py-2 rounded mt-4 block text-center">Book Now</a>
                    <?php } else { ?>
                        <p class="text-red-500 font-bold mt-4">This room is currently unavailable.</p>
                    <?php } ?>
                </div>

            <?php } ?>
        </div>
    </div>
</body>

</html>