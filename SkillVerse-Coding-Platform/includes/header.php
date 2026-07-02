<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Fetch user data if logged in
$currentUser = null;
if (isset($_SESSION['user_id'])) {
    try {
        $db = getDBConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $currentUser = $stmt->fetch();
        
        // Sync XP and profile pic if modified
        if ($currentUser) {
            $_SESSION['user_xp'] = $currentUser['xp'];
            $_SESSION['user_pic'] = $currentUser['profile_pic'];
            $_SESSION['user_name'] = $currentUser['name'];
        }
    } catch (PDOException $e) {
        // Suppress or handle connection error
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? sanitize($pageTitle) . ' | ' . SITE_NAME : SITE_NAME . ' - Learn & Code'; ?></title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="SkillVerse is a premium online coding and assessment platform. Learn courses, solve challenging coding problems, and participate in tournaments.">
    <meta name="keywords" content="coding platform, online code compiler, assessment platform, PHP, JavaScript, learning management system, contest, MCQ quiz, student dashboard">
    <meta name="author" content="SkillVerse Team">
    
    <!-- Google Fonts: Outfit (Brand) & JetBrains Mono (IDE) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <!-- Tailwind CSS v3.4 CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: ['class', '[data-theme="dark"]'],
            theme: {
                extend: {
                    colors: {
                        indigo: {
                            600: '#4f46e5',
                            500: '#6366f1',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/custom.css">
</head>
<body class="bg-slate-50 dark:bg-zinc-950 text-slate-900 dark:text-slate-100">
    <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;"></div>
