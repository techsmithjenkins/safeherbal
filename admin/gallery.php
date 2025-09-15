<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

include "../config/db_connect.php";
include "../includes/functions.php"; // Assumes uploadImage function

// Handle image upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['type']) && $_POST['type'] == 'add') {
    $caption = filter_input(INPUT_POST, "caption", FILTER_SANITIZE_STRING);
    $error = null;

    if (isset($_FILES["gallery_image"]) && $_FILES["gallery_image"]['error'] == 0) {
        $imageName = uploadImage($_FILES["gallery_image"]);
        if ($imageName) {
            $date_added = date("Y-m-d");
            $sql = "INSERT INTO gallery (image_url, caption, date_added) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("sss", $imageName, $caption, $date_added);
                $stmt->execute();
                $stmt->close();
            } else {
                // Fallback if date_added column missing
                $sql_fallback = "INSERT INTO gallery (image_url, caption) VALUES (?, ?)";
                $stmt = $conn->prepare($sql_fallback);
                $stmt->bind_param("ss", $imageName, $caption);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            $error = "Failed to upload image. Please try again.";
        }
    } else {
        $error = "No image selected or upload error.";
    }
}

// Handle image deletion
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("SELECT image_url FROM gallery WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $image_path = '../Uploads/' . $row['image_url'];
            if (file_exists($image_path)) {
                unlink($image_path); // Delete physical file
            }
        }
        $stmt->close();
    }

    $stmt = $conn->prepare("DELETE FROM gallery WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: gallery.php");
    exit;
}

