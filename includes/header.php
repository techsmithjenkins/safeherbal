<?php
session_start();
include "../config/config.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fredy Herbal - <?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : "Home"; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="../assets/favicon.png" type="png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap"
        rel="stylesheet">
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
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                        script: ['Dancing Script', 'cursive']
                    }
                }
            }
        };
    </script>
    <style>
        /* Prevent horizontal scrolling */
        * {
            box-sizing: border-box;
        }
        
        html, body {
            overflow-x: hidden;
            max-width: 100%;
        }

        .sticky-header {
            position: fixed;
            top: 0;
            width: 100%;
            max-width: 100vw;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .sticky-header.scrolled {
            background-color: rgba(1, 1, 127, 0.95);
            padding: 10px 0;
        }

        .nav-link {
            position: relative;
            padding: 8px 12px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: white;
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* Mobile menu improvements */
        #mobile-menu {
            max-width: 100vw;
            left: 0;
            right: 0;
        }

        /* Container improvements */
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        @media (min-width: 640px) {
            .container {
                max-width: 640px;
            }
        }

        @media (min-width: 768px) {
            .container {
                max-width: 768px;
            }
        }

        @media (min-width: 1024px) {
            .container {
                max-width: 1024px;
            }
        }

        @media (min-width: 1280px) {
            .container {
                max-width: 1280px;
            }
        }

        @media (min-width: 1536px) {
            .container {
                max-width: 1536px;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const header = document.querySelector('.sticky-header');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            });

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

            // Mobile dropdown menu toggle
            var menuBtn = document.getElementById('mobile-menu-btn');
            var mobileMenu = document.getElementById('mobile-menu');
            var isOpen = false;

            if (menuBtn && mobileMenu) {
                // Toggle menu on hamburger click
                menuBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    if (!isOpen) {
                        mobileMenu.classList.remove('invisible', 'opacity-0');
                        mobileMenu.classList.add('visible', 'opacity-100');
                        isOpen = true;
                    } else {
                        mobileMenu.classList.add('invisible', 'opacity-0');
                        mobileMenu.classList.remove('visible', 'opacity-100');
                        isOpen = false;
                    }
                });

                // Prevent menu click from bubbling to document
                mobileMenu.addEventListener('click', function (e) {
                    e.stopPropagation();
                });

                // Close menu on link click
                mobileMenu.querySelectorAll('a').forEach(function (link) {
                    link.addEventListener('click', function () {
                        mobileMenu.classList.add('invisible', 'opacity-0');
                        mobileMenu.classList.remove('visible', 'opacity-100');
                        isOpen = false;
                    });
                });

                // Close menu on outside click
                document.addEventListener('click', function () {
                    if (isOpen) {
                        mobileMenu.classList.add('invisible', 'opacity-0');
                        mobileMenu.classList.remove('visible', 'opacity-100');
                        isOpen = false;
                    }
                });
            }
        });
    </script>
</head>
<body class="font-sans bg-gray-50">
    <header class="sticky-header bg-deepBlue py-1" style="font-family:'Times New Roman', Times, serif;">
        <div class="container mx-auto px-4">
            <div class="flex flex-row justify-between items-center">
                <!-- Logo -->
                <div class="flex items-center">
                    <span class="p-2 rounded-md bg-white cursor-pointer">
                        <a href="../pages/welcome.php" class="flex items-center text-gray-900">
                            <p class="ml-2 text-2xl font-extrabold" style="color: #01932f;font-family:Arial;">FREDY HERBAL</p>
                        </a>
                    </span>
                </div>
                
                <!-- Desktop Navigation -->
                <nav class="hidden md:flex flex-wrap justify-center gap-1 md:gap-0">
                    <a href="../pages/homepage.php" class="nav-link text-white font-bold text-sm md:text-base">ABOUT US</a>
                    <a href="../pages/homepage.php#services" class="nav-link text-white font-bold text-sm md:text-base">OUR SERVICES</a>
                    <a href="../pages/homepage.php#edge" class="nav-link text-white font-bold text-sm md:text-base">SAFE & EFFICIENT</a>
                    <a href="../pages/homepage.php#testimonials" class="nav-link text-white font-bold text-sm md:text-base">OUR PATIENTS SAY...</a>
                    <a href="../pages/homepage.php#updates" class="nav-link text-white font-bold text-sm md:text-base">RECENT UPDATES</a>
                    <a href="../pages/homepage.php#contact" class="nav-link text-white font-bold text-sm md:text-base">CONTACT US</a>
                    <?php if (isset($_SESSION["admin_id"])): ?>
                        <!-- <a href="../admin/index.php" class="nav-link text-white font-bold text-sm md:text-base">ADMIN PANEL</a> -->
                    <?php endif; ?>
                </nav>

                <!-- Mobile Hamburger Button -->
                <button id="mobile-menu-btn" class="md:hidden text-white p-2 focus:outline-none">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Dropdown Menu -->
        <div id="mobile-menu"
            class="absolute left-0 top-full w-full bg-deepBlue z-[2000] flex flex-col items-center py-4 transition-all duration-300 ease-in-out invisible opacity-0 rounded-b-2xl shadow-lg md:hidden">
            <nav class="flex flex-col gap-2 text-center w-full px-6">
                <a href="../pages/homepage.php" class="nav-link text-white font-bold text-lg py-2">ABOUT US</a>
                <a href="../pages/homepage.php#services" class="nav-link text-white font-bold text-lg py-2">OUR SERVICES</a>
                <a href="../pages/homepage.php#edge" class="nav-link text-white font-bold text-lg py-2">SAFE & EFFICIENT</a>
                <a href="../pages/homepage.php#testimonials" class="nav-link text-white font-bold text-lg py-2">OUR PATIENTS SAY...</a>
                <a href="../pages/homepage.php#updates" class="nav-link text-white font-bold text-lg py-2">RECENT UPDATES</a>
                <a href="../pages/homepage.php#contact" class="nav-link text-white font-bold text-lg py-2">CONTACT US</a>
                <?php if (isset($_SESSION["admin_id"])): ?>
                    <!-- <a href="../admin/index.php" class="nav-link text-white font-bold text-lg py-2">ADMIN PANEL</a> -->
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="container mx-auto p-4"></main>
</body>
</html>