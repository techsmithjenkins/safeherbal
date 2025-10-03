<?php
session_start(); // Start session for potential admin checks (optional for frontend)
$pageTitle = "Welcome";
include_once "../config/db_connect.php";

// Function to fetch content with defaults and safe HTML rendering
function getWelcomeContent($conn, $page, $caption, $defaultHeader = null, $defaultContent = null, $defaultExcerpt = null)
{
    try {
        $stmt = $conn->prepare("SELECT header, content, excerpt FROM site_content WHERE page = ? AND caption = ? LIMIT 1");
        $stmt->bind_param("ss", $page, $caption);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row && ($row['header'] !== null || $row['content'] !== '' || $row['excerpt'] !== null)) {
            // Sanitize database content to prevent XSS
            return [
                'header' => $row['header'] !== null ? htmlspecialchars($row['header'], ENT_QUOTES, 'UTF-8') : $defaultHeader,
                'content' => $row['content'] !== '' ? htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8') : $defaultContent,
                'excerpt' => $row['excerpt'] !== null ? htmlspecialchars($row['excerpt'], ENT_QUOTES, 'UTF-8') : $defaultExcerpt
            ];
        }
        // Use default content as-is (trusted HTML) if no entry
        return ['header' => $defaultHeader, 'content' => $defaultContent, 'excerpt' => $defaultExcerpt];
    } catch (Exception $e) {
        error_log("Error fetching content: " . $e->getMessage());
        return ['header' => $defaultHeader, 'content' => $defaultContent, 'excerpt' => $defaultExcerpt];
    }
}

// Fetch welcome section content using caption 'welcome' for all fields
$welcomeContent = getWelcomeContent($conn, 'welcome', 'welcome', 'Welcome!', null, null);
$welcomeMessage = getWelcomeContent($conn, 'welcome', 'welcome', null, 'This is the official website of <span class="font-bold text-secondary">Fredy Herbal</span><br>We are a Herbal Medicine Treatment Company registered in Ghana as <span class="font-semibold text-green-700">AGBENYEGA HERBAL CONCEPT</span> since 2013.', null);
$welcomeExcerpt = getWelcomeContent($conn, 'welcome', 'welcome', null, null, 'We target the root cause, go beyond the cure, and restore your body system.');

