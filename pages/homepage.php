<?php
$pageTitle = "Home";
include "../includes/header.php";
include "../config/db_connect.php";
?>

<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#16a34a',
                        primaryLight: '#dcfce7',
                        primaryDark: '#15803d',
                        secondary: '#a16207',
                        deepBlue: '#01017f'
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'fadeIn': 'fadeIn 1s ease-out forwards',
                        'slideInLeft': 'slideInLeft 0.8s ease-out forwards',
                        'slideInRight': 'slideInRight 0.8s ease-out forwards',
                        'pulse': 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite'
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' }
                        },
                        fadeIn: {
                            '0%': { opacity: 0 },
                            '100%': { opacity: 1 }
                        },
                        slideInLeft: {
                            '0%': { opacity: 0, transform: 'translateX(-50px)' },
                            '100%': { opacity: 1, transform: 'translateX(0)' }
                        },
                        slideInRight: {
                            '0%': { opacity: 0, transform: 'translateX(50px)' },
                            '100%': { opacity: 1, transform: 'translateX(0)' }
                        },
                        pulse: {
                            '0%, 100%': { opacity: 1 },
                            '50%': { opacity: 0.8 }
                        }
                    }
                }
            }
        };
    </script>
    <style>
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .animate-on-scroll.animated {
            opacity: 1;
            transform: translateY(0);
        }

        .floating-icon {
            position: absolute;
            z-index: 3;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            box-shadow: 0 5px 5px 0px rgba(1, 145, 13, 0.30);
            animation: float 6s ease-in-out infinite;
        }

        .herb-bg {
            background: radial-gradient(circle at center, #f0fdf4 0%, #dcfce7 70%, #bbf7d0 100%);
        }

        .contact-input {
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
        }

        .contact-input:focus {
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.2);
        }

        .leaf-decoration {
            position: absolute;
            width: 150px;
            height: 150px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2316a34a' stroke-width='1' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z'/%3E%3Cpath d='M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12'/%3E%3C/svg%3E");
            background-size: contain;
            background-repeat: no-repeat;
            opacity: 0.1;
            z-index: 0;
        }

        .section-divider {
            position: relative;
            height: 100px;
            overflow: hidden;
        }

        .section-divider svg {
            display: block;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100px;
        }

        .section-divider svg path {
            animation: wave 4s ease-in-out infinite;
            transform-origin: 50% 100%;
        }

        .section-divider svg path:nth-child(1) {
            animation-delay: 0s;
            animation-duration: 7s;
        }

        .section-divider svg path:nth-child(2) {
            animation-delay: 2s;
            animation-duration: 5s;
        }

        .section-divider svg path:nth-child(3) {
            animation-delay: 4s;
            animation-duration: 6s;
        }

        @keyframes wave {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            25% {
                transform: translateY(-8px) rotate(-1deg);
            }

            50% {
                transform: translateY(0) rotate(0deg);
            }

            75% {
                transform: translateY(8px) rotate(1deg);
            }
        }

        .testimonial-card {
            opacity: 0;
            transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
        }

        .testimonial-card.active {
            opacity: 1;
            transform: translateX(0) scale(1);
            z-index: 3;
        }

        .testimonial-card.inactive-left {
            opacity: 0.7;
            transform: translateX(-50%) scale(0.9);
            z-index: 2;
        }

        .testimonial-card.inactive-right {
            opacity: 0.7;
            transform: translateX(50%) scale(0.9);
            z-index: 2;
        }

        .testimonial-card.next-up {
            opacity: 0.4;
            transform: translateX(-70%) scale(0.8);
            z-index: 1;
        }

        .testimonial-card.next-down {
            opacity: 0.4;
            transform: translateX(70%) scale(0.8);
            z-index: 1;
        }

        #testimonials-wrapper {
            width: 300%;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Scroll animations
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animated');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                observer.observe(el);
            });

            // Testimonial carousel
            const testimonials = Array.from(document.querySelectorAll('.testimonial-card'));
            let currentActive = 0;
            let autoRotate = true;

            function rotateTestimonials() {
                testimonials.forEach((card, index) => {
                    card.classList.remove('active', 'inactive-left', 'inactive-right', 'next-up', 'next-down');
                    if (index === currentActive) {
                        card.classList.add('active');
                    } else if (index === (currentActive + 1) % testimonials.length) {
                        card.classList.add('inactive-right');
                    } else if (index === (currentActive - 1 + testimonials.length) % testimonials.length) {
                        card.classList.add('inactive-left');
                    } else if (index === (currentActive + 2) % testimonials.length) {
                        card.classList.add('next-up');
                    } else {
                        card.classList.add('next-down');
                    }
                });
                currentActive = (currentActive + 1) % testimonials.length;
            }

            let rotationInterval = setInterval(rotateTestimonials, 8000);
            document.getElementById('testimonial-carousel').addEventListener('mouseenter', () => {
                autoRotate = false;
                clearInterval(rotationInterval);
            });
            document.getElementById('testimonial-carousel').addEventListener('mouseleave', () => {
                autoRotate = true;
                rotationInterval = setInterval(rotateTestimonials, 8000);
            });
            rotateTestimonials();

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        window.scrollTo({
                            top: target.offsetTop - 80,
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Form submission
            document.getElementById('upload-testimonial').addEventListener('submit', function (e) {
                e.preventDefault();
                this.reset();
                const msg = document.getElementById('submission-message');
                msg.classList.remove('hidden');
                msg.style.opacity = '1';
                setTimeout(() => {
                    msg.style.opacity = '0';
                    setTimeout(() => msg.classList.add('hidden'), 500);
                }, 4000);
            });
        });
    </script>
