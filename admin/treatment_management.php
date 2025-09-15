<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
  header("Location: login.php");
  exit;
}

include "../config/db_connect.php";
include "../includes/functions.php"; // Assumes uploadImage function

// Handle treatment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['type']) && $_POST['type'] == 'treatment') {
  $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_STRING);
  $caption = filter_input(INPUT_POST, "caption", FILTER_SANITIZE_STRING);
  $imageName = isset($_FILES["treatment_image"]) && $_FILES["treatment_image"]['error'] == 0 ? uploadImage($_FILES["treatment_image"]) : null;

  $sql = "INSERT INTO treatments (title, caption, image) VALUES (?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sss", $title, $caption, $imageName);
  $stmt->execute();
  $stmt->close();
}

// Handle treatment deletion
if (isset($_GET['delete_treatment_id']) && is_numeric($_GET['delete_treatment_id'])) {
  $delete_id = $_GET['delete_treatment_id'];
  $stmt = $conn->prepare("DELETE FROM treatments WHERE id = ?");
  $stmt->bind_param("i", $delete_id);
  $stmt->execute();
  $stmt->close();
  header("Location: articles.php");
  exit;
}

// Handle treatment edit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['type']) && $_POST['type'] == 'edit_treatment') {
  $id = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);
  $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_STRING);
  $caption = filter_input(INPUT_POST, "caption", FILTER_SANITIZE_STRING);
  $imageName = isset($_FILES["treatment_image"]) && $_FILES["treatment_image"]['error'] == 0 ? uploadImage($_FILES["treatment_image"]) : $_POST['existing_image'];

  $sql = "UPDATE treatments SET title = ?, caption = ?, image = ? WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssi", $title, $caption, $imageName, $id);
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

  <div class="max-w-4xl mx-auto mt-8 bg-white p-8 rounded-2xl shadow-xl z-10">
   <a href="index.php" class="inline-flex items-center text-secondary font-semibold hover:text-secondaryDark mb-6">
      <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
    </a>

  <!-- Treatment Form -->
  <h2 class="text-3xl font-serif font-bold text-center text-primary mb-6">Treatment Management</h2>
  <form method="post" enctype="multipart/form-data" class="space-y-5 mb-8">
    <input type="hidden" name="type" value="treatment">
    <div>
      <label for="treatment_title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
      <input type="text" name="title" id="treatment_title" required
        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primaryLight transition"
        placeholder="Enter treatment title" />
    </div>
    <div>
      <label for="treatment_image" class="block text-sm font-medium text-gray-700 mb-1">Image (optional)</label>
      <div
        class="w-full border-2 border-dashed border-gray-400 rounded-xl p-6 text-center cursor-pointer hover:border-green-500 hover:bg-green-100 transition relative"
        onclick="document.getElementById('treatment_image').click()"
        ondragover="event.preventDefault(); this.classList.add('border-green-500');"
        ondragleave="this.classList.remove('border-green-500');"
        ondrop="event.preventDefault(); handleDrop('treatment_image', event);">
        <input type="file" name="treatment_image" id="treatment_image" accept="image/*" class="hidden"
          onchange="previewFile('treatment_image', event)" />
        <div id="treatment_upload_placeholder" class="flex flex-col items-center">
          <svg class="mx-auto mb-3 w-10 h-10 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6h.1a5 5 0 010 10h-1m-2-2l-3 3m0 0l-3-3m3 3V10" />
          </svg>
          <p class="text-gray-600"><span class="font-semibold">Drag & Drop</span> your image here <br /> or <span
              class="text-green-600">click to browse</span></p>
        </div>
        <div id="treatment_preview_container" class="hidden mt-4 relative">
          <button type="button" onclick="removeImage('treatment_image')"
            class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-md hover:bg-red-600">✕</button>
          <img id="treatment_preview_image" class="mx-auto max-h-48 rounded-lg shadow-md" alt="Preview" />
          <p class="mt-2 text-sm text-gray-500" id="treatment_file_name"></p>
        </div>
      </div>
    </div>
    <div>
      <label for="caption" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
      <textarea name="caption" id="caption" required
        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primaryLight transition"
        placeholder="Enter treatment description" rows="4"></textarea>
    </div>
    <button type="submit"
      class="w-full bg-primary text-white py-3 rounded-full text-lg font-semibold hover:bg-primaryDark transition-transform transform hover:scale-105 shadow-lg">Submit
      Treatment</button>
  </form>

  <!-- Existing Treatments -->
  <h3 class="text-xl font-serif font-semibold text-primary mt-8 mb-4">Existing Treatments</h3>
  <div class="space-y-6">
    <?php
    $result = $conn->query("SELECT * FROM treatments ORDER BY created_at DESC");
    while ($row = $result->fetch_assoc()) {
      echo "<div class='p-6 border border-gray-200 rounded-2xl bg-primaryLight shadow-md'>";
      echo "<h4 class='text-lg font-medium text-primary'>" . htmlspecialchars($row['title']) . "</h4>";
      if ($row['image']) {
        echo "<img src='../Uploads/{$row['image']}' alt='" . htmlspecialchars($row['title']) . "' class='mt-2 max-w-xs rounded-lg'>";
      }
      echo "<p class='text-gray-700 mt-2'>" . nl2br(htmlspecialchars($row['caption'])) . "</p>";
      echo "<p class='text-sm text-gray-500 mt-1'>Added: {$row['created_at']}</p>";
      echo "<button onclick=\"openEditForm({$row['id']}, '" . htmlspecialchars($row['title'], ENT_QUOTES) . "', '" . htmlspecialchars($row['caption'], ENT_QUOTES) . "', '" . ($row['image'] ? htmlspecialchars($row['image'], ENT_QUOTES) : '') . "')\" class='mt-2 inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 hover:bg-blue-200 hover:text-blue-900 rounded transition font-semibold'>";
      echo "<i class='fas fa-edit mr-1'></i>Edit</button>";
      echo "<a href='?delete_treatment_id={$row['id']}' class='mt-2 inline-flex items-center px-3 py-1 bg-red-100 text-red-700 hover:bg-red-200 hover:text-red-900 rounded transition font-semibold ml-2' onclick=\"return confirm('Are you sure you want to delete this treatment?');\">";
      echo "<i class='fas fa-trash-alt mr-1'></i>Delete</a>";
      echo "</div>";
    }
    $conn->close();
    ?>
  </div>

  <!-- Edit Treatment Form (Hidden) -->
  <div id="edit_treatment_form"
    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full">
      <h3 class="text-xl font-serif font-semibold text-primary mb-4">Edit Treatment</h3>
      <form method="post" enctype="multipart/form-data" class="space-y-5">
        <input type="hidden" name="type" value="edit_treatment">
        <input type="hidden" name="id" id="edit_treatment_id">
        <div>
          <label for="edit_treatment_title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
          <input type="text" name="title" id="edit_treatment_title" required
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primaryLight transition" />
        </div>
        <div>
          <label for="edit_treatment_image" class="block text-sm font-medium text-gray-700 mb-1">Image
            (optional)</label>
          <div
            class="w-full border-2 border-dashed border-gray-400 rounded-xl p-6 text-center cursor-pointer hover:border-green-500 hover:bg-green-100 transition relative"
            onclick="document.getElementById('edit_treatment_image').click()"
            ondragover="event.preventDefault(); this.classList.add('border-green-500');"
            ondragleave="this.classList.remove('border-green-500');"
            ondrop="event.preventDefault(); handleDrop('edit_treatment_image', event);">
            <input type="file" name="treatment_image" id="edit_treatment_image" accept="image/*" class="hidden"
              onchange="previewFile('edit_treatment_image', event)" />
            <div id="edit_treatment_upload_placeholder" class="flex flex-col items-center">
              <svg class="mx-auto mb-3 w-10 h-10 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6h.1a5 5 0 010 10h-1m-2-2l-3 3m0 0l-3-3m3 3V10" />
              </svg>
              <p class="text-gray-600"><span class="font-semibold">Drag & Drop</span> your image here <br /> or <span
                  class="text-green-600">click to browse</span></p>
            </div>
            <div id="edit_treatment_preview_container" class="hidden mt-4 relative">
              <button type="button" onclick="removeImage('edit_treatment_image')"
                class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-md hover:bg-red-600">✕</button>
              <img id="edit_treatment_preview_image" class="mx-auto max-h-48 rounded-lg shadow-md" alt="Preview" />
              <p class="mt-2 text-sm text-gray-500" id="edit_treatment_file_name"></p>
            </div>
          </div>
          <input type="hidden" name="existing_image" id="edit_treatment_existing_image">
        </div>
        <div>
          <label for="edit_treatment_caption" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
          <textarea name="caption" id="edit_treatment_caption" required
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primaryLight transition"
            rows="4"></textarea>
        </div>
        <div class="flex justify-end space-x-2">
          <button type="button" onclick="closeEditForm()"
            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">Cancel</button>
          <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-primaryDark">Update
            Treatment</button>
        </div>
      </form>
    </div>
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