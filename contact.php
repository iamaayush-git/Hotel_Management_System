<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Hotel Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-contact {
            background-image: url('path/to/your/contact-background.jpg');
            /* Update with the actual path */
        }

        .active {
            font-weight: bold;
            color: black;
        }
    </style>
</head>

<body class="bg-contact bg-cover bg-center">
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto p-8 bg-white bg-opacity-70 rounded-lg mt-4">
        <h1 class="text-4xl font-bold text-center mb-6">Contact Us</h1>
        <p class="text-lg text-center mb-4">We would love to hear from you! Please fill out the form below.</p>

        <form action="submit_contact.php" method="POST" class="space-y-4">
            <div>
                <label for="name" class="block text-lg font-semibold">Name</label>
                <input type="text" id="name" name="name" required class="w-full p-2 border border-gray-300 rounded">
            </div>
            <div>
                <label for="email" class="block text-lg font-semibold">Email</label>
                <input type="email" id="email" name="email" required class="w-full p-2 border border-gray-300 rounded">
            </div>
            <div>
                <label for="message" class="block text-lg font-semibold">Message</label>
                <textarea id="message" name="message" rows="4" required
                    class="w-full p-2 border border-gray-300 rounded"></textarea>
            </div>
            <div>
                <button type="submit" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600">Send
                    Message</button>
            </div>
        </form>
    </div>
</body>

</html>