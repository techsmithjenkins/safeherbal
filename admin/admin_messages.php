<?php
// admin_messages.php - Admin page to view and delete contact form submissions
session_start();
require_once '../config/db_connect.php'; // Path to match admin/index.php

// Check if user is logged in and is an admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . (isset($conn) ? $conn->connect_error : "Connection not initialized"));
}

// Handle delete request
$notification = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = filter_input(INPUT_POST, 'delete_id', FILTER_SANITIZE_NUMBER_INT);
    if ($delete_id) {
        $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            $notification = "<p class='text-green-600 text-center'>Message deleted successfully.</p>";
        } else {
            $notification = "<p class='text-red-600 text-center'>Error deleting message: " . htmlspecialchars($conn->error) . "</p>";
        }
        $stmt->close();
    } else {
        $notification = "<p class='text-red-600 text-center'>Invalid message ID.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Contact Messages - FREDY HERBAL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="../assets/admin.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#16a34a',
                        primaryLight: '#dcfce7',
                        primaryDark: '#15803d',
                        secondary: '#a16207',
                        deepBlue: '#01017f',
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                        script: ['Dancing Script', 'cursive'],
                    },
                },
            },
        };
    </script>
    <style>
        .herb-bg {
            background: radial-gradient(
                circle at center,
                #f0fdf4 0%,
                #dcfce7 70%,
                #bbf7d0 100%
            );
        }
        .leaf-decoration {
            position: absolute;
            width: 100px;
            height: 100px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2316a34a' stroke-width='1' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z'/%3E%3Cpath d='M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12'/%3E%3C/svg%3E");
            background-size: contain;
            background-repeat: no-repeat;
            opacity: 0.1;
        }
    </style>
</head>
<body class="herb-bg min-h-screen flex flex-col items-center justify-center relative">
    <div class="leaf-decoration top-10 left-10"></div>
    <div class="leaf-decoration bottom-10 right-10 rotate-45"></div>
    <div class="container mx-auto px-4 py-8 max-w-6xl bg-white rounded-2xl shadow-xl z-10">
        <h2 class="text-3xl font-serif font-bold mb-8 text-center text-primary">Contact Messages</h2>
        <?php if ($notification): ?>
            <div class="mb-4"><?php echo $notification; ?></div>
        <?php endif; ?>
        <div class="p-6">
            <?php
            $result = $conn->query("SELECT * FROM contact_messages ORDER BY submitted_at DESC");
            if ($result === false) {
                echo "<p class='text-red-600 text-center'>Error fetching messages: " . htmlspecialchars($conn->error) . "</p>";
            } elseif ($result->num_rows === 0) {
                echo "<p class='text-gray-600 text-center'>No messages found.</p>";
            } else {
                echo "<div class='overflow-x-auto'>";
                echo "<table class='w-full table-auto'>";
                echo "<thead><tr class='bg-primaryLight text-left'><th class='px-4 py-2 font-semibold'>Name</th><th class='px-4 py-2 font-semibold'>Email</th><th class='px-4 py-2 font-semibold'>Phone</th><th class='px-4 py-2 font-semibold'>Message</th><th class='px-4 py-2 font-semibold'>Submitted</th><th class='px-4 py-2 font-semibold'>Action</th></tr></thead>";
                echo "<tbody>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr class='border-b'>";
                    echo "<td class='px-4 py-2'>" . htmlspecialchars($row['full_name']) . "</td>";
                    echo "<td class='px-4 py-2'>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td class='px-4 py-2'>" . ($row['phone'] ? htmlspecialchars($row['phone']) : 'N/A') . "</td>";
                    echo "<td class='px-4 py-2'>" . htmlspecialchars($row['message']) . "</td>";
                    echo "<td class='px-4 py-2'>" . htmlspecialchars($row['submitted_at']) . "</td>";
                    echo "<td class='px-4 py-2'>";
                    echo "<form method='POST' onsubmit='return confirm(\"Are you sure you want to delete this message?\");'>";
                    echo "<input type='hidden' name='delete_id' value='" . htmlspecialchars($row['id']) . "'>";
                    echo "<button type='submit' class='text-red-600 hover:text-red-800 font-semibold'><i class='fas fa-trash-alt mr-1'></i>Delete</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</tbody></table></div>";
                $result->free();
            }
            ?>
        </div>
        <a href="index.php" class="mt-6 inline-block bg-primary hover:bg-primaryDark text-white px-6 py-3 rounded-full text-lg font-semibold text-center transition-transform transform hover:scale-105 shadow-lg">Back to Dashboard</a>
    </div>
</body>
</html>