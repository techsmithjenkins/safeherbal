<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../config/db_connect.php";

// Handle save or update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["content"]) && isset($_POST["section"])) {
    $section = $conn->real_escape_string($_POST["section"]);
    $caption = $conn->real_escape_string($_POST["caption"]);
    $content = $conn->real_escape_string($_POST["content"]);
    $excerpt = isset($_POST["excerpt"]) ? $conn->real_escape_string($_POST["excerpt"]) : null;
    $page = $conn->real_escape_string($_POST["page"]);
    $isActive = isset($_POST["is_active"]) ? 1 : 0;

    // Check if entry exists
    $checkSql = "SELECT id, is_active FROM content_pages WHERE page = ? AND section = ? AND caption = ? LIMIT 1";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("sss", $page, $section, $caption);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $existing = $result->fetch_assoc();
    $checkStmt->close();

    if ($existing) {
        $sql = "UPDATE content_pages SET content = ?, excerpt = ?, is_active = ?, last_updated = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $content, $excerpt, $isActive, $existing['id']);
    } else {
        $sql = "INSERT INTO content_pages (page, section, caption, content, excerpt, is_active) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $page, $section, $caption, $content, $excerpt, $isActive);
    }
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $success = $stmt->execute();
    $stmt->close();

    echo "<script>showToast('" . ($success ? 'Content saved successfully!' : 'Save failed. Please try again.') . "');</script>";
    exit();
}

// Handle delete (soft delete by setting is_active to 0)
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $sql = "UPDATE content_pages SET is_active = 0, last_updated = CURRENT_TIMESTAMP WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();
    echo "<script>showToast('" . ($success ? 'Content deactivated successfully!' : 'Deactivation failed. Please try again.') . "');</script>";
    exit();
}

