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
</head>
<body>
    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <!-- Hero Section -->
    <header class="ch-bg-gradient text-white py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl">
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
            <h2 class="text-3xl font-bold text-center mb-12">Explore Challenge Categories</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <!-- Creative Writing -->
                <a href="/category/creative-writing" class="block">
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
                <a href="/category/photography" class="block">
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
                <a href="/category/diy-crafts" class="block">
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
                <a href="/category/healthy-habits" class="block">
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
                <a href="/category/practical-skills" class="block">
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
            <h2 class="text-3xl font-bold text-center mb-12">How It Works</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <span class="text-blue-600 font-bold text-xl">1</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Get Your Daily Challenge</h3>
                    <p class="text-gray-600">Receive a new micro-challenge each day across different categories. Each task takes just 15-30 minutes to complete.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <span class="text-blue-600 font-bold text-xl">2</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Submit Your Response</h3>
                    <p class="text-gray-600">Complete your challenge and submit your response - whether it's a photo, description, short video, or link.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-md">
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
            <h2 class="text-3xl font-bold text-center mb-4">Community Showcase</h2>
            <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto">See what our community has created through daily challenges. Get inspired by the creativity of others.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- These would be dynamically generated in a real implementation -->
                <div class="ch-card shadow-md overflow-hidden">
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
                
                <div class="ch-card shadow-md overflow-hidden">
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
                
                <div class="ch-card shadow-md overflow-hidden">
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
            
            <div class="text-center mt-10">
                <a href="/showcase" class="ch-btn ch-btn-primary">View More Submissions</a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 ch-bg-gradient text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-4">Ready to Challenge Yourself?</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto">Join thousands of people who are sparking their creativity with simple daily challenges.</p>
            <a href="/register" class="ch-btn ch-btn-primary bg-white text-blue-600 hover:bg-blue-50 hover:text-blue-700">Get Started Today</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="ch-footer py-12 text-sm">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Challengify</h3>
                    <p class="mb-4">Daily micro-challenges to spark your creativity and build new habits.</p>
                    <div class="flex space-x-4">
                        <a href="#" aria-label="Facebook">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" aria-label="Instagram">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" aria-label="Twitter">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                            </svg>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Categories</h3>
                    <ul class="space-y-2">
                        <li><a href="/category/creative-writing">Creative Writing</a></li>
                        <li><a href="/category/photography">Mobile Photography</a></li>
                        <li><a href="/category/diy-crafts">DIY & Crafts</a></li>
                        <li><a href="/category/healthy-habits">Healthy Habits</a></li>
                        <li><a href="/category/practical-skills">Practical Skills</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Community</h3>
                    <ul class="space-y-2">
                        <li><a href="/showcase">Showcase</a></li>
                        <li><a href="/leaderboard">Leaderboard</a></li>
                        <li><a href="/events">Community Events</a></li>
                        <li><a href="/badges">Badges & Rewards</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Help & Support</h3>
                    <ul class="space-y-2">
                        <li><a href="/about">About Us</a></li>
                        <li><a href="/contact">Contact</a></li>
                        <li><a href="/faq">FAQ</a></li>
                        <li><a href="/privacy">Privacy Policy</a></li>
                        <li><a href="/terms">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p>&copy; <?php echo date('Y'); ?> Challengify. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>