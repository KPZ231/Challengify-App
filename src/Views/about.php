<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Challengify - Daily Micro-Challenges</title>
    <link href="/css/tailwind/tailwind.min.css" rel="stylesheet">
    <link href="/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<?php include 'partials/navbar.php'; ?>

<header class="ch-header">
    <div class="container mx-auto px-4 py-16 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">About Challengify</h1>
        <p class="text-xl opacity-90 max-w-2xl mx-auto">Daily micro-challenges to spark your creativity and build new habits</p>
    </div>
</header>

<main class="py-12">
    <div class="container mx-auto px-4">
        <!-- What is Challengify Section -->
        <section class="mb-16">
            <h2 class="text-3xl font-bold mb-8">What is Challengify?</h2>
            <p class="text-lg text-gray-700 mb-8">Challengify is a platform where short, creative "micro-challenges" are published daily across various categories. Each challenge is designed to take just 15-30 minutes of your time, making them perfect for busy schedules.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="ch-card bg-white p-6 shadow-lg">
                    <i class="fas fa-pencil-alt text-3xl text-blue-600 mb-4"></i>
                    <h3 class="text-xl font-semibold mb-3">Creative Writing</h3>
                    <p class="text-gray-600">Write a 100-word story fragment or express your creativity through words.</p>
                </div>
                
                <div class="ch-card bg-white p-6 shadow-lg">
                    <i class="fas fa-camera text-3xl text-blue-600 mb-4"></i>
                    <h3 class="text-xl font-semibold mb-3">Mobile Photography</h3>
                    <p class="text-gray-600">Capture themed photos like shadows, reflections, or daily moments.</p>
                </div>
                
                <div class="ch-card bg-white p-6 shadow-lg">
                    <i class="fas fa-paint-brush text-3xl text-blue-600 mb-4"></i>
                    <h3 class="text-xl font-semibold mb-3">DIY & Crafts</h3>
                    <p class="text-gray-600">Create simple items from recycled materials and everyday objects.</p>
                </div>
            </div>
        </section>

        <!-- Why Choose Us Section -->
        <section class="mb-16">
            <h2 class="text-3xl font-bold mb-8">Why Choose Challengify?</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="ch-card bg-white p-8 shadow-lg">
                    <h3 class="text-2xl font-semibold mb-4">Daily Dose of Inspiration</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span>Simple but creative ideas for small daily actions</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span>Quick challenges that fit into your busy schedule</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span>Perfect for students, working professionals, and parents</span>
                        </li>
                    </ul>
                </div>

                <div class="ch-card bg-white p-8 shadow-lg">
                    <h3 class="text-2xl font-semibold mb-4">Community & Gamification</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i class="fas fa-star text-yellow-500 mt-1 mr-3"></i>
                            <span>Earn points and badges as you complete challenges</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-users text-blue-500 mt-1 mr-3"></i>
                            <span>Connect with like-minded creative individuals</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-trophy text-purple-500 mt-1 mr-3"></i>
                            <span>Participate in weekly championships</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="text-center py-12 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg">
            <div class="max-w-2xl mx-auto px-4">
                <h2 class="text-3xl font-bold text-white mb-4">Ready to Start Your Creative Journey?</h2>
                <p class="text-white opacity-90 mb-8">Join our community of creative minds and start completing daily challenges today!</p>
                <a href="/register" class="ch-btn ch-btn-outline-light text-lg">Get Started Now</a>
            </div>
        </section>
    </div>
</main>

<?php include 'partials/footer.php'; ?>

</body>
</html>