// Fetch content from database
$showInactive = isset($_GET['show_inactive']) && $_GET['show_inactive'] == 1;
$whereClause = $showInactive ? "WHERE is_active IN (0, 1)" : "WHERE is_active = 1";
$result = $conn->query("SELECT * FROM content_pages $whereClause ORDER BY page, section, last_updated DESC");
$pageContent = [];
$latestEntries = [];
while ($row = $result->fetch_assoc()) {
    $key = $row['page'] . '|' . $row['section'] . '|' . $row['caption'];
    if (!isset($latestEntries[$key]) || $row['last_updated'] > $latestEntries[$key]['last_updated']) {
        $pageContent[$row['page']][$row['section']][$row['caption']] = $row;
        $latestEntries[$key] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FREDY HERBAL | Content Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="../assets/admin.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
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

    <div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl shadow-xl relative z-10">
        <h2 class="text-3xl font-serif font-bold text-center text-primary mb-6">
            Content Management
        </h2>

        <a href="index.php" class="inline-flex items-center text-secondary font-semibold hover:text-secondaryDark mb-6">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>

        <!-- Toggle to show/hide inactive content -->
        <a href="?show_inactive=1" class="text-secondary hover:text-secondaryDark mb-6 inline-block <?php echo isset($_GET['show_inactive']) && $_GET['show_inactive'] == 1 ? 'hidden' : ''; ?>">Show Inactive Content</a>
        <a href="?show_inactive=0" class="text-secondary hover:text-secondaryDark mb-6 inline-block <?php echo !isset($_GET['show_inactive']) || $_GET['show_inactive'] == 0 ? 'hidden' : ''; ?>">Hide Inactive Content</a>

        <?php
        // Default content for welcome.php (based on welcome.php)
        $welcomeSections = [
            'welcome_section' => [
                'page' => 'welcome',
                'fields' => [
                    'caption' => ['label' => 'Welcome Header', 'default' => 'Welcome!'],
                    'message' => ['label' => 'Welcome Intro', 'default' => 'This is the official website of Fredy Herbal. We are a Herbal Medicine Treatment Company registered in Ghana as AGBENYEGA HERBAL CONCEPT since 2013.'],
                    'excerpt' => ['label' => 'Welcome Slogan', 'default' => 'We target the root cause, go beyond the cure, and restore your body system.']
                ]
            ]
        ];

        // Content sections for homepage.php
        $homepageSections = [
            'founder_section' => [
                'page' => 'homepage',
                'fields' => [
                    'content' => ['label' => 'Founder Intro', 'default' => 'Meet Dr. Frederick, our visionary founder and lead herbal physician, blending centuries-old Ghanaian wisdom with modern science. 20+ years of clinical herbal practice. Published researcher in phytotherapy. Community healer across Ghanaian regions.'],
                    'excerpt' => ['label' => 'Founder Summary', 'default' => 'Summary of Dr. Frederick\'s expertise.']
                ]
            ],
            'traditional_section' => [
                'page' => 'homepage',
                'fields' => [
                    'content' => ['label' => 'Traditional Medicine', 'default' => 'Our formulas are based on centuries-old Ghanaian herbal traditions passed down through generations.'],
                    'excerpt' => ['label' => 'Traditional Summary', 'default' => 'Heritage-based herbal solutions.']
                ]
            ],
            'modern_section' => [
                'page' => 'homepage',
                'fields' => [
                    'content' => ['label' => 'Modern Validation', 'default' => 'We combine traditional knowledge with modern scientific research for proven effectiveness.'],
                    'excerpt' => ['label' => 'Modern Summary', 'default' => 'Science-backed herbal remedies.']
                ]
            ],
            'ingredients_section' => [
                'page' => 'homepage',
                'fields' => [
                    'content' => ['label' => 'Pure Ingredients', 'default' => 'We use only 100% natural, organic ingredients with no additives or preservatives.'],
                    'excerpt' => ['label' => 'Ingredients Summary', 'default' => 'Natural and pure ingredients.']
                ]
            ],
            'care_section' => [
                'page' => 'homepage',
                'fields' => [
                    'content' => ['label' => 'Personalized Care', 'default' => 'Each client receives a personalized treatment plan tailored to their specific health needs.'],
                    'excerpt' => ['label' => 'Care Summary', 'default' => 'Tailored health solutions.']
                ]
            ],
            'sourcing_section' => [
                'page' => 'homepage',
                'fields' => [
                    'content' => ['label' => 'Sustainable Sourcing', 'default' => 'We ethically source all ingredients with respect for nature and local communities.'],
                    'excerpt' => ['label' => 'Sourcing Summary', 'default' => 'Ethical and sustainable sourcing.']
                ]
            ],
            'results_section' => [
                'page' => 'homepage',
                'fields' => [
                    'content' => ['label' => 'Proven Results', 'default' => 'Over a decade of success stories and satisfied clients across West Africa.'],
                    'excerpt' => ['label' => 'Results Summary', 'default' => 'Decade-long success stories.']
                ]
            ]
        ];

        $allSections = array_merge($welcomeSections, $homepageSections);

        foreach ($allSections as $section => $details) {
            // Only display sections with active content unless inactive is shown
            if (!isset($pageContent[$details['page']][$section]) && !$showInactive) {
                continue;
            }
            echo '<div class="border border-gray-200 rounded-xl p-6 bg-primaryLight mb-6">';
            echo '<h3 class="text-2xl font-serif font-semibold text-primary mb-4 capitalize">' . htmlspecialchars($details['page']) . ' Page - ' . $section . '</h3>';
            echo '<div class="grid grid-cols-1 gap-4">';
            foreach ($details['fields'] as $caption => $field) {
                $entry = isset($pageContent[$details['page']][$section][$caption]) ? $pageContent[$details['page']][$section][$caption] : null;
                $defaultContent = $field['default'] ?? '';
                $displayContent = $entry ? $entry['content'] : ($showInactive && !$entry ? $defaultContent : '');
                echo '<form method="post" class="space-y-4">';
                echo '<label class="block text-sm font-medium text-gray-700">Edit ' . htmlspecialchars($field['label']) . '</label>';
                echo '<textarea name="content" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-primary focus:border-primary transition" placeholder="Enter new text here">' . htmlspecialchars($displayContent) . '</textarea>';
                echo '<textarea name="excerpt" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-primary focus:border-primary transition" placeholder="Enter excerpt (optional)">' . htmlspecialchars($entry['excerpt'] ?? '') . '</textarea>';
                echo '<label class="inline-flex items-center"><input type="checkbox" name="is_active" value="1" ' . ($entry && $entry['is_active'] ? 'checked' : '') . ' class="mr-2"> Active</label>';
                echo '<input type="hidden" name="section" value="' . htmlspecialchars($section) . '">';
                echo '<input type="hidden" name="page" value="' . htmlspecialchars($details['page']) . '">';
                echo '<input type="hidden" name="caption" value="' . htmlspecialchars($caption) . '">';
                echo '<button type="submit" class="w-full bg-primary text-white py-2 rounded-full text-md font-semibold hover:bg-primaryDark transition-transform transform hover:scale-105 shadow-lg">Save</button>';
                if ($entry) {
                    echo '<a href="?delete=1&id=' . htmlspecialchars($entry['id']) . '" class="text-red-600 hover:text-red-800 mt-2 inline-block" onclick="return confirm(\'Are you sure you want to deactivate this content?\');">Deactivate</a>';
                }
                echo '<p class="text-sm text-gray-500">Last Updated: ' . ($entry ? date('Y-m-d H:i', strtotime($entry['last_updated'])) : 'N/A') . '</p>';
                echo '</form>';
            }
            echo '</div>';
            echo '</div>';
        }
        ?>

    </div>
</body>

<script>
    // Toast Notification
    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-6 right-6 bg-green-500 text-white p-4 rounded-lg shadow-lg transition-opacity duration-300';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.add('opacity-0'), 3000);
        setTimeout(() => toast.remove(), 3300);
    }
</script>
</html>