</head>

<section id="founder" class="herb-bg min-h-screen flex items-center justify-center px-4 relative overflow-hidden">
    <div class="leaf-decoration top-12 left-8"></div>
    <div class="leaf-decoration bottom-12 right-8 rotate-45"></div>
    <div class="container mx-auto max-w-6xl flex flex-col lg:flex-row items-center z-10">
        <?php
        // Function to get profile content from DB
        function getProfileContent($conn)
        {
            $default = [
                "header" => "Meet Dr. Frederick",
                "content" => "Our visionary founder and lead herbal physician, blending centuries-old Ghanaian wisdom with modern science."
            ];

            $stmt = $conn->prepare("SELECT header, content FROM site_content WHERE caption = 'profile' LIMIT 1");
            if ($stmt) {
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    return [
                        "header" => !empty($row["header"]) ? $row["header"] : $default["header"],
                        "content" => !empty($row["content"]) ? $row["content"] : $default["content"]
                    ];
                }
            }
            return $default;
        }

        $profileData = getProfileContent($conn);
        ?>

        <div class="lg:w-1/2 pr-0 lg:pr-12 text-center lg:text-left animate-on-scroll" style="animation-delay: 0.3s">
            <h2 class="text-5xl md:text-6xl font-bold mb-4 text-primary">
                <?php echo htmlspecialchars($profileData["header"], ENT_QUOTES, 'UTF-8'); ?>
            </h2>
            <p class="text-xl md:text-2xl mb-6 leading-relaxed text-gray-700">
                <?php echo htmlspecialchars($profileData["content"], ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <ul class="space-y-4 text-gray-700 mb-8">
                <li class="flex items-start">
                    <i class="fas fa-seedling text-primary text-2xl mr-3"></i>
                    <span><strong>20+ years</strong> of clinical herbal practice</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-flask text-primary text-2xl mr-3"></i>
                    <span>Published researcher in phytotherapy</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-globe-africa text-primary text-2xl mr-3"></i>
                    <span>Community healer across Ghanaian regions</span>
                </li>
            </ul>
        </div>

        <div class="lg:w-1/2 mt-12 lg:mt-0 lg:p-6 relative animate-on-scroll" style="animation-delay: 0.6s">
            <?php

            // Query the image from the gallery table where caption is 'profile'
            $stmt = $conn->prepare("SELECT image_url FROM gallery WHERE caption = ? LIMIT 1");
            $stmt->bind_param("s", $caption);
            $caption = "profile";
            $stmt->execute();
            $result = $stmt->get_result();
            $galleryImage = $result->fetch_assoc();
            $imageUrl = $galleryImage ? "../uploads/" . htmlspecialchars($galleryImage['image_url']) : "../assets/profile.jpg";
            $stmt->close();
            // $conn->close(); // Connection will be closed at the end of the page
            ?>
            <img src="<?php echo $imageUrl; ?>" alt="Dr. Frederick"
                class="w-full h-auto max-w-sm md:max-w-md lg:max-w-lg xl:max-w-xl mx-auto rounded-xl shadow-2xl border-4 border-white" />
            <div class="floating-icon" style="top: -10px; right: -10px; animation-delay: 1s">
                <i class="fas fa-stethoscope text-3xl text-primary"></i>
            </div>
        </div>
    </div>
</section>

<div class="section-divider">
    <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
        <path
            d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z"
            opacity=".25" class="fill-current text-primary"></path>
        <path
            d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z"
            opacity=".5" class="fill-current text-primary"></path>
        <path
            d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z"
            class="fill-current text-primary"></path>
    </svg>
</div>


<?php
include "../pages/about.php";
?>



<div class="section-divider">
    <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
        <path
            d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z"
            opacity=".25" class="fill-current text-primary"></path>
        <path
            d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z"
            opacity=".5" class="fill-current text-primary"></path>
        <path
            d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z"
            class="fill-current text-primary"></path>
    </svg>
</div>

<?php
include "../pages/services.php";
?>


<section id="edge" class="pb-16 bg-primaryLight">

    <div class="section-divider">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path
                d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z"
                opacity=".25" class="fill-current text-primary"></path>
            <path
                d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z"
                opacity=".5" class="fill-current text-primary"></path>
            <path
                d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z"
                class="fill-current text-primary"></path>
        </svg>
    </div>

    <div class="container mx-auto px-4 max-w-6xl">
        <h2 class="text-4xl font-bold mb-16 text-center text-primary animate-on-scroll">Safe & Efficient</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-2xl shadow-md animate-on-scroll">
                <div class="text-primary text-4xl mb-4"><i class="fas fa-tree"></i></div>
                <h3 class="text-xl font-bold mb-2 text-primary">Traditional Medicine</h3>
                <p class="text-gray-700">Our formulas are based on centuries-old Ghanaian herbal traditions passed down
                    through generations.</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-md animate-on-scroll">
                <div class="text-primary text-4xl mb-4"><i class="fas fa-flask"></i></div>
                <h3 class="text-xl font-bold mb-2 text-primary">Modern Validation</h3>
                <p class="text-gray-700">We combine traditional knowledge with modern scientific research for proven
                    effectiveness.</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-md animate-on-scroll">
                <div class="text-primary text-4xl mb-4"><i class="fas fa-leaf"></i></div>
                <h3 class="text-xl font-bold mb-2 text-primary">Pure Ingredients</h3>
                <p class="text-gray-700">We use only 100% natural, organic ingredients with no additives or
                    preservatives.</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-md animate-on-scroll">
                <div class="text-primary text-4xl mb-4"><i class="fas fa-users"></i></div>
                <h3 class="text-xl font-bold mb-2 text-primary">Personalized Care</h3>
                <p class="text-gray-700">Each client receives a personalized treatment plan tailored to their specific
                    health needs.</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-md animate-on-scroll">
                <div class="text-primary text-4xl mb-4"><i class="fas fa-globe-africa"></i></div>
                <h3 class="text-xl font-bold mb-2 text-primary">Sustainable Sourcing</h3>
                <p class="text-gray-700">We ethically source all ingredients with respect for nature and local
                    communities.</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-md animate-on-scroll">
                <div class="text-primary text-4xl mb-4"><i class="fas fa-award"></i></div>
                <h3 class="text-xl font-bold mb-2 text-primary">Proven Results</h3>
                <p class="text-gray-700">Over a decade of success stories and satisfied clients across West Africa.</p>
            </div>
        </div>
    </div>
</section>

<div class="section-divider">
    <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
        <path
            d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z"
            opacity=".25" class="fill-current text-primary"></path>
        <path
            d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z"
            opacity=".5" class="fill-current text-primary"></path>
        <path
            d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z"
            class="fill-current text-primary"></path>
    </svg>
</div>

<?php
include "../pages/testimonials.php";
?>



<section id="updates" class="pb-16 bg-primaryLight">


    <div class="section-divider">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path
                d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z"
                opacity=".25" class="fill-current text-primary"></path>
            <path
                d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z"
                opacity=".5" class="fill-current text-primary"></path>
            <path
                d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z"
                class="fill-current text-primary"></path>
        </svg>
    </div>

    <div class="container mx-auto px-4 max-w-6xl"></div>
    <h2 class="text-4xl font-bold text-center text-primary mb-4 animate-on-scroll">Our Recent Updates</h2>
    <h3 class="text-xl font-bold text-primary mb-4 text-center animate-on-scroll">Journal</h3>
    <p class="text-xl text-gray-700 text-center mb-12 animate-on-scroll">Insights, tips, and stories from our herbal
        experts.</p>
    <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 px-6">
        <?php
        $result = $conn->query("SELECT * FROM articles ORDER BY date_added DESC LIMIT 3");
        while ($row = $result->fetch_assoc()) {
            echo "<article class='bg-primaryLight rounded-xl shadow-md overflow-hidden animate-on-scroll flex flex-col'>";
            if ($row['image_url'])
                echo "<img src='../Uploads/{$row['image_url']}' alt='{$row['title']}' class='w-full h-48 object-cover'>";
            else
                echo "<div class='h-48 bg-gray-200 border-2 border-dashed'></div>";
            echo "<div class='p-6 flex-1 flex flex-col'>";
            echo "<span class='text-sm text-secondary font-semibold'>{$row['date_added']}</span>";
            echo "<h3 class='text-2xl font-bold mt-2 mb-4'>{$row['title']}</h3>";
            echo "<p class='text-gray-700 mb-6 flex-1'>{$row['excerpt']}</p>";
            echo "<a href='../pages/blog.php?id={$row['id']}' class='mt-auto inline-flex items-center text-primary font-semibold hover:underline'>";
            echo "Read More <i class='fas fa-arrow-right ml-2'></i></a>";
            echo "</div></article>";
        }
        ?>
    </div>
    <div class="mt-12 flex justify-center space-x-3 animate-on-scroll">
        <a href="../pages/blog.php"
            class="inline-block bg-primary hover:bg-primaryDark text-white px-6 py-3 rounded-full font-medium transition-transform transform hover:scale-105 shadow-lg">
            View All Articles
        </a>
    </div>
    </div>
</section>



<div class="section-divider">
    <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
        <path
            d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z"
            opacity=".25" class="fill-current text-primary"></path>
        <path
            d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z"
            opacity=".5" class="fill-current text-primary"></path>
        <path
            d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z"
            class="fill-current text-primary"></path>
    </svg>
</div>

<?php
include "../pages/contact.php";
?>


<?php include "../includes/footer.php"; ?>
<?php $conn->close(); ?>