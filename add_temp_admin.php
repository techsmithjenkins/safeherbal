<?php
// add_temp_admin.php - Script to add a temporary admin user for troubleshooting login issues

// Include database connection
include "config/db_connect.php";

// Temporary user details - Update these before running
$username = "Admin";
$email = "admin@fredyherbal.com";
$password = "pass123"; // Change to a strong, unique password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Temporary Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white shadow-lg rounded-xl p-6 max-w-lg w-full text-center border border-gray-200">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Add Temporary Admin</h1>
    <div class="text-left space-y-3">
        <?php
        // Prepare and execute SQL insert
        try {
            $sql = "INSERT INTO admins (username, password, email) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username, $hashed_password, $email);

            if ($stmt->execute()) {
                echo "<div class='p-4 bg-green-100 text-green-700 rounded-lg border border-green-300'>";
                echo "<p class='font-semibold'>✅ Temporary admin user created successfully!</p>";
                echo "<p><span class='font-medium'>Username:</span> $username</p>";
                echo "<p><span class='font-medium'>Email:</span> $email</p>";
                echo "<p><span class='font-medium'>Password:</span> <code class='bg-gray-200 px-2 py-1 rounded'>$password</code></p>";
                echo "</div>";
                echo "<p class='mt-4 text-gray-600 text-sm'>➡️ Try logging in at <a href='fredyherbal/admin/login.php' class='text-blue-600 hover:underline'>/admin/login.php</a></p>";
                echo "<p class='text-red-600 text-sm mt-2 font-medium'>⚠️ For security, delete this script immediately after use.</p>";
            } else {
                echo "<div class='p-4 bg-red-100 text-red-700 rounded-lg border border-red-300'>";
                echo "<p class='font-semibold'>❌ Error adding user:</p>";
                echo "<p>" . htmlspecialchars($conn->error) . "</p>";
                echo "</div>";
            }

            $stmt->close();
        } catch (Exception $e) {
            echo "<div class='p-4 bg-red-100 text-red-700 rounded-lg border border-red-300'>";
            echo "<p class='font-semibold'>⚠️ Exception:</p>";
            echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
            echo "</div>";
        }

        $conn->close();
        ?>
    </div>
</div>

</body>
</html>
