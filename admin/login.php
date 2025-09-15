<?php
session_start();
include "../config/db_connect.php";

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

if (isset($_SESSION["admin_id"])) header("Location: index.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST["username"]);
    $password = $_POST["password"];

    $sql = "SELECT id, username, password FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($password, $admin["password"])) {
        $_SESSION["admin_id"] = $admin["id"];
        $_SESSION["username"] = $admin["username"];
        header("Location: index.php");
    } else {
        $error = "Invalid credentials. Try again.";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FREDY HERBAL | Admin Sign In</title>
    <link rel="icon" href="../assets/admin.png" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap"
      rel="stylesheet"
    />
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

  <body class="herb-bg min-h-screen flex items-center justify-center relative">
    <div class="leaf-decoration top-10 left-10"></div>
    <div class="leaf-decoration bottom-10 right-10 rotate-45"></div>

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md z-10">
      <h2 class="text-3xl font-serif font-bold text-center text-primary mb-6">
        Admin Sign In
      </h2>
      <?php if (isset($error)) echo "<p class='mt-4 text-sm text-red-600 text-center'>$error</p>"; ?>
      <form method="post" class="space-y-5">
        <div>
          <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
            Username
          </label>
          <input
            type="text"
            name="username"
            id="username"
            required
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primaryLight transition"
            placeholder="admin"
          />
        </div>
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
            Password
          </label>
          <input
            type="password"
            name="password"
            id="password"
            required
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primaryLight transition"
            placeholder="********"
          />
        </div>
        <button
          type="submit"
          class="w-full bg-primary text-white py-3 rounded-full text-lg font-semibold hover:bg-primaryDark transition-transform transform hover:scale-105 shadow-lg"
        >
          Sign In
        </button>
      </form>
      <div class="mt-6 text-center">
        <a href="../pages/homepage.php" target="_blank" class="inline-flex items-center text-secondary font-semibold hover:text-secondaryDark">
          <i class="fas fa-eye mr-2"></i>View Website
        </a>
      </div>
    </div>
  </body>
</html>