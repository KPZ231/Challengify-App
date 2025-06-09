<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Challenge yourself daily with micro-challenges designed to spark creativity and improve your coding skills">
    <meta name="keywords" content="coding challenges, programming practice, daily challenges, micro-challenges, coding skills, developer challenges">
    <meta name="author" content="Challengify">
    <meta property="og:title" content="Challengify | Daily Micro-Challenges to Spark Your Creativity">
    <meta property="og:description" content="Challenge yourself daily with micro-challenges designed to spark creativity and improve your coding skills">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Challengify">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Challengify | Daily Micro-Challenges">
    <meta name="twitter:description" content="Challenge yourself daily with micro-challenges designed to spark creativity and improve your coding skills">
    <title>Challengify | Daily Micro-Challenges to Spark Your Creativity</title>
    <link rel="stylesheet" href="/css/tailwind/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
</head>
<body>
    <?php include __DIR__ . '/partials/navbar.php'; ?>
    <?php include __DIR__ . '/partials/cookie-accept.php'; ?>
    <!-- Hero Section -->
    <header class="ch-bg-gradient text-white py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl" data-aos="fade-up">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Daily Micro-Challenges to Spark Your Creativity</h1>
                <p class="text-xl mb-8">Challenge yourself with short, creative tasks that take just 15-30 minutes to complete. Build habits, earn badges, and join a community of inspired creators.</p>
                <div class="flex flex-wrap gap-4">
                    <a href="/register" class="ch-btn ch-btn-primary bg-white text-blue-600 hover:bg-blue-50">Get Started</a>
                    <a href="/challenges" class="ch-btn ch-btn-outline-light border-2 border-white text-white">View Today's Challenges</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Categories Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12" data-aos="fade-up">Explore Challenge Categories</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <!-- Creative Writing -->
                <a href="/category/creative-writing" class="block" data-aos="fade-up" data-aos-delay="100">
                    <div class="ch-card bg-blue-50 shadow-md h-full p-6 flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Creative Writing</h3>
                        <p class="text-sm text-gray-600">Write a 100-word story or explore new writing prompts daily</p>
                    </div>
                </a>

                <!-- Mobile Photography -->
                <a href="/category/photography" class="block" data-aos="fade-up" data-aos-delay="200">
                    <div class="ch-card bg-blue-50 shadow-md h-full p-6 flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Mobile Photography</h3>
                        <p class="text-sm text-gray-600">Capture themed photos like shadows, reflections, and more</p>
                    </div>
                </a>

                <!-- DIY & Crafts -->
                <a href="/category/diy-crafts" class="block" data-aos="fade-up" data-aos-delay="300">
                    <div class="ch-card bg-blue-50 shadow-md h-full p-6 flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">DIY & Crafts</h3>
                        <p class="text-sm text-gray-600">Create simple items from recycled materials and everyday objects</p>
                    </div>
                </a>

                <!-- Healthy Habits -->
                <a href="/category/healthy-habits" class="block" data-aos="fade-up" data-aos-delay="400">
                    <div class="ch-card bg-blue-50 shadow-md h-full p-6 flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Healthy Habits</h3>
                        <p class="text-sm text-gray-600">Try meditation, new healthy recipes, and simple wellness tasks</p>
                    </div>
                </a>

                <!-- Practical Skills -->
                <a href="/category/practical-skills" class="block" data-aos="fade-up" data-aos-delay="500">
                    <div class="ch-card bg-blue-50 shadow-md h-full p-6 flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Practical Skills</h3>
                        <p class="text-sm text-gray-600">Learn language phrases, cooking techniques, and useful life skills</p>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-16 bg-blue-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12" data-aos="fade-up">How It Works</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <span class="text-blue-600 font-bold text-xl">1</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Get Your Daily Challenge</h3>
                    <p class="text-gray-600">Receive a new micro-challenge each day across different categories. Each task takes just 15-30 minutes to complete.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-md" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <span class="text-blue-600 font-bold text-xl">2</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Submit Your Response</h3>
                    <p class="text-gray-600">Complete your challenge and submit your response - whether it's a photo, description, short video, or link.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-md" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <span class="text-blue-600 font-bold text-xl">3</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Earn Points & Badges</h3>
                    <p class="text-gray-600">Get votes from the community, earn badges, and build your reputation as you participate in more challenges.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Community Showcase -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-4" data-aos="fade-up">Community Showcase</h2>
            <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto" data-aos="fade-up">See what our community has created through daily challenges. Get inspired by the creativity of others.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- These would be dynamically generated in a real implementation -->
                <div class="ch-card shadow-md overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                    <img src="/images/placeholder-1.jpg" alt="Community showcase" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <span class="ch-badge bg-blue-100 text-blue-600 mb-2">Creative Writing</span>
                        <h3 class="font-semibold mb-2">100-Word Story: The Journey</h3>
                        <p class="text-sm text-gray-600 mb-3">By @username</p>
                        <div class="flex items-center text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
                            </svg>
                            <span>24 votes</span>
                        </div>
                    </div>
                </div>
                
                <div class="ch-card shadow-md overflow-hidden" data-aos="fade-up" data-aos-delay="200">
                    <img src="/images/placeholder-2.jpg" alt="Community showcase" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <span class="ch-badge bg-blue-100 text-blue-600 mb-2">Mobile Photography</span>
                        <h3 class="font-semibold mb-2">Shadow Play</h3>
                        <p class="text-sm text-gray-600 mb-3">By @photographer</p>
                        <div class="flex items-center text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
                            </svg>
                            <span>42 votes</span>
                        </div>
                    </div>
                </div>
                
                <div class="ch-card shadow-md overflow-hidden" data-aos="fade-up" data-aos-delay="300">
                    <img src="/images/placeholder-3.jpg" alt="Community showcase" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <span class="ch-badge bg-blue-100 text-blue-600 mb-2">DIY & Crafts</span>
                        <h3 class="font-semibold mb-2">Upcycled Container Garden</h3>
                        <p class="text-sm text-gray-600 mb-3">By @craftlover</p>
                        <div class="flex items-center text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
                            </svg>
                            <span>37 votes</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-10" data-aos="fade-up">
                <a href="/showcase" class="ch-btn ch-btn-primary">View More Submissions</a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 ch-bg-gradient text-white">
        <div class="container mx-auto px-4 text-center" data-aos="fade-up">
            <h2 class="text-3xl font-bold mb-4">Ready to Challenge Yourself?</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto">Join thousands of people who are sparking their creativity with simple daily challenges.</p>
            <a href="/register" class="ch-btn ch-btn-primary bg-white text-blue-600 hover:bg-blue-50 hover:text-blue-700">Get Started Today</a>
        </div>
    </section>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>
</body>
</html>