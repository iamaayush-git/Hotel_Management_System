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

$food_sql = "SELECT * FROM food_items LIMIT 3";
$food_result = mysqli_query($conn, $food_sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .active {
            font-weight: bold;
            color: blue;
        }
    </style>
</head>

<body class="bg-home bg-cover bg-center">
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto p-8 bg-white bg-opacity-70 rounded-lg mt-4">
        <h1 class="text-4xl font-bold text-center mb-6">Welcome to Our Hotel Management System</h1>
        <p class="text-center mb-8">Your comfort is our priority. Manage your stay with ease.</p>

        <div class="flex justify-center space-x-4 mb-8">
            <a href="register.php" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600">Register</a>
            <!-- <a href="login.php" class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600">Admin Login</a> -->
            <a href="all_rooms.php" class="bg-yellow-500 text-white px-6 py-3 rounded-lg hover:bg-yellow-600">View
                All Rooms</a>
        </div>
        <h2 class="text-3xl font-bold text-gray-800 text-center mt-10 mb-4">Our Services</h2>
        <div class="container mx-auto p-8 bg-white bg-opacity-70 rounded-lg mt-4">
            <h2 class="text-3xl font-bold text-center mb-6">Rooms</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($room = mysqli_fetch_assoc($result)): ?>
                        <a href="all_rooms.php"
                            class="bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-300 hover:scale-105">
                            <img src="<?php echo htmlspecialchars($room['image_url']); ?>"
                                alt="<?php echo htmlspecialchars($room['room_type']); ?>" class="w-full h-40 object-cover">
                            <div class="p-4">
                                <h3 class="text-xl font-bold mb-2">
                                    <?php echo htmlspecialchars(strtoupper($room['room_type'])); ?>
                                </h3>
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
        <div class="container mx-auto p-8 bg-white bg-opacity-70 rounded-lg mt-4">
            <h2 class="text-3xl font-bold text-center mb-6">Foods</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (mysqli_num_rows($food_result) > 0): ?>
                    <?php while ($food = mysqli_fetch_assoc($food_result)): ?>
                        <a href="food.php"
                            class="relative bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-300 hover:scale-105">
                            <img src="<?php echo htmlspecialchars($food['image_url']); ?>" alt="imgnotfound"
                                class="w-full h-40 object-cover">
                            <div class="p-4 my-8">
                                <h3 class="text-xl font-bold mb-2">
                                    <?php echo htmlspecialchars(strtoupper($food['name'])); ?>
                                </h3>
                                <p><?php echo htmlspecialchars($food['description']); ?></p>
                                <p class="font-bold absolute bottom-2">$<?php echo htmlspecialchars($food['price']); ?> per
                                    night</p>
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