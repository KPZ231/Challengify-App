<?php
/**
 * User Settings View
 * 
 * @var \Kpzsproductions\Challengify\Models\User $user
 * @var array $languages
 * @var array $timezones
 * @var string $csrfToken
 * @var string|null $flashMessage
 * @var string $flashType
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Account Settings - Challengify</title>
    <link rel="stylesheet" href="/css/tailwind/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <?php require __DIR__ . '/partials/navbar.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <?php if ($flashMessage): ?>
            <div class="mb-4 p-4 rounded-md text-white <?= $flashType === 'success' ? 'bg-green-500' : 'bg-red-500' ?>" role="alert">
                <?= $flashMessage ?>
            </div>
        <?php endif; ?>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-800">Account Settings</h1>
                <p class="text-gray-600">Manage your account preferences and privacy settings</p>
            </div>
            
            <!-- Settings navigation tabs -->
            <div class="bg-gray-50 border-b border-gray-200">
                <div class="flex flex-wrap">
                    <button id="btn-notifications" class="tab-button active px-6 py-3 font-medium text-sm text-blue-600 border-b-2 border-blue-600">
                        <i class="fas fa-bell mr-2"></i>Notifications
                    </button>
                    <button id="btn-privacy" class="tab-button px-6 py-3 font-medium text-sm text-gray-500 hover:text-gray-700">
                        <i class="fas fa-user-shield mr-2"></i>Privacy
                    </button>
                    <button id="btn-language" class="tab-button px-6 py-3 font-medium text-sm text-gray-500 hover:text-gray-700">
                        <i class="fas fa-globe mr-2"></i>Language & Timezone
                    </button>
                </div>
            </div>
            
            <!-- Notifications Tab -->
            <div id="tab-notifications" class="tab-content p-6">
                <form action="/settings/notifications" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Notification Channels</h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="checkbox" id="notification_email" name="notification_email" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" <?= $user->getNotificationEmail() ? 'checked' : '' ?>>
                                <label for="notification_email" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="fas fa-envelope mr-2 text-gray-500"></i>Email notifications
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="notification_push" name="notification_push" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" <?= $user->getNotificationPush() ? 'checked' : '' ?>>
                                <label for="notification_push" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="fas fa-mobile-alt mr-2 text-gray-500"></i>Push notifications (mobile)
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="notification_sms" name="notification_sms" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" <?= $user->getNotificationSms() ? 'checked' : '' ?>>
                                <label for="notification_sms" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="fas fa-sms mr-2 text-gray-500"></i>SMS notifications
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Daily Notification Time</h3>
                        <div class="flex items-center space-x-2">
                            <input type="time" id="notification_time" name="notification_time" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full sm:w-auto shadow-sm sm:text-sm border-gray-300 rounded-md" value="<?= $user->getNotificationTime() ?>">
                            <span class="text-sm text-gray-500">Time for receiving daily notifications</span>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Summary Reports</h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="checkbox" id="weekly_summary" name="weekly_summary" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" <?= $user->getWeeklySummary() ? 'checked' : '' ?>>
                                <label for="weekly_summary" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="fas fa-calendar-week mr-2 text-gray-500"></i>Weekly summary
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="monthly_summary" name="monthly_summary" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" <?= $user->getMonthlySummary() ? 'checked' : '' ?>>
                                <label for="monthly_summary" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="fas fa-calendar-alt mr-2 text-gray-500"></i>Monthly summary
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-5">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-save mr-2"></i>Save Notification Settings
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Privacy Tab -->
            <div id="tab-privacy" class="tab-content hidden p-6">
                <form action="/settings/privacy" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Profile Visibility</h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="radio" id="visibility_public" name="profile_visibility" value="public" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" <?= $user->getProfileVisibility() === 'public' ? 'checked' : '' ?>>
                                <label for="visibility_public" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="fas fa-globe mr-2 text-gray-500"></i>Public - Everyone can see your profile
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="visibility_followers" name="profile_visibility" value="followers" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" <?= $user->getProfileVisibility() === 'followers' ? 'checked' : '' ?>>
                                <label for="visibility_followers" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="fas fa-user-friends mr-2 text-gray-500"></i>Followers Only - Only your followers can see your profile
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="visibility_private" name="profile_visibility" value="private" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" <?= $user->getProfileVisibility() === 'private' ? 'checked' : '' ?>>
                                <label for="visibility_private" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="fas fa-lock mr-2 text-gray-500"></i>Private - Only you can see your profile
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Messaging Permissions</h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="radio" id="messaging_all" name="messaging_permission" value="all" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" <?= $user->getMessagingPermission() === 'all' ? 'checked' : '' ?>>
                                <label for="messaging_all" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="fas fa-comments mr-2 text-gray-500"></i>Everyone can send you messages
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="messaging_followers" name="messaging_permission" value="followers" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" <?= $user->getMessagingPermission() === 'followers' ? 'checked' : '' ?>>
                                <label for="messaging_followers" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="fas fa-user-friends mr-2 text-gray-500"></i>Only followers can send you messages
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="messaging_none" name="messaging_permission" value="none" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" <?= $user->getMessagingPermission() === 'none' ? 'checked' : '' ?>>
                                <label for="messaging_none" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="fas fa-ban mr-2 text-gray-500"></i>No one can send you messages
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-5">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-save mr-2"></i>Save Privacy Settings
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Language & Timezone Tab -->
            <div id="tab-language" class="tab-content hidden p-6">
                <form action="/settings/language" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Interface Language</h3>
                        <select id="language" name="language" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <?php foreach ($languages as $code => $name): ?>
                                <option value="<?= $code ?>" <?= $user->getLanguage() === $code ? 'selected' : '' ?>><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Timezone Settings</h3>
                        <div class="mb-4 flex items-center">
                            <input type="checkbox" id="auto_timezone" name="auto_timezone" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" <?= $user->getAutoTimezone() ? 'checked' : '' ?>>
                            <label for="auto_timezone" class="ml-3 block text-sm font-medium text-gray-700">
                                <i class="fas fa-magic mr-2 text-gray-500"></i>Automatically detect timezone
                            </label>
                        </div>
                        
                        <div id="manual_timezone_section" class="<?= $user->getAutoTimezone() ? 'opacity-50 pointer-events-none' : '' ?>">
                            <label for="timezone" class="block text-sm font-medium text-gray-700">Select Timezone</label>
                            <select id="timezone" name="timezone" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <?php foreach ($timezones as $tz): ?>
                                    <option value="<?= $tz ?>" <?= $user->getTimezone() === $tz ? 'selected' : '' ?>><?= $tz ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="pt-5">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-save mr-2"></i>Save Language & Timezone Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out'
        });
        
        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    // Remove active state from all buttons
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'text-blue-600', 'border-b-2', 'border-blue-600');
                        btn.classList.add('text-gray-500');
                    });
                    
                    // Add active state to clicked button
                    button.classList.add('active', 'text-blue-600', 'border-b-2', 'border-blue-600');
                    button.classList.remove('text-gray-500');
                    
                    // Hide all tab contents
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });
                    
                    // Show the corresponding tab content
                    const tabId = button.id.replace('btn-', 'tab-');
                    document.getElementById(tabId).classList.remove('hidden');
                });
            });
            
            // Auto timezone toggle functionality
            const autoTimezoneCheckbox = document.getElementById('auto_timezone');
            const manualTimezoneSection = document.getElementById('manual_timezone_section');
            
            autoTimezoneCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    manualTimezoneSection.classList.add('opacity-50', 'pointer-events-none');
                } else {
                    manualTimezoneSection.classList.remove('opacity-50', 'pointer-events-none');
                }
            });
        });
    </script>
    
    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html> 