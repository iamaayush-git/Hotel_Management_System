<?php
session_start();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // Redirect to the admin dashboard or another page
    header("Location: admin/admin_dashboard.php");
    exit;
}

include 'db_connection.php'; // Include your database connection file

// Fetch only the top 3 available rooms
$sql = "SELECT * FROM rooms WHERE availability = 'available' LIMIT 3";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-home {
            background-image: url('path/to/your/background.jpg');
        }

        .active {
            font-weight: bold;
            color: blue;
        }
    </style>
</head>

<body class="bg-home bg-cover bg-center">
    <nav class="bg-white bg-opacity-70 shadow-md">
        <div class="container mx-auto p-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Hotel Management</h1>
            <ul class="flex space-x-6">
                <li><a href="index.php"
                        class="text-gray-700 hover:text-blue-500 <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">Home</a>
                </li>
                <li><a href="about.php"
                        class="text-gray-700 hover:text-blue-500 <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'active' : ''; ?>">About</a>
                </li>
                <li><a href="available_rooms.php"
                        class="text-gray-700 hover:text-blue-500 <?php echo (basename($_SERVER['PHP_SELF']) == 'available_rooms.php') ? 'active' : ''; ?>">Our
                        Rooms</a></li>
                <li><a href="contact.php"
                        class="text-gray-700 hover:text-blue-500 <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>">Contact
                        Us</a></li>
                <?php if (isset($_SESSION['username'])): ?>
                    <li class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</li>
                    <li><a href="logout.php" class="text-red-500 hover:text-red-700">Logout</a></li>
                <?php else: ?>
                    <li><a href="register.php" class="text-gray-700 hover:text-blue-500">Register</a></li>
                    <li><a href="login.php" class="text-gray-700 hover:text-blue-500">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container mx-auto p-8 bg-white bg-opacity-70 rounded-lg mt-4">
        <h1 class="text-4xl font-bold text-center mb-6">Welcome to Our Hotel Management System</h1>
        <p class="text-center mb-8">Your comfort is our priority. Manage your stay with ease.</p>

        <div class="flex justify-center space-x-4 mb-8">
            <a href="register.php" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600">Register</a>
            <!-- <a href="login.php" class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600">Admin Login</a> -->
            <a href="available_rooms.php" class="bg-yellow-500 text-white px-6 py-3 rounded-lg hover:bg-yellow-600">View
               All Rooms</a>
        </div>

        <div class="container mx-auto p-8 bg-white bg-opacity-70 rounded-lg mt-4">
            <h2 class="text-3xl font-bold text-center mb-6">Our Rooms</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($room = mysqli_fetch_assoc($result)): ?>
                        <a href="book_room.php?room_id=<?php echo $room['id']; ?>"
                            class="bg-white rounded-lg shadow-lg overflow-hidden">
                            <img src="<?php echo htmlspecialchars($room['image_url']); ?>"
                                alt="<?php echo htmlspecialchars($room['room_type']); ?>" class="w-full h-40 object-cover">
                            <div class="p-4">
                                <h3 class="font-bold text-lg"><?php echo htmlspecialchars($room['room_type']); ?></h3>
                                <p>Room Number: <?php echo htmlspecialchars($room['room_number']); ?></p>
                                <p class="font-bold">$<?php echo htmlspecialchars($room['price']); ?> per night</p>
                                <p class="text-green-600"><?php echo htmlspecialchars($room['availability']); ?></p>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center">No rooms available at the moment.</p>
                <?php endif; ?>
            </div>
        </div>

</body>

</html>