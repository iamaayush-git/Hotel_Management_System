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
    <link href="public/style.css" rel="stylesheet">

    <style>
        #carousel {
            will-change: transform;
            transform: translateZ(0);
            /* Triggers GPU acceleration */
        }

        .active {
            font-weight: bold;
            color: black;
        }
    </style>
</head>

<body class="bg-gray-100">
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto p-8 bg-white bg-opacity-70 rounded-lg">
        <h1 class="text-4xl font-bold text-center mb-6">Welcome to Our Hotel Management System</h1>
        <p class="text-center mb-8">Your comfort is our priority. Manage your stay with ease.</p>

        <div class="flex justify-center space-x-4 mb-8">
            <a href="register.php" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600">Register</a>
            <a href="all_rooms.php" class="bg-yellow-500 text-white px-6 py-3 rounded-lg hover:bg-yellow-600">View All
                Rooms</a>
        </div>

        <!-- carousel -->
        <div class="relative overflow-hidden">
            <div id="carousel" class="flex transition-transform duration-300 ease-in-out">
                <div class="w-full flex-shrink-0">
                    <img src="assets/carousel/carousel-1.jpg" alt="Carousel Image 1" class="w-full h-96 object-cover"
                        loading="lazy">
                </div>
                <div class="w-full flex-shrink-0">
                    <img src="assets/carousel/carousel-2.jpg" alt="Carousel Image 1" class="w-full h-96 object-cover"
                        loading="lazy">

                </div>
                <div class="w-full flex-shrink-0">
                    <img src="assets/carousel/carousel-3.jpg" alt="Carousel Image 3" class="w-full h-96 object-cover"
                        loading="lazy">
                </div>
                <div class="w-full flex-shrink-0">
                    <img src="assets/carousel/carousel-4.jpg" alt="Carousel Image 4" class="w-full h-96 object-cover"
                        loading="lazy">
                </div>
                <div class="w-full flex-shrink-0">
                    <img src="assets/carousel/carousel-5.jpg" alt="Carousel Image 5" class="w-full h-96 object-cover"
                        loading="lazy">
                </div>
            </div>

            <!-- Demo Rooms Section -->
            <h2 class="text-3xl font-bold text-gray-800 text-center mt-10 mb-4">Rooms</h2>
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

            <!-- Demo Foods Section -->
            <h2 class="text-3xl font-bold text-gray-800 text-center mt-10 mb-4">Foods</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (mysqli_num_rows($food_result) > 0): ?>
                    <?php while ($food = mysqli_fetch_assoc($food_result)): ?>
                        <a href="food.php"
                            class="bg-white rounded-lg shadow-lg overflow-hidden transform transition duration-300 hover:scale-105">
                            <img src="<?php echo htmlspecialchars($food['image_url']); ?>" alt="Food Image"
                                class="w-full h-40 object-cover">
                            <div class="p-4">
                                <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars(strtoupper($food['name'])); ?>
                                </h3>
                                <p><?php echo htmlspecialchars($food['description']); ?></p>
                                <p class="font-bold">$<?php echo htmlspecialchars($food['price']); ?></p>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center">No foods available at the moment.</p>
                <?php endif; ?>
            </div>
        </div>

        <script>
            const carousel = document.getElementById('carousel');
            const slides = carousel.children;
            const totalSlides = slides.length;
            let currentIndex = 0;
            let isAnimating = false;

            // Automatic slide transition every 3 seconds
            function autoSlide() {
                if (!isAnimating) {
                    isAnimating = true;
                    currentIndex = (currentIndex + 1) % totalSlides;
                    updateCarousel();
                    setTimeout(() => {
                        isAnimating = false;
                    }, 300); // Delay to match transition duration
                }
            }

            setInterval(autoSlide, 3000); // 3 seconds

            // Function to update carousel position
            function updateCarousel() {
                const offset = -currentIndex * 100;
                carousel.style.transform = `translateX(${offset}%)`;
            }
        </script>



</body>

</html>