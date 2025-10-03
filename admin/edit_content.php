<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../config/db_connect.php";

// Map pages to default captions
$captionMap = [
    "welcome" => "welcome",
    "about" => "about",
    "safe_cards" => "cards",
    "profile" => "profile"
];

// Pagination setup
$limit = 10;
$page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$offset = ($page - 1) * $limit;

// Handle delete (hard delete)
if (isset($_GET['delete'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "DELETE FROM site_content WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: ?page=" . $_GET['page']);
    exit();
}

// Handle save (insert or update)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["content"], $_POST["caption"], $_POST["page"])) {
    $page = $conn->real_escape_string($_POST["page"]);
    $caption = $conn->real_escape_string($_POST["caption"]);
    $header = $conn->real_escape_string($_POST["header"]);
    $content = $conn->real_escape_string($_POST["content"]);
    $excerpt = $conn->real_escape_string($_POST["excerpt"] ?? '');
    $edit_id = isset($_POST["edit_id"]) ? (int)$_POST["edit_id"] : 0;
    
    if (empty($header) || empty($content)) {
        die("Header and content are required.");
    }
    // Temporarily disable is_active until column is added
    $isActive = 1; // Default to active until DB supports it

    // Check if record exists for this page/caption to decide insert or update
    $checkStmt = $conn->prepare("SELECT id FROM site_content WHERE page = ? AND caption = ? LIMIT 1");
    $checkStmt->bind_param("ss", $page, $caption);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $existingId = $checkResult->fetch_assoc();

    if ($existingId && $edit_id == 0) {
        $edit_id = $existingId['id']; // Use existing ID for update
    }

    if ($edit_id > 0) {
        // Update existing content
        $sql = "UPDATE site_content SET header = ?, content = ?, excerpt = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $header, $content, $excerpt, $edit_id);
    } else {
        // Insert new content (should be unique due to constraint)
        $sql = "INSERT INTO site_content (page, caption, header, content, excerpt) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $page, $caption, $header, $content, $excerpt);
    }
    $stmt->execute();
    $stmt->close();
    $checkStmt->close();
    header("Location: ?page=" . $page);
    exit();
}

// Fetch content for editing if edit_id is set
$editContent = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $editStmt = $conn->prepare("SELECT * FROM site_content WHERE id = ? LIMIT 1");
    $editStmt->bind_param("i", $edit_id);
    $editStmt->execute();
    $editResult = $editStmt->get_result();
    $editContent = $editResult->fetch_assoc();
    $editStmt->close();
}

// Fetch content with pagination (temporarily remove is_active filter)
$selectedPage = isset($_GET['page']) ? $conn->real_escape_string($_GET['page']) : 'welcome';
$stmt = $conn->prepare("SELECT * FROM site_content WHERE page = ? ORDER BY id DESC LIMIT ? OFFSET ?");
$stmt->bind_param("sii", $selectedPage, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
$contentList = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Count total items for pagination (temporarily remove is_active filter)
$totalStmt = $conn->prepare("SELECT COUNT(*) as total FROM site_content WHERE page = ?");
$totalStmt->bind_param("s", $selectedPage);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);
$totalStmt->close();

$conn->close();

