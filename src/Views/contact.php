<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Challengify</title>
    <link rel="stylesheet" href="/css/tailwind/tailwind.min.css">
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<?php include 'partials/navbar.php'; ?>

<header class="ch-header">
    <div class="container mx-auto px-4 py-16 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Contact Us</h1>
        <p class="text-xl opacity-90">Get in touch with the Challengify team</p>
    </div>
</header>

<main class="container mx-auto px-4 py-12">
    <div class="max-w-3xl mx-auto">
        <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline"> Your message has been sent. We'll get back to you soon!</span>
        </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">
                <?php 
                $error = $_GET['error'];
                switch ($error) {
                    case 'invalid_token':
                        echo 'Security token validation failed. Please try again.';
                        break;
                    case 'missing_fields':
                        echo 'Please fill in all required fields.';
                        break;
                    case 'invalid_email':
                        echo 'Please enter a valid email address.';
                        break;
                    default:
                        echo 'An error occurred. Please try again.';
                }
                ?>
            </span>
        </div>
        <?php endif; ?>
        
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div>
                    <h3 class="text-xl font-semibold mb-4">Contact Information</h3>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-primary-color w-6"></i>
                            <span class="ml-3">support@challengify.com</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-phone text-primary-color w-6"></i>
                            <span class="ml-3">+1 (555) 123-4567</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt text-primary-color w-6"></i>
                            <span class="ml-3">123 Challenge Street<br>Innovation City, IC 12345</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-xl font-semibold mb-4">Follow Us</h3>
                    <p class="mb-4">Stay connected with us on social media for the latest updates and challenges.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="ch-btn ch-btn-outline-primary">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="ch-btn ch-btn-outline-primary">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="ch-btn ch-btn-outline-primary">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t pt-8">
                <h3 class="text-xl font-semibold mb-6">Send us a Message</h3>
                <form action="/contact/submit" method="POST">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input type="text" id="name" name="name" required 
                                   class="ch-form-control w-full" 
                                   placeholder="Your name">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" id="email" name="email" required 
                                   class="ch-form-control w-full"
                                   placeholder="your@email.com">
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                        <input type="text" id="subject" name="subject" required 
                               class="ch-form-control w-full"
                               placeholder="Message subject">
                    </div>

                    <div class="mb-6">
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                        <textarea id="message" name="message" rows="5" required 
                                  class="ch-form-control w-full"
                                  placeholder="Your message"></textarea>
                    </div>

                    <button type="submit" class="ch-btn ch-btn-primary">
                        <i class="fas fa-paper-plane mr-2"></i>Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include 'partials/footer.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</body>
</html>

