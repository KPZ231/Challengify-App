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
 * @var \Kpzsproductions\Challengify\Services\TranslationService $translationService
 */
?>

<!DOCTYPE html>
<html lang="<?= $user->getLanguage() ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= t('account_settings', [], 'settings') ?> - Challengify</title>
    <link rel="stylesheet" href="/css/tailwind/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="/css/styles.css">
    <script src="/js/settings.js" defer></script>
    <script>
        // Ensure the correct tab is shown initially based on URL hash
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.hash) {
                const tabId = window.location.hash.substring(1);
                const tab = document.getElementById(tabId);
                const tabLink = document.querySelector(`[data-tab="${tabId}"]`);
                
                if (tab && tabLink) {
                    // Hide all tabs
                    document.querySelectorAll('.tab-content').forEach(t => t.classList.add('hidden'));
                    
                    // Show the selected tab
                    tab.classList.remove('hidden');
                    
                    // Update tab links
                    document.querySelectorAll('.tab-link').forEach(link => {
                        link.classList.remove('bg-blue-100', 'text-blue-700');
                        link.classList.add('hover:bg-gray-100');
                    });
                    
                    tabLink.classList.add('bg-blue-100', 'text-blue-700');
                    tabLink.classList.remove('hover:bg-gray-100');
                }
            }
        });
    </script>
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
                <h1 class="text-2xl font-bold text-gray-800"><?= t('account_settings', [], 'settings') ?></h1>
                <p class="text-gray-600"><?= t('manage_account_prefs', [], 'settings') ?></p>
            </div>
            
            <!-- Settings navigation tabs -->
            <div class="mb-4 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px">
                    <li class="mr-2">
                        <a href="#" data-tab="tab-notifications" class="tab-link inline-block py-4 px-4 text-sm font-medium text-center text-gray-700 rounded-t-lg border-b-2 border-transparent hover:bg-gray-100">
                            <i class="fas fa-bell mr-2"></i><?= t('tab_notifications', [], 'settings') ?>
                        </a>
                    </li>
                    <li class="mr-2">
                        <a href="#" data-tab="tab-privacy" class="tab-link inline-block py-4 px-4 text-sm font-medium text-center text-gray-700 rounded-t-lg border-b-2 border-transparent hover:bg-gray-100">
                            <i class="fas fa-lock mr-2"></i><?= t('tab_privacy', [], 'settings') ?>
                        </a>
                    </li>
                    <li class="mr-2">
                        <a href="#" data-tab="tab-language" class="tab-link inline-block py-4 px-4 text-sm font-medium text-center text-gray-700 rounded-t-lg border-b-2 border-transparent hover:bg-gray-100">
                            <i class="fas fa-globe mr-2"></i><?= t('tab_language', [], 'settings') ?>
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Notifications Tab -->
            <div id="tab-notifications" class="tab-content p-6">
                <form action="/settings/notifications" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4"><?= t('notification_channels', [], 'settings') ?></h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="checkbox" id="notification_email" name="notification_email" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" <?= $user->getNotificationEmail() ? 'checked' : '' ?>>
                                <label for="notification_email" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="fas fa-envelope mr-2 text-gray-500"></i><?= t('email_notifications', [], 'settings') ?>
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="notification_push" name="notification_push" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" <?= $user->getNotificationPush() ? 'checked' : '' ?>>
                                <label for="notification_push" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="fas fa-mobile-alt mr-2 text-gray-500"></i><?= t('push_notifications', [], 'settings') ?>
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="notification_sms" name="notification_sms" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" <?= $user->getNotificationSms() ? 'checked' : '' ?>>
                                <label for="notification_sms" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="fas fa-sms mr-2 text-gray-500"></i><?= t('sms_notifications', [], 'settings') ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4"><?= t('notification_time', [], 'settings') ?></h3>
                        <div class="flex items-center space-x-2">
                            <input type="time" id="notification_time" name="notification_time" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full sm:w-auto shadow-sm sm:text-sm border-gray-300 rounded-md" value="<?= $user->getNotificationTime() ?>">
                            <span class="text-sm text-gray-500"><?= t('time_for_daily', [], 'settings') ?></span>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4"><?= t('summary_reports', [], 'settings') ?></h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="checkbox" id="weekly_summary" name="weekly_summary" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" <?= $user->getWeeklySummary() ? 'checked' : '' ?>>
                                <label for="weekly_summary" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="fas fa-calendar-week mr-2 text-gray-500"></i><?= t('weekly_summary', [], 'settings') ?>
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="monthly_summary" name="monthly_summary" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" <?= $user->getMonthlySummary() ? 'checked' : '' ?>>
                                <label for="monthly_summary" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="fas fa-calendar-alt mr-2 text-gray-500"></i><?= t('monthly_summary', [], 'settings') ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-5">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-save mr-2"></i><?= t('save_notification_settings', [], 'settings') ?>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Privacy Tab -->
            <div id="tab-privacy" class="tab-content hidden p-6">
                <form action="/settings/privacy" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4"><?= t('profile_visibility', [], 'settings') ?></h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="radio" id="visibility_public" name="profile_visibility" value="public" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" <?= $user->getProfileVisibility() === 'public' ? 'checked' : '' ?>>
                                <label for="visibility_public" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="fas fa-globe mr-2 text-gray-500"></i><?= t('visibility_public', [], 'settings') ?>
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="visibility_followers" name="profile_visibility" value="followers" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" <?= $user->getProfileVisibility() === 'followers' ? 'checked' : '' ?>>
                                <label for="visibility_followers" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="fas fa-user-friends mr-2 text-gray-500"></i><?= t('visibility_followers', [], 'settings') ?>
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="visibility_private" name="profile_visibility" value="private" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" <?= $user->getProfileVisibility() === 'private' ? 'checked' : '' ?>>
                                <label for="visibility_private" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="fas fa-lock mr-2 text-gray-500"></i><?= t('visibility_private', [], 'settings') ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-5">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-save mr-2"></i><?= t('save_privacy_settings', [], 'settings') ?>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Language & Timezone Tab -->
            <div id="tab-language" class="tab-content hidden p-6">
                <form action="/settings/language" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4"><?= t('interface_language', [], 'settings') ?></h3>
                        <select id="language" name="language" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <?php foreach ($languages as $code => $name): ?>
                                <option value="<?= $code ?>" <?= $user->getLanguage() === $code ? 'selected' : '' ?>><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4"><?= t('timezone_settings', [], 'settings') ?></h3>
                        <div class="mb-4 flex items-center">
                            <input type="checkbox" id="auto_timezone" name="auto_timezone" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" <?= $user->getAutoTimezone() ? 'checked' : '' ?>>
                            <label for="auto_timezone" class="ml-3 block text-sm font-medium text-gray-700">
                                <i class="fas fa-magic mr-2 text-gray-500"></i><?= t('auto_timezone', [], 'settings') ?>
                            </label>
                        </div>
                        
                        <div id="manual_timezone_section" class="<?= $user->getAutoTimezone() ? 'opacity-50 pointer-events-none' : '' ?>">
                            <label for="timezone" class="block text-sm font-medium text-gray-700"><?= t('select_timezone', [], 'settings') ?></label>
                            <select id="timezone" name="timezone" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <?php foreach ($timezones as $tz): ?>
                                    <option value="<?= $tz ?>" <?= $user->getTimezone() === $tz ? 'selected' : '' ?>><?= $tz ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="pt-5">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-save mr-2"></i><?= t('save_language_settings', [], 'settings') ?>
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
        
        // Auto timezone toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const autoTimezoneCheckbox = document.getElementById('auto_timezone');
            const manualTimezoneSection = document.getElementById('manual_timezone_section');
            
            if (autoTimezoneCheckbox) {
                autoTimezoneCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        manualTimezoneSection.classList.add('opacity-50', 'pointer-events-none');
                    } else {
                        manualTimezoneSection.classList.remove('opacity-50', 'pointer-events-none');
                    }
                });
            }
            
            // Store timezone in a cookie for auto detection
            if (Intl && Intl.DateTimeFormat) {
                const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                document.cookie = `timezone=${timezone}; path=/; max-age=31536000; SameSite=Lax`;
            }
        });
    </script>
    
    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html> 