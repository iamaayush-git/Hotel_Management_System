<?php
// Include database connection
include 'db_connection.php';

session_start(); // Start a session

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to check if the user exists
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify the password directly (no hashing)
        if ($password === $user['password']) {
            // Password is correct, set session variables
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['login_success'] = true;
            // Redirect based on user role
            if ($user['role'] === 'admin') {
                $_SESSION['admin_logged_in'] = true;
                echo "<script>window.location.href='./admin/admin_dashboard.php';</script>"; // Redirect to admin dashboard
            } else {
                $_SESSION['user_logged_in'] = true;
                echo "<script>window.location.href='index.php';</script>"; // Redirect to homepage
            }
        } else {
            echo "<script>alert('Invalid password.');</script>";
        }
    } else {
        echo "<script>alert('User not found.');</script>";
    }
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    <link href="public/style.css" rel="stylesheet">

</head>

<body class="bg-gray-100">
    <div class="flex justify-center items-center h-screen">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-sm w-full">
            <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>
            <form method="POST" action="login.php" class="space-y-4">
                <div>
                    <label for="username" class="block text-gray-700">Username:</label>
                    <input type="text" name="username"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                        required>
                </div>
                <div>
                    <label for="password" class="block text-gray-700">Password:</label>
                    <input type="password" name="password"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                        required>
                </div>
                <button type="submit"
                    class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">Login</button>
            </form>
        </div>
    </div>
</body>

</html>