// Fetch welcome image
$imageCaption = "welcome";
try {
    $stmt_image = $conn->prepare("SELECT image_url FROM gallery WHERE caption = ? LIMIT 1");
    $stmt_image->bind_param("s", $imageCaption);
    $stmt_image->execute();
    $result_image = $stmt_image->get_result();
    $galleryImage = $result_image->fetch_assoc();
    $imageUrl = $galleryImage ? "../uploads/" . htmlspecialchars($galleryImage['image_url'], ENT_QUOTES, 'UTF-8') : "../assets/welcome.jpg";
    $stmt_image->close();
} catch (Exception $e) {
    error_log("Error fetching image: " . $e->getMessage());
    $imageUrl = "../assets/welcome.jpg";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FREDY HERBAL | Natural Herbal Medicine</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="../assets/favicon.png" type="png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style type="text/css">
        @font-face {
            font-family: Poppins;
            font-style: normal;
            font-weight: 300;
            src: url(/cf-fonts/s/poppins/5.0.11/devanagari/300/normal.woff2);
            unicode-range: U+0900-097F, U+1CD0-1CF9, U+200C-200D, U+20A8, U+20B9, U+25CC, U+A830-A839, U+A8E0-A8FF;
            font-display: swap;
        }

        @font-face {
            font-family: Poppins;
            font-style: normal;
            font-weight: 300;
            src: url(/cf-fonts/s/poppins/5.0.11/latin-ext/300/normal.woff2);
            unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
            font-display: swap;
        }

        @font-face {
            font-family: Poppins;
            font-style: normal;
            font-weight: 300;
            src: url(/cf-fonts/s/poppins/5.0.11/latin/300/normal.woff2);
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            font-display: swap;
        }

        @font-face {
            font-family: Poppins;
            font-style: normal;
            font-weight: 400;
            src: url(/cf-fonts/s/poppins/5.0.11/latin-ext/400/normal.woff2);
            unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
            font-display: swap;
        }

        @font-face {
            font-family: Poppins;
            font-style: normal;
            font-weight: 400;
            src: url(/cf-fonts/s/poppins/5.0.11/latin/400/normal.woff2);
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            font-display: swap;
        }

        @font-face {
            font-family: Poppins;
            font-style: normal;
            font-weight: 400;
            src: url(/cf-fonts/s/poppins/5.0.11/devanagari/400/normal.woff2);
            unicode-range: U+0900-097F, U+1CD0-1CF9, U+200C-200D, U+20A8, U+20B9, U+25CC, U+A830-A839, U+A8E0-A8FF;
            font-display: swap;
        }

        @font-face {
            font-family: Poppins;
            font-style: normal;
            font-weight: 500;
            src: url(/cf-fonts/s/poppins/5.0.11/devanagari/500/normal.woff2);
            unicode-range: U+0900-097F, U+1CD0-1CF9, U+200C-200D, U+20A8, U+20B9, U+25CC, U+A830-A839, U+A8E0-A8FF;
            font-display: swap;
        }

        @font-face {
            font-family: Poppins;
            font-style: normal;
            font-weight: 500;
            src: url(/cf-fonts/s/poppins/5.0.11/latin/500/normal.woff2);
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            font-display: swap;
        }

        @font-face {
            font-family: Poppins;
            font-style: normal;
            font-weight: 500;
            src: url(/cf-fonts/s/poppins/5.0.11/latin-ext/500/normal.woff2);
            unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
            font-display: swap;
        }

        @font-face {
            font-family: Poppins;
            font-style: normal;
            font-weight: 600;
            src: url(/cf-fonts/s/poppins/5.0.11/latin-ext/600/normal.woff2);
            unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
            font-display: swap;
        }

        @font-face {
            font-family: Poppins;
            font-style: normal;
            font-weight: 600;
            src: url(/cf-fonts/s/poppins/5.0.11/devanagari/600/normal.woff2);
            unicode-range: U+0900-097F, U+1CD0-1CF9, U+200C-200D, U+20A8, U+20B9, U+25CC, U+A830-A839, U+A8E0-A8FF;
            font-display: swap;
        }

        @font-face {
            font-family: Poppins;
            font-style: normal;
            font-weight: 600;
            src: url(/cf-fonts/s/poppins/5.0.11/latin/600/normal.woff2);
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            font-display: swap;
        }

        @font-face {
            font-family: Poppins;
            font-style: normal;
            font-weight: 700;
            src: url(/cf-fonts/s/poppins/5.0.11/latin-ext/700/normal.woff2);
            unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
            font-display: swap;
        }

        @font-face {
            font-family: Poppins;
            font-style: normal;
            font-weight: 700;
            src: url(/cf-fonts/s/poppins/5.0.11/devanagari/700/normal.woff2);
            unicode-range: U+0900-097F, U+1CD0-1CF9, U+200C-200D, U+20A8, U+20B9, U+25CC, U+A830-A839, U+A8E0-A8FF;
            font-display: swap;
        }

        @font-face {
            font-family: Poppins;
            font-style: normal;
            font-weight: 700;
            src: url(/cf-fonts/s/poppins/5.0.11/latin/700/normal.woff2);
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            font-display: swap;
        }

        @font-face {
            font-family: Playfair Display;
            font-style: normal;
            font-weight: 400;
            src: url(/cf-fonts/v/playfair-display/5.0.18/vietnamese/wght/normal.woff2);
            unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
            font-display: swap;
        }

        @font-face {
            font-family: Playfair Display;
            font-style: normal;
            font-weight: 400;
            src: url(/cf-fonts/v/playfair-display/5.0.18/cyrillic/wght/normal.woff2);
            unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
            font-display: swap;
        }

        @font-face {
            font-family: Playfair Display;
            font-style: normal;
            font-weight: 400;
            src: url(/cf-fonts/v/playfair-display/5.0.18/latin/wght/normal.woff2);
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            font-display: swap;
        }

        @font-face {
            font-family: Playfair Display;
            font-style: normal;
            font-weight: 400;
            src: url(/cf-fonts/v/playfair-display/5.0.18/latin-ext/wght/normal.woff2);
            unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
            font-display: swap;
        }

        @font-face {
            font-family: Playfair Display;
            font-style: normal;
            font-weight: 600;
            src: url(/cf-fonts/v/playfair-display/5.0.18/vietnamese/wght/normal.woff2);
            unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
            font-display: swap;
        }

        @font-face {
            font-family: Playfair Display;
            font-style: normal;
            font-weight: 600;
            src: url(/cf-fonts/v/playfair-display/5.0.18/latin-ext/wght/normal.woff2);
            unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
            font-display: swap;
        }

        @font-face {
            font-family: Playfair Display;
            font-style: normal;
            font-weight: 600;
            src: url(/cf-fonts/v/playfair-display/5.0.18/latin/wght/normal.woff2);
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            font-display: swap;
        }

        @font-face {
            font-family: Playfair Display;
            font-style: normal;
            font-weight: 600;
            src: url(/cf-fonts/v/playfair-display/5.0.18/cyrillic/wght/normal.woff2);
            unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
            font-display: swap;
        }

        @font-face {
            font-family: Playfair Display;
            font-style: normal;
            font-weight: 700;
            src: url(/cf-fonts/v/playfair-display/5.0.18/vietnamese/wght/normal.woff2);
            unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
            font-display: swap;
        }

        @font-face {
            font-family: Playfair Display;
            font-style: normal;
            font-weight: 700;
            src: url(/cf-fonts/v/playfair-display/5.0.18/latin/wght/normal.woff2);
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            font-display: swap;
        }

        @font-face {
            font-family: Playfair Display;
            font-style: normal;
            font-weight: 700;
            src: url(/cf-fonts/v/playfair-display/5.0.18/cyrillic/wght/normal.woff2);
            unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
            font-display: swap;
        }

        @font-face {
            font-family: Playfair Display;
            font-style: normal;
            font-weight: 700;
            src: url(/cf-fonts/v/playfair-display/5.0.18/latin-ext/wght/normal.woff2);
            unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
            font-display: swap;
        }
    </style>
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
        }
    </script>
    <script>
        // Initialize animations on scroll
        document.addEventListener('DOMContentLoaded', function () {
            // Set up Intersection Observer for scroll animations
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animated');
                    }
                });
            }, {
                threshold: 0.1
            });

            // Observe all animate-on-scroll elements
            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                observer.observe(el);
            });

            // Header scroll effect
            const header = document.querySelector('.sticky-header');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            });

            // Footer scroll effect
            const footer = document.querySelector('.sticky-footer');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    footer.classList.add('scrolled');
                } else {
                    footer.classList.remove('scrolled');
                }
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

            // Auto rotate every 8 seconds
            let rotationInterval = setInterval(rotateTestimonials, 8000);

            // Pause on hover
            document.getElementById('testimonial-container').addEventListener('mouseenter', () => {
                autoRotate = false;
                clearInterval(rotationInterval);
            });

            document.getElementById('testimonial-container').addEventListener('mouseleave', () => {
                autoRotate = true;
                rotationInterval = setInterval(rotateTestimonials, 8000);
            });

            // Initial rotation
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
        });
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

        .sticky-header {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .sticky-header.scrolled {
            background-color: rgba(1, 1, 127, 0.95);
            padding: 10px 0;
        }

        .sticky-footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .sticky-footer.scrolled {
            background-color: rgba(1, 1, 127, 0.95);
            padding: 10px 0;
        }

        /* FOOTER-LINK UNDERLINE */
        .footer-link {
            position: relative;
            padding: 8px 12px;
        }

        .footer-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: rgba(255, 255, 255, 0.605);
            transition: width 0.3s ease;
        }

        .footer-link:hover::after {
            width: 100%;
        }

        .nav-link {
            position: relative;
            padding: 8px 12px;
            transition: all 0.3s ease;
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
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100px;
        }
    </style>
