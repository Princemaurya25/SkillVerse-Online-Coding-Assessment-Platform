<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'student';
?>
<aside class="sidebar d-flex flex-column justify-content-between">
    <div>
        <div class="d-flex align-items-center justify-content-between mb-4">
            <a class="d-flex align-items-center gap-2 fw-bold text-slate-800 dark:text-slate-100 no-underline" href="<?php echo SITE_URL; ?>/index.php">
                <span class="fs-4 bg-indigo-600 text-white rounded px-2 py-0.5"><i class="bi bi-incognito"></i></span>
                <span class="gradient-text font-bold">SkillVerse</span>
            </a>
        </div>
        
        <nav class="nav flex-column">
            <!-- Universal Dashboard Link -->
            <a class="nav-link" href="<?php echo SITE_URL; ?>/<?php echo $userRole; ?>/dashboard.php">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>

            <!-- Role-Specific Sidebar Items -->
            <?php if ($userRole === 'admin'): ?>
                <!-- ADMIN NAV -->
                <div class="px-3 my-2 text-uppercase text-xs tracking-wider text-slate-400 dark:text-zinc-500 font-semibold">Management</div>
                <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/courses.php">
                    <i class="bi bi-journal-bookmark-fill"></i>
                    <span>Courses</span>
                </a>
                <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/challenges.php">
                    <i class="bi bi-code-square"></i>
                    <span>Challenges</span>
                </a>
                <a class="nav-link" href="<?php echo SITE_URL; ?>/leaderboard.php">
                    <i class="bi bi-trophy-fill"></i>
                    <span>Leaderboard</span>
                </a>
                
            <?php elseif ($userRole === 'instructor'): ?>
                <!-- INSTRUCTOR NAV -->
                <div class="px-3 my-2 text-uppercase text-xs tracking-wider text-slate-400 dark:text-zinc-500 font-semibold font-sans">Content Creator</div>
                <a class="nav-link" href="<?php echo SITE_URL; ?>/instructor/add-course.php">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Create Course</span>
                </a>
                <a class="nav-link" href="<?php echo SITE_URL; ?>/instructor/add-lesson.php">
                    <i class="bi bi-file-earmark-plus-fill"></i>
                    <span>Add Lesson</span>
                </a>
                <a class="nav-link" href="<?php echo SITE_URL; ?>/instructor/add-quiz.php">
                    <i class="bi bi-patch-question-fill"></i>
                    <span>Create Quiz</span>
                </a>
                <a class="nav-link" href="<?php echo SITE_URL; ?>/instructor/add-challenge.php">
                    <i class="bi bi-braces-asterisk"></i>
                    <span>Create Challenge</span>
                </a>
                
            <?php else: ?>
                <!-- STUDENT NAV -->
                <div class="px-3 my-2 text-uppercase text-xs tracking-wider text-slate-400 dark:text-zinc-500 font-semibold">Learn & Practice</div>
                <a class="nav-link" href="<?php echo SITE_URL; ?>/courses.php">
                    <i class="bi bi-journal-text"></i>
                    <span>All Courses</span>
                </a>
                <a class="nav-link" href="<?php echo SITE_URL; ?>/contests/index.php">
                    <i class="bi bi-award-fill"></i>
                    <span>Contests</span>
                </a>
                <a class="nav-link" href="<?php echo SITE_URL; ?>/leaderboard.php">
                    <i class="bi bi-trophy-fill"></i>
                    <span>Leaderboard</span>
                </a>
                <a class="nav-link" href="<?php echo SITE_URL; ?>/forum/index.php">
                    <i class="bi bi-chat-text-fill"></i>
                    <span>Forum</span>
                </a>
            <?php endif; ?>
            
            <div class="px-3 my-2 text-uppercase text-xs tracking-wider text-slate-400 dark:text-zinc-500 font-semibold">User</div>
            <a class="nav-link" href="<?php echo SITE_URL; ?>/profile.php">
                <i class="bi bi-person-circle"></i>
                <span>My Profile</span>
            </a>
        </nav>
    </div>
    
    <div class="border-t border-slate-100 dark:border-zinc-800 pt-3">
        <a class="nav-link text-danger d-flex align-items-center gap-2 hover:bg-red-50 dark:hover:bg-red-950/20" href="<?php echo SITE_URL; ?>/auth/logout.php">
            <i class="bi bi-box-arrow-left"></i>
            <span>Sign Out</span>
        </a>
    </div>
</aside>
