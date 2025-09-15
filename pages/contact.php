<?php
// contact.php - Contact page for FREDY HERBAL website
require_once '../config/db_connect.php'; // Path to match admin/index.php

if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . (isset($conn) ? $conn->connect_error : "Connection not initialized"));
}
?>

<section id="contact" class="py-20 bg-white">
    <div class="container mx-auto px-4 max-w-6xl">
        <h2 class="text-4xl font-bold mb-16 text-center text-primary animate-on-scroll">Contact Us</h2>
        <div class="flex flex-col lg:flex-row gap-12">
            <div class="lg:w-1/2 animate-on-scroll">
                <form id="contact-form" class="space-y-6">
                    <div>
                        <label class="block text-gray-700 mb-2">Full Name</label>
                        <input type="text" name="full_name" required placeholder="Full Name"
                            class="contact-input w-full px-4 py-3 rounded-lg focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Email Address</label>
                        <input type="email" name="email" required placeholder="Email Address"
                            class="contact-input w-full px-4 py-3 rounded-lg focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Phone Number</label>
                        <input type="number" name="phone" placeholder="Phone Number"
                            class="contact-input w-full px-4 py-3 rounded-lg focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Message</label>
                        <textarea name="message" required placeholder="Message"
                            class="contact-input w-full px-4 py-3 rounded-lg focus:outline-none h-32"></textarea>
                    </div>
                    <button type="submit"
                        class="w-full bg-primary hover:bg-primaryDark text-white py-4 rounded-lg text-lg font-medium transition-all duration-300">
                        Send Message
                    </button>
                </form>
                <div id="submission-message"
                    class="hidden mt-4 p-4 bg-green-100 text-green-800 rounded-lg text-center transition-opacity duration-500">
                    Thank you for your message. We'll get back to you soon!
                </div>
            </div>
            <div class="lg:w-1/2 animate-on-scroll" style="animation-delay: 0.5s">
                <div
                    class="bg-primaryLight p-6 md:p-8 rounded-2xl shadow-lg h-full transition-all duration-300 hover:shadow-xl">
                    <h3 class="text-2xl md:text-3xl font-serif font-bold mb-6 text-primary text-center md:text-left">
                        Get In Touch
                    </h3>
                    <div class="space-y-4">
                        <!-- Location -->
                        <div
                            class="flex items-center p-4 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="text-primary text-2xl mr-4"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <h4 class="text-xl md:text-2xl font-bold text-primary">Find Us</h4>
                                <p class="text-gray-700 text-base md:text-lg">Online | By Appointment</p>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div
                            class="flex items-center p-4 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="text-primary text-2xl mr-4"><i class="fas fa-phone"></i></div>
                            <div>
                                <h4 class="text-xl md:text-2xl font-bold text-primary">Phone</h4>
                                <p class="text-gray-700 text-base md:text-lg">+233 53 290 8424 | +233 53 261 0210</p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div
                            class="flex items-center p-4 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="text-primary text-2xl mr-4"><i class="fas fa-envelope"></i></div>
                            <div>
                                <h4 class="text-xl md:text-2xl font-bold text-primary">Email</h4>
                                <p class="text-gray-700 text-base md:text-lg">
                                    <a href="mailto:info@fredyherbal.com"
                                        class="hover:text-primary transition-colors">info@fredyherbal.com</a>                                </p>
                            </div>
                        </div>

                        <!-- Business Hours -->
                        <div
                            class="flex items-center p-4 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="text-primary text-2xl mr-4"><i class="fas fa-clock"></i></div>
                            <div>
                                <h4 class="text-xl md:text-2xl font-bold text-primary">Hours</h4>
                                <p class="text-gray-700 text-base md:text-lg">Mon-Fri: 8am-6pm | Sat: 9am-2pm | Sun:
                                    Closed</p>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="mt-6">
                        <h4 class="text-xl md:text-2xl font-bold mb-4 text-primary text-center md:text-left">Follow Us
                        </h4>
                        <div class="flex justify-center md:justify-start space-x-3">
                            <a href="https://www.facebook.com/FredyHerbalOfficial " target="_blank" aria-label="Facebook"
                                class="w-9 h-9 rounded-full bg-white flex items-center justify-center text-primary text-lg hover:bg-primary hover:text-white transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-110">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://vm.tiktok.com/ZMHnhAxJCqBjN-uiczl/ " target="_blank" aria-label="Tik-Tok"
                                class="w-9 h-9 rounded-full bg-white flex items-center justify-center text-primary text-lg hover:bg-primary hover:text-white transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-110">
                                <i class="fab fa-tiktok"></i>
                            </a>
                            <a href="https://whatsapp.com/channel/0029VaSeAYK2ZjCjUKaSLJ3a " target="_blank" aria-label="WhatsApp"
                                class="w-9 h-9 rounded-full bg-white flex items-center justify-center text-primary text-lg hover:bg-primary hover:text-white transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-110">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('contact-form');
        const submissionMessage = document.getElementById('submission-message');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                try {
                    const response = await fetch('submit_contact.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    submissionMessage.textContent = result.message;
                    submissionMessage.classList.remove('hidden');
                    if (result.success) {
                        alert('Your message has been sent successfully!');
                        form.reset();
                        setTimeout(() => {
                            submissionMessage.classList.add('hidden');
                        }, 5000);
                    }
                } catch (error) {
                    submissionMessage.textContent = 'Error submitting message.';
                    submissionMessage.classList.remove('hidden');
                }
            });
        }
    });
</script>