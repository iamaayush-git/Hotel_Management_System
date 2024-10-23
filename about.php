<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Hotel Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-about {
            background-image: url('path/to/your/about-background.jpg'); /* Update with the actual path */
        }
        .active {
            font-weight: bold;
            color: blue; /* Change the color for the active link */
        }
    </style>
</head>
<body class="bg-about bg-cover bg-center">
    <nav class="bg-white bg-opacity-70 shadow-md">
        <div class="container mx-auto p-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Hotel Management</h1>
            <ul class="flex space-x-6">
                <li><a href="index.php" class="text-gray-700 hover:text-blue-500">Home</a></li>
                <li><a href="about.php" class="text-gray-700 hover:text-blue-500 active">About</a></li>
                <li><a href="available_rooms.php" class="text-gray-700 hover:text-blue-500">Our Rooms</a></li>
                <li><a href="contact.php" class="text-gray-700 hover:text-blue-500">Contact Us</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mx-auto p-8 bg-white bg-opacity-70 rounded-lg mt-4">
        <h1 class="text-4xl font-bold text-center mb-6">About Us</h1>
        <p class="text-lg text-center mb-4">Welcome to our hotel! We are dedicated to providing our guests with a memorable stay and exceptional service.</p>
        
        <div class="mb-6">
            <img src="./assets/hotel.jpg" alt="Hotel Overview" class="w-full h-64 object-cover rounded-lg shadow-lg">
        </div>
        
        <p class="text-lg text-center mb-4">
            Our hotel offers a range of luxurious accommodations and excellent dining options to cater to every guest's needs. 
            We pride ourselves on our exceptional services, ensuring that your stay with us is nothing short of perfect.
        </p>
        
        <p class="text-lg text-center">
            Experience relaxation and comfort in our well-appointed rooms, enjoy delicious meals at our in-house restaurant, 
            and take advantage of our recreational amenities. Thank you for choosing our hotel, where your comfort is our priority!
        </p>
    </div>
</body>
</html>