// Format page title
$formattedPage = ucwords(str_replace("_", " ", $selectedPage));
$autoCaption = $captionMap[$selectedPage] ?? strtolower($selectedPage);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Content | Fredy Herbal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="../assets/admin.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#16a34a',
                        primaryLight: '#dcfce7',
                        primaryDark: '#15803d',
                        secondary: '#a16207',
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .herb-bg {
            background: radial-gradient(circle at center, #f0fdf4 0%, #dcfce7 70%, #bbf7d0 100%);
        }

        .leaf-decoration {
            position: absolute;
            width: 100px;
            height: 100px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%2316a34a' stroke-width='1.2' stroke-linecap='round' stroke-linejoin='round' viewBox='0 0 24 24'%3E%3Cpath d='M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z'/%3E%3Cpath d='M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12'/%3E%3C/svg%3E");
            background-size: contain;
            background-repeat: no-repeat;
            opacity: 0.08;
        }
    </style>
</head>

<body class="herb-bg min-h-screen relative p-4 md:p-8 font-sans">
    <div class="leaf-decoration top-10 left-10"></div>
    <div class="leaf-decoration bottom-10 right-10 rotate-45"></div>

    <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-xl p-6 md:p-10 relative z-10">
        <h1 class="text-3xl font-serif text-primary text-center mb-6">Content Management</h1>
        <div class="flex flex-col md:flex-row gap-6">

            <!-- Sidebar -->
            <aside class="w-full md:w-1/4 bg-primaryLight rounded-xl p-5 shadow-md">
                <h3 class="text-xl font-semibold text-primary mb-4">Pages</h3>
                <ul class="space-y-2">
                    <li><a href="?page=welcome" class="block px-3 py-2 rounded-md <?php echo $selectedPage == 'welcome' ? 'bg-primary text-white' : 'hover:bg-green-100 text-gray-700'; ?>">Welcome</a></li>
                    <li><a href="?page=profile" class="block px-3 py-2 rounded-md <?php echo $selectedPage == 'profile' ? 'bg-primary text-white' : 'hover:bg-green-100 text-gray-700'; ?>">Profile</a></li> 
                    <li><a href="?page=about" class="block px-3 py-2 rounded-md <?php echo $selectedPage == 'about' ? 'bg-primary text-white' : 'hover:bg-green-100 text-gray-700'; ?>">About Us</a></li>
                    <li><a href="?page=safe_cards" class="block px-3 py-2 rounded-md <?php echo $selectedPage == 'safe_cards' ? 'bg-primary text-white' : 'hover:bg-green-100 text-gray-700'; ?>">Safe & Efficient Cards</a></li>
                    <li class="pt-8">
                        <a href="index.php" class="inline-flex items-center text-secondary font-semibold hover:text-secondaryDark">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                        </a>
                    </li>
                </ul>
            </aside>

            <!-- Main Content -->
            <main class="flex-1">
                <h2 class="text-xl font-serif text-primary mb-4 flex items-center">
                    <i class="fas fa-edit text-secondary mr-2"></i>
                    <?php echo $editContent ? 'Update' : 'Edit'; ?> <?php echo $formattedPage; ?> Page
                </h2>

                <!-- Edit Form -->
                <form method="post" class="bg-green-50 rounded-xl p-6 shadow-md space-y-4">
                    <input type="hidden" name="page" value="<?php echo $selectedPage; ?>">
                    <?php if ($editContent): ?>
                        <input type="hidden" name="edit_id" value="<?php echo $editContent['id']; ?>">
                    <?php endif; ?>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Caption (Auto)</label>
                        <input type="text" name="caption" value="<?php echo $editContent ? htmlspecialchars($editContent['caption']) : $autoCaption; ?>" readonly
                            class="w-full border rounded-lg px-4 py-2 bg-gray-100 text-gray-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Header</label>
                        <input type="text" name="header" placeholder="Enter header title" value="<?php echo $editContent ? htmlspecialchars($editContent['header']) : ''; ?>"
                            class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primaryLight">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Content</label>
                        <textarea name="content" rows="4"
                            class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primaryLight"
                            placeholder="Enter content here"><?php echo $editContent ? htmlspecialchars($editContent['content']) : ''; ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Excerpt</label>
                        <textarea name="excerpt" rows="2"
                            class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primaryLight"
                            placeholder="Enter short excerpt here"><?php echo $editContent ? htmlspecialchars($editContent['excerpt']) : ''; ?></textarea>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-primary text-white py-3 rounded-lg font-semibold hover:bg-primaryDark transition">
                            <i class="fas fa-save mr-2"></i> <?php echo $editContent ? 'Update' : 'Save'; ?> Content
                        </button>
                        <?php if ($editContent): ?>
                            <a href="?page=<?php echo $selectedPage; ?>" class="px-6 bg-gray-400 text-white py-3 rounded-lg font-semibold hover:bg-gray-500 transition inline-flex items-center justify-center">
                                Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </form>

                <!-- Existing Content -->
                <h3 class="text-xl font-semibold text-primary mt-8 mb-4">Current <?php echo $formattedPage; ?> Content</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <?php if ($contentList): ?>
                        <?php foreach ($contentList as $item): ?>
                            <div class="p-4 bg-white border rounded-xl shadow-sm">
                                <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($item['caption']); ?></h4>
                                <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($item['header']); ?></p>
                                <p class="text-gray-600 mt-2 text-sm">
                                    <?php echo htmlspecialchars(substr($item['content'], 0, 80)) . (strlen($item['content']) > 80 ? '...' : ''); ?>
                                </p>
                                <p class="text-gray-600 mt-1 text-sm">
                                    Excerpt: <?php echo htmlspecialchars($item['excerpt'] ?? 'N/A'); ?>
                                </p>
                                <div class="mt-3 flex gap-2">
                                    <a href="?page=<?php echo $selectedPage; ?>&edit_id=<?php echo $item['id']; ?>"
                                        class="inline-block text-blue-600 hover:text-blue-800 text-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?delete=1&id=<?php echo $item['id']; ?>&page=<?php echo $selectedPage; ?>"
                                        class="inline-block text-red-600 hover:text-red-800 text-sm"
                                        onclick="return confirm('Delete this content permanently?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500">No content available for this page.</p>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="mt-4 flex justify-center space-x-2">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $selectedPage; ?>&page_num=<?php echo $page - 1; ?>" class="px-3 py-1 bg-primary text-white rounded-md hover:bg-primaryDark">Prev</a>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $selectedPage; ?>&page_num=<?php echo $i; ?>" class="px-3 py-1 <?php echo $i == $page ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700'; ?> rounded-md"><?php echo $i; ?></a>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $selectedPage; ?>&page_num=<?php echo $page + 1; ?>" class="px-3 py-1 bg-primary text-white rounded-md hover:bg-primaryDark">Next</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
</body>

</html>