</head>

<body class="font-sans bg-gray-50">
    <!-- Sticky Header -->
    <header class="sticky-header bg-deepBlue py-1" style="font-family:'Times New Roman', Times, serif;">
        <div class="container mx-auto px-4">
            <div class="flex flex-row justify-between items-center">
                <!-- Logo top left -->
                <div class="flex items-center">
                    <span class="p-2 rounded-md bg-white cursor-pointer">
                        <a class="flex items-center text-gray-900" href="../pages/homepage.php">
                            <p class="ml-2 text-2xl font-extrabold" style="color: #01932f;font-family:Arial;">FREDY
                                HERBAL</p>
                        </a>
                    </span>
                </div>
                <!-- Desktop Nav -->
                <nav class="hidden md:flex flex-wrap justify-center gap-1 md:gap-0">
                    <a href="../pages/homepage.php" class="nav-link text-white font-bold text-sm md:text-base">ABOUT
                        US</a>
                    <a href="../pages/homepage.php #services"
                        class="nav-link text-white font-bold text-sm md:text-base">OUR SERVICES</a>
                    <a href="../pages/homepage.php #edge"
                        class="nav-link text-white font-bold text-sm md:text-base">OUR UNIQUE EDGE</a>
                    <a href="../pages/homepage.php #testimonials"
                        class="nav-link text-white font-bold text-sm md:text-base">TESTIMONIALS</a>
                    <a href="../pages/homepage.php #updates"
                        class="nav-link text-white font-bold text-sm md:text-base">RECENT UPDATES</a>
                    <a href="../pages/homepage.php #contact"
                        class="nav-link text-white font-bold text-sm md:text-base">CONTACT US</a>
                </nav>
                <!-- Mobile Hamburger top right -->
                <button id="mobile-menu-btn" class="md:hidden text-white p-2 focus:outline-none">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
        <!-- Mobile Dropdown Menu -->
        <div id="mobile-menu"
            class="absolute left-0 top-full w-full bg-deepBlue z-[2000] flex flex-col items-center py-4 transition-all duration-300 ease-in-out invisible opacity-0 rounded-b-2xl shadow-lg md:hidden">
            <nav class="flex flex-col gap-2 text-center w-full px-6">
                <a href="../pages/homepage.php #about" class="nav-link text-white font-bold text-lg py-2">ABOUT US</a>
                <a href="../pages/homepage.php #services" class="nav-link text-white font-bold text-lg py-2">OUR
                    SERVICES</a>
                <a href="../pages/homepage.php #edge" class="nav-link text-white font-bold text-lg py-2">OUR UNIQUE
                    EDGE</a>
                <a href="../pages/homepage.php #testimonials"
                    class="nav-link text-white font-bold text-lg py-2">TESTIMONIALS</a>
                <a href="../pages/homepage.php #updates" class="nav-link text-white font-bold text-lg py-2">RECENT
                    UPDATES</a>
                <a href="../pages/homepage.php #contact" class="nav-link text-white font-bold text-lg py-2">CONTACT
                    US</a>
            </nav>
        </div>
        <script>
            // Mobile dropdown menu toggle
            document.addEventListener('DOMContentLoaded', function () {
                var menuBtn = document.getElementById('mobile-menu-btn');
                var mobileMenu = document.getElementById('mobile-menu');
                var isOpen = false;

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
            });
        </script>
    </header>

    <!-- Welcome Section -->
    <section class="herb-bg min-h-screen flex items-center pt-20 pb-6 px-4 relative overflow-hidden">
        <div class="leaf-decoration top-10 left-10"></div>
        <div class="leaf-decoration bottom-10 right-10 rotate-45"></div>
        <div class="leaf-decoration top-1/3 right-20 rotate-12"></div>

        <div class="container mx-auto max-w-6xl">
            <div class="flex flex-col-reverse lg:flex-row items-center lg:items-stretch gap-12 lg:gap-0">
                <!-- Left: Content -->
                <div class="w-full lg:w-1/2 flex flex-col justify-center lg:pr-12 relative z-10"
                    style="font-family: 'Poppins', Arial, Helvetica, sans-serif;">
                    <h1 class="text-7xl md:text-8xl font-times font-bold mb-4 text-primary animate-on-scroll"
                        style="animation-delay: 0.1s">
                        <?php echo $welcomeContent['header']; ?>
                    </h1>
                    <p class="text-xl md:text-2xl mb-4 leading-relaxed animate-on-scroll text-gray-800 p-2"
                        style="animation-delay: 0.3s">
                        <?php echo nl2br($welcomeMessage['content']); ?>
                    </p>
                    <div class="w-full mb-6 p-2 bg-white rounded-xl border border-green-100 shadow-sm inline-block animate-on-scroll text-center"
                        style="animation-delay: 0.5s">
                        <p class="text-lg md:text-xl font-semibold text-primary flex items-center justify-center gap-3"
                            style="font-family: 'Poppins', Arial, sans-serif;">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>No Preservative</span>
                            <span class="text-gray-400">|</span>
                            <span>No Additive</span>
                            <span class="text-gray-400">|</span>
                            <span>100% Natural</span>
                        </p>
                    </div>
                    <p class="text-xl md:text-2xl lg:text-3xl mb-4 text-blue-700 leading-relaxed animate-on-scroll"
                        style="animation-delay: 0.7s">
                        <?php echo nl2br($welcomeExcerpt['excerpt']); ?>
                    </p>
                    <div class="flex flex-wrap gap-4 animate-on-scroll" style="animation-delay: 0.9s">
                        <a href="../pages/homepage.php"
                            class="inline-flex items-center bg-primary hover:bg-primaryDark text-white px-8 py-4 rounded-full text-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            Learn More
                            <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                        <a href="../pages/homepage.php #contact"
                            class="inline-flex items-center border-2 border-primary text-primary font-semibold hover:bg-primary hover:text-white px-8 py-4 rounded-full text-lg font-medium transition-all duration-300">
                            Contact Us
                        </a>
                    </div>
                </div>
                <!-- Right: Image -->
                <div class="w-full lg:w-1/2 flex items-center justify-center relative animate-on-scroll transition-all duration-700 ease-linear"
                    style="animation-delay: 1.1s; min-height: 350px;">
                    <img src="<?php echo $imageUrl; ?>" alt="Welcome Image"
                        class="w-full h-auto max-w-lg rounded-2xl shadow-2xl border-4 border-green-500 object-cover mx-auto transition-all duration-500 ease-in-out hover:scale-105 hover:shadow-3xl hover:border-primaryDark cursor-pointer">

                    <!-- Floating Icons -->
                    <div class="absolute -top-0 -left-5 animate-[float_4s_ease-in-out_infinite_0.5s]">
                        <div
                            class="w-12 h-12 rounded-full bg-white flex items-center justify-center transition-transform duration-300 ease-in-out hover:scale-125 hover:rotate-12">
                            <i class="fas fa-leaf text-2xl text-primary"></i>
                        </div>
                    </div>

                    <div class="absolute bottom-8 -right-5 animate-[float_4s_ease-in-out_infinite_1.5s]">
                        <div
                            class="w-12 h-12 rounded-full bg-white flex items-center justify-center transition-transform duration-300 ease-in-out hover:scale-125 hover:-rotate-12">
                            <i class="fas fa-heart text-2xl text-primary"></i>
                        </div>
                    </div>

                    <div class="absolute top-1/2 -right-7 animate-[float_4s_ease-in-out_infinite_2.5s]">
                        <div
                            class="w-12 h-12 rounded-full bg-white flex items-center justify-center transition-transform duration-300 ease-in-out hover:scale-125 hover:rotate-6">
                            <i class="fas fa-seedling text-2xl text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-deepBlue text-white py-4">
    <div class="container px-4">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
                <div class="flex items-center">
                    <span class="p-2 rounded-md bg-white">
                        <p class="ml-2 text-2xl font-extrabold" style="color: #01932f;font-family:Arial;">FREDY HERBAL
                        </p>
                    </span>
                </div>
                <p class="mt-2 text-gray-300 max-w-md">Natural healing solutions rooted in Ghanaian tradition, perfected
                    with modern science for your optimal health and wellness.</p>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <h4 class="text-lg font-bold mb-1">Quick Links</h4>
                    <ul class="space-y-1">
                        <li><a href="../pages/homepage.php#about" class="text-gray-300 hover:text-white transition-colors">About Us</a></li>
                        <li><a href="../pages/homepage.php#services" class="text-gray-300 hover:text-white transition-colors">Our Services</a></li>
                        <li><a href="../pages/homepage.php#testimonials" class="text-gray-300 hover:text-white transition-colors">Testimonials</a></li>
                        <li><a href="../pages/homepage.php#contact" class="text-gray-300 hover:text-white transition-colors">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-1">Legal</h4>
                    <ul class="space-y-1">
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Terms of Service</a>
                        </li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Disclaimer</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-600/30 mt-2 pt-2 text-center text-gray-400">
            <p>&copy; 2023 FREDY HERBAL (AGBENYEGA HERBAL CONCEPT). All rights reserved.</p>
            <p class="mt-2">
                <a href="https://jurist-jenkins.vercel.app/"
                    class="inline-flex items-center text-gray-400 hover:text-white transition-colors duration-200 footer-link"
                    target="_blank" rel="noopener noreferrer">
                    <span>Designed by</span>
                    <span class="ml-1 font-medium">3J Advertising Solutions</span>
                    <i class="fas fa-external-link-alt text-xs ml-1"></i>
                </a>
            </p>
        </div>
    </div>
</footer>

<style>
    .footer-link {
        position: relative;
        padding: 8px 12px;
    }

    .footer-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background-color: rgba(255, 255, 255, 0.605);
        transition: width 0.3s ease;
    }

    .footer-link:hover::after {
        width: 100%;
    }
</style>

    <script>(function () { function c() { var b = a.contentDocument || a.contentWindow.document; if (b) { var d = b.createElement('script'); d.innerHTML = "window.__CF$cv$params={r:'97b6cbeefd541df8',t:'MTc1NzI1NDMwMC4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);"; b.getElementsByTagName('head')[0].appendChild(d) } } if (document.body) { var a = document.createElement('iframe'); a.height = 1; a.width = 1; a.style.position = 'absolute'; a.style.top = 0; a.style.left = 0; a.style.border = 'none'; a.style.visibility = 'hidden'; document.body.appendChild(a); if ('loading' !== document.readyState) c(); else if (window.addEventListener) document.addEventListener('DOMContentLoaded', c); else { var e = document.onreadystatechange || function () { }; document.onreadystatechange = function (b) { e(b); 'loading' !== document.readyState && (document.onreadystatechange = e, c()) } } } })();</script>
</body>

</html>