// Handle caption edit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['type']) && $_POST['type'] == 'edit') {
    $id = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);
    $caption = filter_input(INPUT_POST, "caption", FILTER_SANITIZE_STRING);
    $sql = "UPDATE gallery SET caption = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("si", $caption, $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: gallery.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FREDY HERBAL | Gallery Management</title>
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
        <h2 class="text-3xl font-serif font-bold text-center text-primary mb-6">Gallery Management</h2>
        <a href="index.php" class="inline-flex items-center text-secondary font-semibold hover:text-secondaryDark mb-6">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>

        <?php if (isset($error)) { ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($error); ?></p>
        <?php } ?>

        <h3 class="text-xl font-serif font-semibold text-primary mt-6 mb-4">Add New Image</h3>
        <form method="post" enctype="multipart/form-data" class="space-y-5 mb-8">
            <input type="hidden" name="type" value="add">
            <div>
                <label for="gallery_image" class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                <div class="w-full border-2 border-gray-300 border-dashed rounded-lg p-6 text-center bg-gray-50 hover:bg-green-50 transition-colors relative overflow-hidden"
                    id="drop-zone">
                    <label for="gallery_image" class="cursor-pointer flex flex-col items-center space-y-2">
                        <svg class="w-12 h-12 text-gray-400 group-hover:text-green-600 transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16V10a5 5 0 015-5v0a5 5 0 015 5v6m-2 0h4m-6 0h-4m6 0v4m-6 0v-4m6-10v10m-6 0v-4">
                            </path>
                        </svg>
                        <p class="text-gray-700 font-medium" id="file-text">Drag and drop an image here or click to
                            select</p>
                        <p class="text-sm text-gray-500">(Only image files are allowed)</p>
                        <input type="file" name="gallery_image" id="gallery_image" accept="image/*" required
                            class="hidden" />
                    </label>
                    <div class="absolute bottom-0 left-0 w-full h-2 bg-gray-200 rounded-full overflow-hidden hidden"
                        id="progress-wrapper">
                        <div class="h-full bg-green-500 w-0 transition-all duration-300" id="progress-bar"></div>
                    </div>
                </div>
            </div>
            <div>
                <label for="caption" class="block text-sm font-medium text-gray-700 mb-1">Caption</label>
                <textarea name="caption" id="caption" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primaryLight transition"
                    placeholder="Enter image caption" rows="4"></textarea>
                <p class="text-sm text-gray-500 mt-1">
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mt-3 text-sm">
                    <h3 class="text-gray-600 font-medium mb-2 text-sm">Special Caption Guidelines:</h3>
                    <p class="text-gray-600 mb-3 text-sm">Use these captions to assign images to specific sections:</p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center">
                            <span class="font-mono bg-gray-100 px-2 py-0.5 rounded text-sm text-primary font-semibold">profile</span>
                            <span class="ml-2">→ Sets image on profile page <span class="text-gray-500">ie: Meet Dr Frederick...</span>
                        </li>
                        <li class="flex items-center">
                            <span class="font-mono bg-gray-100 px-2 py-0.5 rounded text-sm text-primary font-semibold">welcome</span>
                            <span class="ml-2">→ Sets image on Welcome page <span class="text-gray-500">ie:Welcome to the official page of...</span>
                        </li>
                        <li class="flex items-center">
                            <span class="font-mono bg-gray-100 px-2 py-0.5 rounded text-sm text-primary font-semibold">about</span>
                            <span class="ml-2">→ Sets image on About page <span class="text-gray-500">ie: About Fredy Herbal...</span>
                        </li>
                    </ul>
                    <p class="text-xs text-gray-400 mt-3 italic">
                        Note: For each special caption, only the first uploaded image will be used
                    </p>
                </div>
                </p>
            </div>
            <button type="submit"
                class="w-full bg-primary text-white py-3 rounded-full text-lg font-semibold hover:bg-primaryDark transition-transform transform hover:scale-105 shadow-lg">Submit</button>
        </form>

        <h3 class="text-xl font-serif font-semibold text-primary mt-8 mb-4">Existing Gallery</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            $result = $conn->query("SELECT * FROM gallery ORDER BY id DESC"); // Fixed: Use id DESC for chronological order without date_added dependency
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='p-6 border border-gray-200 rounded-2xl bg-primaryLight shadow-md'>";
                    echo "<img src='../Uploads/{$row['image_url']}' alt='" . htmlspecialchars($row['caption']) . "' class='w-full h-48 object-cover rounded-lg mb-2'>";
                    echo "<p class='text-gray-700'>" . htmlspecialchars($row['caption']) . "</p>";
                    $date_display = isset($row['date_added']) ? $row['date_added'] : 'No date available';
                    echo "<p class='text-sm text-gray-500 mt-1'>Added: " . htmlspecialchars($date_display) . "</p>";
                    echo "<button onclick=\"openEditForm({$row['id']}, '" . htmlspecialchars($row['caption'], ENT_QUOTES) . "')\" class='text-blue-600 hover:text-blue-800 mt-2 inline-block mr-2'>Edit Caption</button>";
                    echo "<a href='?delete_id={$row['id']}' class='text-red-600 hover:text-red-800 mt-2 inline-block' onclick=\"return confirm('Are you sure you want to delete this image?');\">Delete</a>";
                    echo "</div>";
                }
            } else {
                echo "<p class='text-gray-600 col-span-full'>No gallery images available.</p>";
            }
            $conn->close();
            ?>
        </div>

        <!-- Edit Caption Form (Hidden Modal) -->
        <div id="edit_caption_form"
            class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full mx-4">
                <h3 class="text-xl font-serif font-semibold text-primary mb-4">Edit Caption</h3>
                <form method="post" class="space-y-5">
                    <input type="hidden" name="type" value="edit">
                    <input type="hidden" name="id" id="edit_caption_id">
                    <div>
                        <label for="edit_caption" class="block text-sm font-medium text-gray-700 mb-1">Caption</label>
                        <textarea name="caption" id="edit_caption" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-primary focus:ring-2 focus:ring-primaryLight transition"
                            rows="4"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeEditForm()"
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">Cancel</button>
                        <button type="submit"
                            class="bg-primary text-white px-4 py-2 rounded-md hover:bg-primaryDark">Update
                            Caption</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confetti Library -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
    <script>
        // Confetti function for successful upload simulation
        function launchConfetti() {
            const duration = 2000;
            const end = Date.now() + duration;
            (function frame() {
                const particleCount = 5;
                confetti({
                    particleCount,
                    angle: 60,
                    spread: 55,
                    origin: { x: 0 },
                    colors: ['#16a34a', '#22c55e', '#86efac']
                });
                confetti({
                    particleCount,
                    angle: 120,
                    spread: 55,
                    origin: { x: 1 },
                    colors: ['#16a34a', '#22c55e', '#86efac']
                });
                if (Date.now() < end) {
                    requestAnimationFrame(frame);
                }
            })();
        }

        // Drag & Drop and Preview JavaScript
        document.addEventListener('DOMContentLoaded', function () {
            const dropZone = document.querySelector('#drop-zone');
            const input = document.querySelector('#gallery_image');
            const fileText = document.querySelector('#file-text');
            const progressWrapper = document.querySelector('#progress-wrapper');
            const progressBar = document.querySelector('#progress-bar');

            if (dropZone && input && fileText && progressWrapper && progressBar) {
                dropZone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropZone.classList.add('border-green-500', 'bg-green-50');
                });

                dropZone.addEventListener('dragleave', (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('border-green-500', 'bg-green-50');
                });

                dropZone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('border-green-500', 'bg-green-50');
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        input.files = files;
                        handleFile(files[0]);
                    }
                });

                input.addEventListener('change', (e) => {
                    if (input.files.length > 0) {
                        handleFile(input.files[0]);
                    }
                });

                function handleFile(file) {
                    if (!file.type.startsWith("image/")) {
                        fileText.textContent = "Only image files are allowed!";
                        fileText.classList.add("text-red-500");
                        return;
                    }
                    fileText.textContent = file.name;
                    progressWrapper.classList.remove("hidden");
                    progressBar.style.width = "0%";
                    let progress = 0;
                    const interval = setInterval(() => {
                        progress += 10;
                        progressBar.style.width = progress + "%";
                        if (progress >= 100) {
                            clearInterval(interval);
                            setTimeout(() => {
                                progressWrapper.classList.add("hidden");
                                launchConfetti();
                            }, 300);
                        }
                    }, 200);
                }
            }
        });

        // Edit Modal Functions
        function openEditForm(id, caption) {
            document.getElementById('edit_caption_id').value = id;
            document.getElementById('edit_caption').value = caption;
            document.getElementById('edit_caption_form').classList.remove('hidden');
        }

        function closeEditForm() {
            document.getElementById('edit_caption_form').classList.add('hidden');
        }
    </script>
</body>

</html>