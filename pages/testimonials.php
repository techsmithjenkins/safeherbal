<?php
// testimonials.php - Testimonials page for FREDY HERBAL website
require_once "../config/db_connect.php"; // Database connection

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'], $_POST['message'])) {
    $name = trim($_POST['name']);
    $topic = trim($_POST['topic']);
    $message = trim($_POST['message']);
    $photoPath = null;

    if (!empty($_FILES['photo']['name'])) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir))
            mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES["photo"]["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
            $photoPath = "uploads/" . $fileName;
        }
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'], $_POST['message'])) {
        $name = trim($_POST['name']);
        $topic = trim($_POST['topic']);
        $message = trim($_POST['message']);
        $photoPath = null;

        // Check if photo was uploaded
        if (empty($_FILES['photo']['name'])) {
            die("Error: Photo is required.");
        }

        $targetDir = "../uploads/";
        if (!is_dir($targetDir))
            mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES["photo"]["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
            $photoPath = "uploads/" . $fileName;
        }
    }

    if (!empty($name) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO testimonials (name, topic, message, photo, is_approved) VALUES (?, ?, ?, ?, 0)");
        $stmt->bind_param("ssss", $name, $topic, $message, $photoPath);
        $stmt->execute();
        $stmt->close();

        echo "<script>
            document.addEventListener('DOMContentLoaded', () => {
                const msg = document.getElementById('submission-message');
                msg.classList.remove('hidden');
                setTimeout(() => msg.classList.add('hidden'), 5000);
            });
        </script>";
    }
}
?>

<section id="testimonials" class="py-20 bg-white">
    <div class="container mx-auto px-4 max-w-6xl">
        <h2 class="text-5xl font-bold mb-16 text-center text-primary animate-on-scroll">
            What Our Patients<br>Say About Our Services
        </h2>

        <!-- Testimonials Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            <?php
            $result = $conn->query("SELECT * FROM testimonials WHERE is_approved = 1 ORDER BY id DESC LIMIT 6");
            $totalTestimonials = $result ? $result->num_rows : 0;

            if ($totalTestimonials > 0) {
                while ($row = $result->fetch_assoc()) {
                    $name = htmlspecialchars($row['name']);
                    $topic = htmlspecialchars($row['topic'] ?? 'General');
                    $message = htmlspecialchars($row['message']);
                    $photo = !empty($row['photo']) ? "../" . $row['photo'] : "../uploads/default_avatar.png";
                    ?>
                    <div
                        class="group bg-primary/5 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 transform hover:-translate-y-1">
                        <div class="relative">
                            <div
                                class="absolute -top-2 -right-2 text-primary opacity-40 group-hover:opacity-80 transition-opacity duration-300">
                                <i class="fas fa-quote-right text-4xl"></i>
                            </div>
                        </div>
                        <div class="flex items-center mb-6">
                            <div class="relative">
                                <img src="<?php echo $photo; ?>" alt="<?php echo $name; ?>"
                                    class="w-16 h-16 rounded-full bg-primaryLight border-2 border-primary/20 object-cover group-hover:border-primary transition-colors duration-300">
                                <div
                                    class="absolute -bottom-1 -right-1 bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center">
                                    <i class="fas fa-check text-xs"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-xl font-bold text-gray-800"><?php echo $name; ?></h4>
                                <p class="text-primary font-medium"><span class="text-sm text-gray-500">Health Focus:
                                    </span><?php echo $topic; ?></p>
                            </div>
                        </div>
                        <div class="flex mb-4 text-yellow-400">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <p
                            class="text-gray-600 italic leading-relaxed line-clamp-4 group-hover:line-clamp-none transition-all duration-300">
                            "<?php echo $message; ?>"
                        </p>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="col-span-full text-center py-12">
                    <div class="bg-primaryLight/30 rounded-2xl p-8 max-w-lg mx-auto">
                        <i class="fas fa-comments text-5xl text-primary/40 mb-4"></i>
                        <p class="text-xl text-gray-600">No testimonials yet. Be the first to share your experience!</p>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>

    <!-- Testimonial Form -->
    <div class="max-w-3xl mx-auto">
        <div
            class="bg-white p-8 mx-4 md:mx-0 rounded-2xl bg-primary/5 shadow-xl animate-on-scroll relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 transform translate-x-16 -translate-y-16">
                <div class="absolute inset-0 bg-primary rounded-full"></div>
            </div>

            <h3 class="text-3xl font-serif font-bold mb-6 text-primary relative">
                <span class="block text-sm uppercase tracking-wider text-secondary font-sans mb-2">We Value Your
                    Feedback</span>
                Share Your Experience
            </h3>

            <form id="upload-testimonial" class="space-y-6 relative" method="post" enctype="multipart/form-data">
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Name</label>
                        <input type="text" name="name" required placeholder="Enter your full name"
                            class="w-full px-4 py-3 rounded-lg border-2 border-gray-100 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-200 outline-none" />
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Health Focus</label>
                        <input type="text" name="topic" required placeholder="e.g., Arthritis Relief"
                            class="w-full px-4 py-3 rounded-lg border-2 border-gray-100 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-200 outline-none" />
                    </div>
                </div>

                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Your Testimonial</label>
                    <textarea name="message" required rows="4"
                        placeholder="Share your experience with our treatments..."
                        class="w-full px-4 py-3 rounded-lg border-2 border-gray-100 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-200 outline-none resize-none"></textarea>
                </div>

                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Your Photo</label>
                    <div class="relative group">
                        <input type="file" name="photo" accept="image/*" id="photo-upload" class="hidden" required />
                        <label for="photo-upload"
                            class="flex items-center justify-center w-full px-4 py-3 rounded-lg border-2 border-dashed border-primary/30 bg-primary/5 hover:bg-primary/10 cursor-pointer transition-all duration-200 group-hover:border-primary">
                            <i class="fas fa-camera mr-2 text-primary"></i>
                            <span class="text-primary font-medium">Choose a Photo</span>
                        </label>
                        <div id="file-preview" class="mt-2 text-sm text-gray-500 hidden">
                            <i class="fas fa-image mr-1"></i>
                            <span class="file-name"></span>
                        </div>
                    </div>
                </div>

                <button type="submit"
                    class="inline-flex items-center justify-center w-full md:w-auto px-8 py-4 bg-primary hover:bg-primaryDark text-white rounded-full text-lg font-semibold transition-all duration-200 transform hover:scale-105 hover:shadow-lg group">
                    <span class="mr-2">Submit Testimonial</span>
                    <i class="fas fa-paper-plane transform group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

            <div id="submission-message"
                class="hidden mt-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-center animate-fade-in">
                <div class="flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-500 mr-2 text-xl"></i>
                    <p>Thank you for your submission! Your testimonial is awaiting approval.</p>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>

<script>
    // Add smooth reveal animation for testimonial cards
    document.addEventListener('DOMContentLoaded', () => {
        // Testimonial cards animation
        const cards = document.querySelectorAll('.grid > div');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease-out';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 * index);
        });

        // File input preview
        const fileInput = document.getElementById('photo-upload');
        const filePreview = document.getElementById('file-preview');
        const fileName = filePreview.querySelector('.file-name');

        fileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                fileName.textContent = file.name;
                filePreview.classList.remove('hidden');

                // Show file size
                const size = (file.size / 1024 / 1024).toFixed(2);
                fileName.textContent = `${file.name} (${size}MB)`;
            } else {
                filePreview.classList.add('hidden');
            }
        });
    });
</script>