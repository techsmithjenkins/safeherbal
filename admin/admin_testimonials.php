<?php
session_start();
if (!isset($_SESSION["admin_id"])) header("Location: login.php");

include "../config/db_connect.php";

// Handle approval
if (isset($_GET['approve_id']) && is_numeric($_GET['approve_id'])) {
    $approve_id = $_GET['approve_id'];
    $stmt = $conn->prepare("UPDATE testimonials SET status = 'approved' WHERE id = ? AND status = 'pending'");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $approve_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_testimonials.php");
    exit();
}

// Handle deletion
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM testimonials WHERE id = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_testimonials.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FREDY HERBAL | Testimonial Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="../assets/admin.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet" />
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
        background: radial-gradient(circle at center, #f0fdf4 0%, #dcfce7 70%, #bbf7d0 100%);
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
<body class="herb-bg min-h-screen p-6 relative">
    <div class="leaf-decoration top-10 left-10"></div>
    <div class="leaf-decoration bottom-10 right-10 rotate-45"></div>

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-xl z-10">
        <h2 class="text-3xl font-serif font-bold text-center text-primary mb-6">Testimonial Management</h2>
        <a href="index.php" class="inline-flex items-center text-secondary font-semibold hover:text-secondaryDark mb-6">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>

        <h3 class="text-xl font-serif font-semibold text-primary mt-8 mb-4">Pending Testimonials</h3>
        <div class="space-y-6">
            <?php
            $result = $conn->query("SELECT * FROM testimonials WHERE status = 'pending' ORDER BY submitted_at DESC");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='p-6 border border-gray-200 rounded-2xl bg-primaryLight shadow-md'>";
                    echo "<h4 class='text-lg font-medium text-primary'>{$row['name']}'s Testimonial</h4>";
                    if ($row['photo']) echo "<img src='data:{$row['photo_mime']};base64," . base64_encode($row['photo']) . "' alt='{$row['name']}' class='mt-2 max-w-xs rounded-lg'>";
                    echo "<p class='text-gray-700 mt-2'>{$row['message']}</p>";
                    echo "<p class='text-sm text-gray-500 mt-1'>Added: {$row['submitted_at']}</p>";
                    echo "<a href='?approve_id={$row['id']}' class='text-green-600 hover:text-green-800 mt-2 mr-4 inline-block' onclick=\"return confirm('Approve this testimonial?');\">Approve</a>";
                    echo "<a href='?delete_id={$row['id']}' class='text-red-600 hover:text-red-800 mt-2 inline-block' onclick=\"return confirm('Delete this testimonial?');\">Delete</a>";
                    echo "</div>";
                }
            } else {
                echo "<p class='text-gray-600'>No pending testimonials.</p>";
            }
            ?>
        </div>

        <h3 class="text-xl font-serif font-semibold text-primary mt-8 mb-4">Approved Testimonials</h3>
        <div class="space-y-6">
            <?php
            $result = $conn->query("SELECT * FROM testimonials WHERE status = 'approved' ORDER BY submitted_at DESC");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='p-6 border border-gray-200 rounded-2xl bg-primaryLight shadow-md'>";
                    echo "<h4 class='text-lg font-medium text-primary'>{$row['name']}'s Testimonial</h4>";
                    if ($row['photo']) echo "<img src='data:{$row['photo_mime']};base64," . base64_encode($row['photo']) . "' alt='{$row['name']}' class='mt-2 max-w-xs rounded-lg'>";
                    echo "<p class='text-gray-700 mt-2'>{$row['message']}</p>";
                    echo "<p class='text-sm text-gray-500 mt-1'>Added: {$row['submitted_at']}</p>";
                    echo "<a href='?delete_id={$row['id']}' class='text-red-600 hover:text-red-800 mt-2 inline-block' onclick=\"return confirm('Delete this testimonial?');\">Delete</a>";
                    echo "</div>";
                }
            } else {
                echo "<p class='text-gray-600'>No approved testimonials.</p>";
            }
            // Close connection after all queries
            $conn->close();
            ?>
        </div>
    </div>
</body>
</html>