<?php
include 'db_connection.php';

$error = ""; // Variable to store error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_POST['email'];
    $role = 'user';

    // Validate password length
    if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    }
    // Check if passwords match
    elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if username or email exists
        $check_sql = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
        $result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($result) > 0) {
            $error = "Username or Email already exists."; // Store error message
        } else {
            $sql = "INSERT INTO users (username, password, email, role) VALUES ('$username', '$password', '$email', '$role')";

            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
                exit; // Stop further execution
            } else {
                echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
            }
        }
    }
}

mysqli_close($conn);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="public/style.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <!-- Error Modal -->
    <div id="error-modal"
        style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border: 1px solid black; text-align: center;">
        <h2 style="color: red;">Registration Error</h2>
        <p><?php echo $error; ?></p>
        <button onclick="closeModal()">OK</button>
    </div>

    <div class="flex justify-center items-center h-screen">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-sm w-full">
            <h2 class="text-2xl font-bold mb-6 text-center">Register</h2>
            <form method="POST" action="register.php" class="space-y-4">
                <div>
                    <label for="username" class="block text-gray-700">Username:</label>
                    <input type="text" name="username"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                        required>
                </div>
                <div>
                    <label for="email" class="block text-gray-700">Email:</label>
                    <input type="email" name="email"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                        required>
                </div>
                <div>
                    <label for="password" class="block text-gray-700">Password:</label>
                    <input type="password" name="password"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                        required>
                </div>
                <div>
                    <label for="confirm_password" class="block text-gray-700">Confirm Password:</label>
                    <input type="password" name="confirm_password" id="confirm_password"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                        required>
                </div>
                <div>
                    <label for="role" class="block text-gray-700">Role:</label>
                    <select disabled name="role"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="staff">User</option>
                    </select>
                </div>
                <button type="submit"
                    class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">Register</button>
            </form>
        </div>
    </div>

    <script>
        function closeModal() {
            document.getElementById('error-modal').style.display = 'none';
        }

        // Show modal if there is an error
        <?php if (!empty($error)) { ?>
            document.getElementById('error-modal').style.display = 'block';
        <?php } ?>

        // Frontend validation for password length and match
        document.querySelector("form").addEventListener("submit", function (event) {
            let password = document.getElementById("password").value;
            let confirmPassword = document.getElementById("confirm_password").value;

            if (password.length < 8) {
                alert("Password must be at least 8 characters long.");
                event.preventDefault(); // Stop form submission
            } else if (password !== confirmPassword) {
                alert("Passwords do not match.");
                event.preventDefault(); // Stop form submission
            }
        });
    </script>

</body>

</html>