<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
  header("Location: login.php");
  exit;
}

include "../config/db_connect.php";
include "../includes/functions.php"; // Assumes uploadImage function

// Handle article submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['type']) && $_POST['type'] == 'article') {
  $imageName = isset($_FILES["article_image"]) && $_FILES["article_image"]['error'] == 0 ? uploadImage($_FILES["article_image"]) : null;
  $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_STRING);
  $excerpt = filter_input(INPUT_POST, "excerpt", FILTER_SANITIZE_STRING);
  $content = filter_input(INPUT_POST, "content", FILTER_SANITIZE_STRING);
  $tags = filter_input(INPUT_POST, "tags", FILTER_SANITIZE_STRING);
  $author = filter_input(INPUT_POST, "author", FILTER_SANITIZE_STRING);
  $date_added = date("Y-m-d");
  $is_approved = 0; // Default to pending

  $sql = "INSERT INTO articles (title, excerpt, content, image_url, date_added, tags, author, is_approved) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssssssi", $title, $excerpt, $content, $imageName, $date_added, $tags, $author, $is_approved);
  $stmt->execute();
  $stmt->close();
}

// Handle article deletion
if (isset($_GET['delete_article_id']) && is_numeric($_GET['delete_article_id'])) {
  $delete_id = $_GET['delete_article_id'];
  $stmt = $conn->prepare("DELETE FROM articles WHERE id = ?");
  $stmt->bind_param("i", $delete_id);
  $stmt->execute();
  $stmt->close();
  header("Location: articles.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FREDY HERBAL | Content Management</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" href="../assets/admin.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap"
    rel="stylesheet" />
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
    <h2 class="text-3xl font-serif font-bold text-center text-primary mb-6">Article Management</h2>
    <a href="index.php" class="inline-flex items-center text-secondary font-semibold hover:text-secondaryDark mb-6">
      <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
    </a>

    <!-- Article Form -->
    <h3 class="text-xl font-serif font-semibold text-primary mt-6 mb-4">Create New Article</h3>
    <form method="post" enctype="multipart/form-data" class="space-y-5 mb-8">
      <input type="hidden" name="type" value="article">
      <div>
        <label for="article_title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
        <input type="text" name="title" id="article_title" required
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primaryLight transition"
          placeholder="Enter article title" />
      </div>
      <div>
        <label for="article_image" class="block text-sm font-medium text-gray-700 mb-1">Image</label>
        <div
          class="w-full border-2 border-dashed border-gray-400 rounded-xl p-6 text-center cursor-pointer hover:border-green-500 hover:bg-green-100 transition relative"
          onclick="document.getElementById('article_image').click()"
          ondragover="event.preventDefault(); this.classList.add('border-green-500');"
          ondragleave="this.classList.remove('border-green-500');"
          ondrop="event.preventDefault(); handleDrop('article_image', event);">
          <input type="file" name="article_image" id="article_image" accept="image/*" class="hidden"
            onchange="previewFile('article_image', event)" />
          <div id="article_upload_placeholder" class="flex flex-col items-center">
            <svg class="mx-auto mb-3 w-10 h-10 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
              viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6h.1a5 5 0 010 10h-1m-2-2l-3 3m0 0l-3-3m3 3V10" />
            </svg>
            <p class="text-gray-600"><span class="font-semibold">Drag & Drop</span> your image here <br /> or <span
                class="text-green-600">click to browse</span></p>
          </div>
          <div id="article_preview_container" class="hidden mt-4 relative">
            <button type="button" onclick="removeImage('article_image')"
              class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-md hover:bg-red-600">✕</button>
            <img id="article_preview_image" class="mx-auto max-h-48 rounded-lg shadow-md" alt="Preview" />
            <p class="mt-2 text-sm text-gray-500" id="article_file_name"></p>
          </div>
        </div>
      </div>
      <div>
        <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-1">Excerpt</label>
        <textarea name="excerpt" id="excerpt" required
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primaryLight transition"
          placeholder="Enter article excerpt" rows="4"></textarea>
      </div>
      <div>
        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
        <textarea name="content" id="content" required
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primaryLight transition"
          placeholder="Enter article content" rows="8"></textarea>
      </div>
      <div>
        <label for="tags" class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
        <div id="tags_wrapper"
          class="flex flex-wrap items-center gap-2 border border-gray-300 rounded-lg px-3 py-2 focus-within:border-primary focus-within:ring-2 focus-within:ring-primaryLight transition">
          <input type="text" id="tags_input" placeholder="Type and press Enter..."
            class="flex-1 border-none focus:ring-0 outline-none text-gray-700" />
        </div>
        <input type="hidden" name="tags" id="tags" />
      </div>
      <div>
        <label for="author" class="block text-sm font-medium text-gray-700 mb-1">Author</label>
        <input type="text" name="author" id="author" required
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primaryLight transition"
          placeholder="Enter author name" />
      </div>
      <button type="submit"
        class="w-full bg-primary text-white py-3 rounded-full text-lg font-semibold hover:bg-primaryDark transition-transform transform hover:scale-105 shadow-lg">Submit
        Article</button>
    </form>
  </div>

  <div class="max-w-4xl mx-auto mt-8 bg-white p-8 rounded-2xl shadow-xl z-10">


    <!-- Existing Articles -->
    <h3 class="text-xl font-serif font-semibold text-primary mt-8 mb-4">Existing Articles</h3>
    <div class="space-y-6">
      <?php
      $result = $conn->query("SELECT * FROM articles ORDER BY date_added DESC");
      while ($row = $result->fetch_assoc()) {
        echo "<div class='p-6 border border-gray-200 rounded-2xl bg-primaryLight shadow-md'>";
        echo "<h4 class='text-lg font-medium text-primary'>" . htmlspecialchars($row['title']) . "</h4>";
        if ($row['image_url']) {
          echo "<img src='../Uploads/{$row['image_url']}' alt='" . htmlspecialchars($row['title']) . "' class='mt-2 max-w-xs rounded-lg'>";
        }
        echo "<p class='text-gray-700 mt-2'>" . htmlspecialchars($row['excerpt']) . "</p>";
        echo "<p class='text-sm text-gray-500 mt-1'>Added: {$row['date_added']}</p>";
        echo "<a href='?delete_article_id={$row['id']}' class='mt-2 inline-flex items-center px-3 py-1 bg-red-100 text-red-700 hover:bg-red-200 hover:text-red-900 rounded transition font-semibold' onclick=\"return confirm('Are you sure you want to delete this article?');\">";
        echo "<i class='fas fa-trash-alt mr-1'></i>Delete</a>";
        echo "</div>";
      }
      ?>
    </div>

  </div>

  <script>
    function previewFile(inputId, event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          document.getElementById(`${inputId}_preview_image`).src = e.target.result;
          document.getElementById(`${inputId}_file_name`).textContent = file.name;
          document.getElementById(`${inputId}_upload_placeholder`).classList.add("hidden");
          document.getElementById(`${inputId}_preview_container`).classList.remove("hidden");
        };
        reader.readAsDataURL(file);
      }
    }

    function handleDrop(inputId, event) {
      const files = event.dataTransfer.files;
      if (files.length > 0) {
        document.getElementById(inputId).files = files;
        previewFile(inputId, { target: { files: files } });
      }
    }

    function removeImage(inputId) {
      document.getElementById(inputId).value = "";
      document.getElementById(`${inputId}_preview_container`).classList.add("hidden");
      document.getElementById(`${inputId}_upload_placeholder`).classList.remove("hidden");
    }

    function openEditForm(id, title, caption, image) {
      document.getElementById('edit_treatment_id').value = id;
      document.getElementById('edit_treatment_title').value = title;
      document.getElementById('edit_treatment_caption').value = caption;
      document.getElementById('edit_treatment_existing_image').value = image;
      if (image) {
        document.getElementById('edit_treatment_preview_image').src = `../Uploads/${image}`;
        document.getElementById('edit_treatment_file_name').textContent = image.split('/').pop();
        document.getElementById('edit_treatment_upload_placeholder').classList.add("hidden");
        document.getElementById('edit_treatment_preview_container').classList.remove("hidden");
      } else {
        document.getElementById('edit_treatment_upload_placeholder').classList.remove("hidden");
        document.getElementById('edit_treatment_preview_container').classList.add("hidden");
      }
      document.getElementById('edit_treatment_form').classList.remove('hidden');
    }

    function closeEditForm() {
      document.getElementById('edit_treatment_form').classList.add('hidden');
    }
  </script>
</body>

</html>