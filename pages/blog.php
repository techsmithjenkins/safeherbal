<?php
include "../config/db_connect.php";
$result = $conn->query("SELECT * FROM articles ORDER BY date_added DESC LIMIT 3"); // Example limit
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FREDY HERBAL | Blog/Articles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet" />
    <style>
        .herb-bg { background: radial-gradient(circle at center, #f0fdf4 0%, #dcfce7 70%, #bbf7d0 100%); }
        .leaf-decoration { position: absolute; width: 100px; height: 100px; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2316a34a' stroke-width='1' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z'/%3E%3Cpath d='M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12'/%3E%3C/svg%3E"); background-size: contain; background-repeat: no-repeat; opacity: 0.1; }
        #modalContent p {
            white-space: pre-wrap;
            word-break: break-word;
        }
    </style>
</head>
<body class="herb-bg min-h-screen p-6 relative">
    <div class="leaf-decoration top-10 left-10"></div>
    <div class="leaf-decoration bottom-10 right-10 rotate-45"></div>

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-xl z-10">
        <h2 class="text-3xl font-serif font-bold text-center text-[#16a34a] mb-6">Our Recent Updates</h2>
        <div class="space-y-6">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="p-6 border border-gray-200 rounded-2xl bg-[#dcfce7] shadow-md">
                    <h4 class="text-lg font-medium text-[#16a34a]"><?php echo htmlspecialchars($row['title']); ?></h4>
                    <?php if ($row['image_url']) echo "<img src='../uploads/{$row['image_url']}' alt='{$row['title']}' class='mt-2 max-w-xs rounded-lg'>"; ?>
                    <p class="text-gray-700 mt-2"><?php echo htmlspecialchars(substr($row['content'], 0, 100)) . "..."; ?></p>
                    <p class="text-sm text-gray-500 mt-1">Added: <?php echo $row['date_added']; ?></p>
                    <a href="#" class="text-[#a16207] hover:text-[#15803d] mt-2 inline-block read-more" data-id="<?php echo $row['id']; ?>">Read More</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Modal -->
    <div id="articleModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <h3 class="text-2xl font-serif font-bold text-[#16a34a] mb-4">Article</h3>
            <button id="closeModal" class="text-gray-500 hover:text-gray-700 absolute top-4 right-4">&times;</button>
            <div id="modalContent"></div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.read-more').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const articleId = this.getAttribute('data-id');
                fetch(`http://localhost/fredyherbal/api/get_article.php?id=${articleId}`)
                    .then(response => {
                        if (!response.ok) throw new Error(`Network response was not ok: ${response.status} ${response.statusText}`);
                        return response.text();
                    })
                    .then(html => {
                        document.getElementById('modalContent').innerHTML = html;
                        document.getElementById('articleModal').classList.remove('hidden');
                    })
                    .catch(error => {
                        document.getElementById('modalContent').innerHTML = `<p class='text-red-600'>Error loading article: ${error.message}</p>`;
                        document.getElementById('articleModal').classList.remove('hidden');
                    });
            });
        });

        document.getElementById('closeModal').addEventListener('click', () => {
            document.getElementById('articleModal').classList.add('hidden');
            document.getElementById('modalContent').innerHTML = '';
        });

        document.getElementById('articleModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('articleModal')) {
                document.getElementById('articleModal').classList.add('hidden');
                document.getElementById('modalContent').innerHTML = '';
            }
        });
    </script>
</body>
</html>