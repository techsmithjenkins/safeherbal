<?php
// about.php - About page for FREDY HERBAL website
?>

<section id="about" class="py-20 bg-white">
    <div class="container mx-auto px-4 max-w-6xl">
        <h2 class="text-4xl font-bold mb-16 text-center text-primary animate-on-scroll">About FREDY HERBAL</h2>
        <div class="flex flex-col lg:flex-row items-center gap-12">
            <div class="lg:w-1/2 animate-on-scroll">
                <?php

                // Query the image from the gallery table where caption is 'profile'
                $stmt = $conn->prepare("SELECT image_url FROM gallery WHERE caption = ? LIMIT 1");
                $stmt->bind_param("s", $caption);
                $caption = "about";
                $stmt->execute();
                $result = $stmt->get_result();
                $galleryImage = $result->fetch_assoc();
                $imageUrl = $galleryImage ? "../uploads/" . htmlspecialchars($galleryImage['image_url']) : "../assets/about.jpg";
                $stmt->close();
                // $conn->close(); // Connection will be closed at the end of the page
                ?>
                <img src="<?php echo $imageUrl; ?>" alt="Dr. Frederick" class="rounded-2xl shadow-xl">
            </div>
            <div class="lg:w-1/2 animate-on-scroll">
                <?php
                // Function to get About content from DB
                function getAboutContent($conn)
                {
                    $default = "Founded in 2013 as AGBENYEGA HERBAL CONCEPT, we've
        dedicated ourselves to harnessing nature's healing power to help people achieve optimal health. Our
        journey began in Ghana with traditional herbal knowledge passed down through generations.<br><br>
        Today, we combine this ancestral wisdom with modern research to create effective, natural solutions for a wide range of health concerns. Our
        commitment is to purity, potency, and your wellness journey.";

                    $stmt = $conn->prepare("SELECT content FROM site_content WHERE caption = 'about' LIMIT 1");
                    if ($stmt) {
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($row = $result->fetch_assoc()) {
                            return !empty($row["content"]) ? $row["content"] : $default;
                        }
                    }
                    return $default;
                }

                $aboutContent = getAboutContent($conn);
                ?>

                <div class="text-lg text-gray-700 mb-6 leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($aboutContent, ENT_QUOTES, 'UTF-8')); ?>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center">
                            <i class="fas fa-check text-white"></i>
                        </div>
                        <span class="text-lg">Traditional herbal knowledge combined with modern science</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center">
                            <i class="fas fa-check text-white"></i>
                        </div>
                        <span class="text-lg">Ethically sourced ingredients from sustainable farms</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center">
                            <i class="fas fa-check text-white"></i>
                        </div>
                        <span class="text-lg">Rigorous quality control and testing procedures</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center">
                            <i class="fas fa-check text-white"></i>
                        </div>
                        <span class="text-lg">Over 10 years of proven results and happy clients</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>