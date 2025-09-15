<?php
// admin/index.php - Admin dashboard for FREDY HERBAL website
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

include "../config/db_connect.php";

if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . (isset($conn) ? $conn->connect_error : "Connection not initialized"));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FREDY HERBAL | Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="../assets/admin.png" type="png">
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

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md z-10">
        <h1 class="text-3xl font-serif font-bold text-center text-primary mb-4">
            Admin Dashboard
        </h1>
        <p class="text-lg text-center text-gray-700 mb-6">
            Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!
        </p>
        <ul class="space-y-4">
            <li>
                <a
                    href="articles.php"
                    class="block w-full bg-primary text-white py-3 rounded-full text-lg font-semibold text-center hover:bg-primaryDark transition-transform transform hover:scale-105 shadow-lg"
                >
                    <i class="fas fa-file-alt mr-2"></i>Manage Articles
                </a>
            </li>
            <li>
                <a
                    href="gallery.php"
                    class="block w-full bg-primary text-white py-3 rounded-full text-lg font-semibold text-center hover:bg-primaryDark transition-transform transform hover:scale-105 shadow-lg"
                >
                    <i class="fas fa-image mr-2"></i>Manage Gallery
                </a>
            </li>
            <li>
                <a
                    href="testimonials.php"
                    class="block w-full bg-primary text-white py-3 rounded-full text-lg font-semibold text-center hover:bg-primaryDark transition-transform transform hover:scale-105 shadow-lg"
                >
                    <i class="fas fa-comments mr-2"></i>Manage Testimonials
                </a>
            </li>
            <li>
                <a
                    href="edit_content.php"
                    class="block w-full bg-primary text-white py-3 rounded-full text-lg font-semibold text-center hover:bg-primaryDark transition-transform transform hover:scale-105 shadow-lg"
                >
                    <i class="fas fa-edit mr-2"></i>Manage Website Content
                </a>
            </li>
            <li>
                <a
                    href="treatment_management.php"
                    class="block w-full bg-primary text-white py-3 rounded-full text-lg font-semibold text-center hover:bg-primaryDark transition-transform transform hover:scale-105 shadow-lg"
                >
                    <i class="fas fa-mortar-pestle mr-2"></i>Manage Treatments Section
                </a>
            </li>
            <li>
                <a
                    href="admin_messages.php"
                    class="block w-full bg-primary text-white py-3 rounded-full text-lg font-semibold text-center hover:bg-primaryDark transition-transform transform hover:scale-105 shadow-lg"
                >
                    <i class="fas fa-envelope mr-2"></i>Manage Contact Messages
                </a>
            </li>
            <li>
                <a
                    href="login.php?logout=1"
                    class="block w-full bg-secondary text-white py-3 rounded-full text-lg font-semibold text-center hover:bg-secondaryDark transition-transform transform hover:scale-105 shadow-lg"
                >
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </li>
        </ul>
    </div>
</body>
</html>