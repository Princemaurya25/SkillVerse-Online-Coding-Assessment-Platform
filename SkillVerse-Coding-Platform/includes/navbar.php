<nav class="navbar navbar-expand-lg border-bottom sticky-top glass-panel px-4 py-2 mb-4">
    <div class="container-fluid">
        <!-- Sidebar toggle (only visible on mobile/tablets) -->
        <?php if (isset($_SESSION['user_id'])): ?>
        <button class="btn btn-outline-secondary d-lg-none me-2" id="sidebar-toggle" aria-label="Toggle Navigation">
            <i class="bi bi-list"></i>
        </button>
        <?php endif; ?>
        
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-slate-800 dark:text-slate-100" href="<?php echo SITE_URL; ?>/index.php">
            <span class="fs-4 bg-indigo-600 text-white rounded px-2 py-0.5"><i class="bi bi-incognito"></i></span>
            <span class="gradient-text font-bold">SkillVerse</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNavbar" aria-controls="topNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="topNavbar">
            <!-- Search bar -->
            <form class="d-flex mx-auto col-lg-5 my-2 my-lg-0 position-relative" action="<?php echo SITE_URL; ?>/courses.php" method="GET">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 border-slate-300 dark:border-zinc-700 text-slate-400">
                        <i class="bi bi-search"></i>
                    </span>
                    <input class="form-control border-start-0 border-slate-300 dark:border-zinc-700 bg-transparent text-slate-700 dark:text-slate-200" type="search" placeholder="Search courses, challenges..." name="search" aria-label="Search">
                </div>
            </form>
            
            <ul class="navbar-nav ms-auto align-items-center gap-3">
                <li class="nav-item">
                    <a class="nav-link text-slate-600 dark:text-slate-300 hover:text-indigo-600" href="<?php echo SITE_URL; ?>/courses.php">Courses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-slate-600 dark:text-slate-300 hover:text-indigo-600" href="<?php echo SITE_URL; ?>/contests/index.php">Contests</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-slate-600 dark:text-slate-300 hover:text-indigo-600" href="<?php echo SITE_URL; ?>/leaderboard.php">Leaderboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-slate-600 dark:text-slate-300 hover:text-indigo-600" href="<?php echo SITE_URL; ?>/forum/index.php">Forum</a>
                </li>
                
                <!-- Dark Mode Toggler -->
                <li class="nav-item">
                    <button id="theme-toggle" class="theme-toggle-btn shadow-sm" aria-label="Toggle Theme">
                        <i class="bi bi-moon-stars-fill text-slate-500 dark:text-slate-300"></i>
                    </button>
                </li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- User is logged in -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo get_avatar_url($_SESSION['user_pic']); ?>" alt="Profile" class="rounded-circle border border-indigo-500" width="32" height="32" style="object-fit: cover;">
                            <span class="d-none d-md-inline text-slate-700 dark:text-slate-300"><?php echo sanitize($_SESSION['user_name']); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end glass-panel shadow border border-slate-200 dark:border-zinc-700 mt-2">
                            <li>
                                <div class="dropdown-header">
                                    <div class="fw-bold text-slate-800 dark:text-slate-200"><?php echo sanitize($_SESSION['user_name']); ?></div>
                                    <small class="text-indigo-600 capitalize font-semibold"><?php echo sanitize($_SESSION['user_role']); ?></small>
                                    <?php if ($_SESSION['user_role'] === 'student'): ?>
                                        <div class="mt-1"><span class="badge bg-indigo-500 text-white"><?php echo $_SESSION['user_xp']; ?> XP</span></div>
                                    <?php endif; ?>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider border-slate-200 dark:border-zinc-700"></li>
                            <li>
                                <?php 
                                $dashboard_url = SITE_URL . '/' . $_SESSION['user_role'] . '/dashboard.php';
                                ?>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?php echo $dashboard_url; ?>">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?php echo SITE_URL; ?>/profile.php">
                                    <i class="bi bi-person-fill"></i> Profile
                                </a>
                            </li>
                            <li><hr class="dropdown-divider border-slate-200 dark:border-zinc-700"></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 text-danger py-2" href="<?php echo SITE_URL; ?>/auth/logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- User is guest -->
                    <li class="nav-item">
                        <a class="btn btn-outline-primary border-slate-300 dark:border-zinc-700 text-indigo-600 hover:bg-slate-100 dark:hover:bg-zinc-800 px-4" href="<?php echo SITE_URL; ?>/auth/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary-gradient px-4" href="<?php echo SITE_URL; ?>/auth/register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
