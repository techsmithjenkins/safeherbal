<?php
include "../config/db_connect.php";
$result = $conn->query("SELECT * FROM articles WHERE tags LIKE '%testimonial%' AND is_approved = 1 ORDER BY date_added DESC LIMIT 3");
?>

<div class="testimonials-section w-full max-w-5xl mx-auto mt-12 p-6">
    <h2 class="text-3xl font-serif font-bold text-center text-[#16a34a] mb-8">What Our Users Say</h2>
    <div class="flex overflow-x-auto space-x-6 pb-4 hide-scrollbar">
        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<div class='testimonial-card w-full flex-shrink-0 px-6 py-8 max-w-full bg-white rounded-2xl shadow-lg'>";
            echo "<div class='flex items-center mb-6'>";
            echo "<div class='w-16 h-16 rounded-full bg-gray-200 border-2 border-dashed'></div>";
            echo "<div class='ml-4'>";
            echo "<h4 class='text-xl font-bold'>" . htmlspecialchars($row['author']) . "</h4>";
            echo "<p class='text-primary'>" . htmlspecialchars($row['tags']) . "</p>";
            echo "</div></div>";
            if ($row['image_url']) echo "<img src='../uploads/{$row['image_url']}' alt='" . htmlspecialchars($row['author']) . "' class='w-full h-32 object-cover mb-2 rounded'>";
            echo "<p class='text-gray-700 italic'>" . htmlspecialchars($row['excerpt']) . "</p>";
            echo "<div class='flex mt-4 space-x-1 text-yellow-400'>";
            echo "<i class='fas fa-star'></i><i class='fas fa-star'></i><i class='fas fa-star'></i><i class='fas fa-star'></i><i class='fas fa-star'></i>";
            echo "</div></div>";
        }
        $conn->close();
        ?>
    </div>
</div>