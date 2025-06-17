<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $pageDescription ?? 'Challenge yourself with creative tasks on Challangeo.io - boost productivity through gamification and team challenges.' ?>">
    <meta name="keywords" content="<?= $pageKeywords ?? 'challenge platform, team challenges, productivity gamification, remote team engagement, workplace motivation' ?>">
    <meta name="author" content="Challangeo">
    
    <!-- Robots directive -->
    <?php if (isset($noIndex) && $noIndex === true): ?>
    <meta name="robots" content="noindex, nofollow">
    <?php else: ?>
    <meta name="robots" content="index, follow">
    <?php endif; ?>
    
    <!-- Canonical link -->
    <link rel="canonical" href="https://challangeo.io<?= $canonicalPath ?? $_SERVER['REQUEST_URI'] ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?= $ogType ?? 'website' ?>">
    <meta property="og:url" content="https://challangeo.io<?= $canonicalPath ?? $_SERVER['REQUEST_URI'] ?>">
    <meta property="og:title" content="<?= $ogTitle ?? 'Challangeo | ' . ($pageTitle ?? 'Daily Micro-Challenges') ?>">
    <meta property="og:description" content="<?= $ogDescription ?? ($pageDescription ?? 'Challenge yourself with creative tasks on Challangeo.io - boost productivity through gamification and team challenges.') ?>">
    <meta property="og:image" content="<?= $ogImage ?? 'https://challangeo.io/images/challengify-logo.png' ?>">
    <meta property="og:site_name" content="Challangeo">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $twitterTitle ?? 'Challangeo | ' . ($pageTitle ?? 'Daily Micro-Challenges') ?>">
    <meta name="twitter:description" content="<?= $twitterDescription ?? ($pageDescription ?? 'Challenge yourself with creative tasks on Challangeo.io - boost productivity through gamification and team challenges.') ?>">
    <meta name="twitter:image" content="<?= $twitterImage ?? 'https://challangeo.io/images/challengify-logo.png' ?>">
    
    <!-- Structured data - default Organization -->
    <?php if (!isset($structuredData) || $structuredData !== false): ?>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "<?= $schemaType ?? 'Organization' ?>",
      "name": "Challangeo",
      "url": "https://challangeo.io",
      "logo": "https://challangeo.io/images/challengify-logo.png",
      "sameAs": [
        "https://facebook.com/challangeo",
        "https://twitter.com/challangeo",
        "https://instagram.com/challangeo"
      ]
      <?php if (isset($schemaExtraData)): ?>
      ,<?= $schemaExtraData ?>
      <?php endif; ?>
    }
    </script>
    <?php endif; ?>
    
    <title><?= $pageTitle ? 'Challangeo | ' . $pageTitle : 'Challangeo | Challenge Platform for Team Engagement' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="/images/challangify-logo/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/challangify-logo/apple-touch-icon.png">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="/css/tailwind/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    
    <?php if (isset($cspNonce) && !empty($cspNonce)): ?>
    <style nonce="<?= $cspNonce ?>">
        /* Any critical inline styles can go here */
        .hidden {
            display: none !important;
        }
    </style>
    <?php endif; ?>
</head>
<body>
    <?php include __DIR__ . '/navbar.php'; ?>
    <?php include __DIR__ . '/cookie-accept.php'; ?>
</body>
</html> 