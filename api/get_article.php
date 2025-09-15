<?php
include "../config/db_connect.php";

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM articles WHERE id = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $article = $result->fetch_assoc();
    $stmt->close();

    if ($article) {
        echo "<h4 class='text-lg font-medium text-[#16a34a] mb-2'>" . htmlspecialchars($article['title']) . "</h4>";
        if ($article['image_url']) echo "<img src='/fredyherbal/uploads/{$article['image_url']}' alt='{$article['title']}' class='mt-2 max-w-xs rounded-lg mb-2'>";
        echo "<p class='text-gray-700'>" . nl2br(htmlspecialchars(str_replace("\r", "", $article['content']) ?: 'No content available')) . "</p>";
        echo "<p class='text-sm text-gray-500 mt-1'>By " . (htmlspecialchars($article['author']) ?: 'Unknown') . " on " . $article['date_added'] . "</p>";
    } else {
        echo "<p class='text-red-600'>Article not found.</p>";
    }
} else {
    echo "<p class='text-red-600'>Invalid article ID.</p>";
}
$conn->close();
?>



