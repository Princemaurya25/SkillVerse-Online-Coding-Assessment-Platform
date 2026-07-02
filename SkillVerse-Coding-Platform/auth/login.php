<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/' . $_SESSION['user_role'] . '/dashboard.php');
    exit;
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email)) {
        $errors['email'] = "Email is required.";
    }
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    }

    if (empty($errors)) {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Password matches, log user in!
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_pic'] = $user['profile_pic'];
                $_SESSION['user_xp'] = $user['xp'];
                
                $_SESSION['success'] = "Welcome back, " . $user['name'] . "!";
                
                // Redirect based on role
                header('Location: ' . SITE_URL . '/' . $user['role'] . '/dashboard.php');
                exit;
            } else {
                $errors['global'] = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $errors['global'] = "An error occurred. Details: " . $e->getMessage();
        }
    }
}

$pageTitle = "Login";
include_once __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50 dark:bg-zinc-950">
    <div class="max-w-md w-full space-y-8 glass-panel p-8 shadow-lg border border-slate-200 dark:border-zinc-800 animate-fade-in-up">
        
        <div class="text-center">
            <a href="<?php echo SITE_URL; ?>/index.php" class="inline-flex items-center gap-2 text-2xl font-extrabold no-underline mb-4">
                <span class="fs-4 bg-indigo-600 text-white rounded px-2 py-0.5"><i class="bi bi-incognito"></i></span>
                <span class="gradient-text">SkillVerse</span>
            </a>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Sign in to your account</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-zinc-400">
                Or <a href="register.php" class="font-medium text-indigo-600 hover:text-indigo-500">register a new account</a>
            </p>
        </div>

        <?php if (isset($errors['global'])): ?>
            <div class="alert alert-danger text-center" role="alert">
                <i class="bi bi-exclamation-octagon-fill"></i> <?php echo sanitize($errors['global']); ?>
            </div>
        <?php endif; ?>

        <form class="mt-8 space-y-6" action="login.php" method="POST">
            <!-- Email -->
            <div>
                <label for="email" class="form-label text-slate-700 dark:text-zinc-300 font-semibold">Email address</label>
                <input type="email" id="email" name="email" class="form-control bg-transparent border-slate-300 dark:border-zinc-700 text-slate-900 dark:text-white <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" value="<?php echo sanitize($email); ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                <?php endif; ?>
            </div>

            <!-- Password -->
            <div>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label text-slate-700 dark:text-zinc-300 font-semibold mb-0">Password</label>
                </div>
                <input type="password" id="password" name="password" class="form-control bg-transparent border-slate-300 dark:border-zinc-700 text-slate-900 dark:text-white <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" required>
                <?php if (isset($errors['password'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="w-full btn btn-primary-gradient py-2 mt-4 font-bold">
                Sign In
            </button>
        </form>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
