<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Challenge yourself daily with micro-challenges designed to spark creativity and improve your coding skills">
    <meta name="keywords" content="coding challenges, programming practice, daily challenges, micro-challenges, coding skills, developer challenges">
    <meta name="author" content="Challengify">
    <meta property="og:title" content="Challengify | <?= $pageTitle ?? 'Daily Micro-Challenges' ?>">
    <meta property="og:description" content="Challenge yourself daily with micro-challenges designed to spark creativity and improve your coding skills">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Challengify">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Challengify | <?= $pageTitle ?? 'Daily Micro-Challenges' ?>">
    <meta name="twitter:description" content="Challenge yourself daily with micro-challenges designed to spark creativity and improve your coding skills">
    
    <title>Challengify | <?= $pageTitle ?? 'Daily Micro-Challenges' ?></title>
    
    <link rel="stylesheet" href="/css/tailwind/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="shortcut icon" href="/images/challengify-logo.png" type="image/x-icon">
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