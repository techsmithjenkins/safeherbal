<?php
// services.php - Treatment page for FREDY HERBAL website
?>
<section id="services" class="py-20 bg-gradient-to-b from-white to-green-50">
    <div class="container mx-auto px-4 max-w-6xl">
        <h2 class="text-4xl font-bold mb-16 text-center text-primary animate-on-scroll">The Treatment We Offer</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
            <?php
            $result = $conn->query("SELECT * FROM treatments ORDER BY created_at DESC, id DESC LIMIT 9");
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='bg-white p-8 rounded-2xl border border-green-100 shadow-md transform transition duration-300 hover:scale-105 animate-on-scroll'>";
                    echo "<div class='text-primary text-5xl mb-5'><i class='fas fa-heartbeat'></i></div>";
                    echo "<h3 class='text-2xl font-bold mb-3 text-primary'>" . htmlspecialchars($row['title']) . "</h3>";
                    
                    // Check if image exists and display it
                    if (!empty($row['image'])) {
                        echo "<img src='../Uploads/" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['title']) . "' class='w-full h-32 object-cover mb-2 rounded'>";
                    }
                    
                    // Display caption (which serves as excerpt)
                    echo "<p class='text-gray-700'>" . htmlspecialchars($row['caption']) . "</p>";
                    echo "</div>";
                }
            } else {
                // Display message when no treatments are available
                echo "<div class='col-span-full text-center py-12'>";
                echo "<div class='text-primary text-6xl mb-4'><i class='fas fa-leaf'></i></div>";
                echo "<h3 class='text-2xl font-bold mb-3 text-gray-600'>No Treatments Available</h3>";
                echo "<p class='text-gray-500'>Our treatment services will be available soon. Please check back later.</p>";
                echo "</div>";
            }
            ?>
        </div>
    </div>
</section>