<?php
session_start();
if (!isset($_SESSION["admin_id"])) header("Location: login.php");

include "../config/db_connect.php";

// Handle approval
if (isset($_GET['approve_id']) && is_numeric($_GET['approve_id'])) {
    $approve_id = $_GET['approve_id'];
    $stmt = $conn->prepare("UPDATE testimonials SET is_approved = 1 WHERE id = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $approve_id);
    $stmt->execute();
    $stmt->close();
    header("Location: testimonials.php");
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
    header("Location: testimonials.php");
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
</head>
<body class="bg-gray-50 min-h-screen p-6">
  <div class="max-w-5xl mx-auto bg-white p-6 md:p-8 rounded-2xl shadow-xl">
    <h2 class="text-2xl md:text-3xl font-serif font-bold text-center text-primary mb-6">Testimonial Management</h2>
    <a href="index.php" class="inline-flex items-center text-secondary font-semibold hover:text-secondary/80 mb-6 transition-colors">
      <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
    </a>

    <!-- Pending Testimonials -->
    <h3 class="text-lg md:text-xl font-serif font-semibold text-primary mt-8 mb-4">Pending Testimonials</h3>
    <div class="space-y-6">
      <?php
      $result = $conn->query("SELECT * FROM testimonials WHERE is_approved = 0 ORDER BY submitted_at DESC");
      if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo "<div class='p-4 md:p-6 border border-gray-200 rounded-2xl bg-primaryLight shadow-md'>";
              echo "<h4 class='text-base md:text-lg font-medium text-primary mb-3'>" . htmlspecialchars($row['name']) . " on " . htmlspecialchars($row['topic']) . "</h4>";
              echo "<p class='text-gray-700 mt-3 text-sm md:text-base'>" . nl2br(htmlspecialchars($row['message'])) . "</p>";
              echo "<p class='text-xs md:text-sm text-gray-500 mt-2'>Submitted: " . htmlspecialchars($row['submitted_at']) . "</p>";

              if (!empty($row['photo'])) {
                  $imgData = base64_encode($row['photo']);
                  $mime = htmlspecialchars($row['photo_mime']);
                  echo "<img src='data:$mime;base64,$imgData' alt='Photo' class='mt-3 max-w-xs rounded-lg'>";
              }

              echo "<div class='flex gap-2 mt-4'>";
              echo "<a href='?approve_id=" . $row['id'] . "' class='px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700' onclick=\"return confirm('Approve this testimonial?');\">Approve</a>";
              echo "<a href='?delete_id=" . $row['id'] . "' class='px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700' onclick=\"return confirm('Delete this testimonial?');\">Delete</a>";
              echo "</div></div>";
          }
      } else {
          echo "<div class='p-6 text-center border border-gray-200 rounded-2xl bg-gray-50'>";
          echo "<p class='text-gray-600'>No pending testimonials.</p>";
          echo "</div>";
      }
      ?>
    </div>

    <!-- Approved Testimonials -->
    <h3 class="text-lg md:text-xl font-serif font-semibold text-primary mt-8 mb-4">Approved Testimonials</h3>
    <div class="space-y-6">
      <?php
      $result = $conn->query("SELECT * FROM testimonials WHERE is_approved = 1 ORDER BY submitted_at DESC");
      if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo "<div class='p-4 md:p-6 border border-gray-200 rounded-2xl bg-green-50 shadow-md'>";
              echo "<div class='flex items-center mb-3'>";
              echo "<div class='text-green-600 mr-2'><i class='fas fa-check-circle'></i></div>";
              echo "<h4 class='text-base md:text-lg font-medium text-primary'>" . htmlspecialchars($row['name']) . " on " . htmlspecialchars($row['topic']) . "</h4>";
              echo "</div>";
              echo "<p class='text-gray-700 mt-3 text-sm md:text-base'>" . nl2br(htmlspecialchars($row['message'])) . "</p>";
              echo "<p class='text-xs md:text-sm text-gray-500 mt-2'>Submitted: " . htmlspecialchars($row['submitted_at']) . "</p>";

              if (!empty($row['photo'])) {
                  $imgData = base64_encode($row['photo']);
                  $mime = htmlspecialchars($row['photo_mime']);
                  echo "<img src='data:$mime;base64,$imgData' alt='Photo' class='mt-3 max-w-xs rounded-lg'>";
              }

              echo "<div class='flex gap-2 mt-4'>";
              echo "<a href='?delete_id=" . $row['id'] . "' class='px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700' onclick=\"return confirm('Delete this testimonial?');\">Delete</a>";
              echo "</div></div>";
          }
      } else {
          echo "<div class='p-6 text-center border border-gray-200 rounded-2xl bg-gray-50'>";
          echo "<p class='text-gray-600'>No approved testimonials yet.</p>";
          echo "</div>";
      }
      $conn->close();
      ?>
    </div>
  </div>
</body>
</html>
