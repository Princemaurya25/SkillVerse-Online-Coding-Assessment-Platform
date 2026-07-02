<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/' . $_SESSION['user_role'] . '/dashboard.php');
    exit;
}

$errors = [];
$name = '';
$email = '';
$role = 'student';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'student';

    // Server-side validation
    if (empty($name)) {
        $errors['name'] = "Name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "A valid email address is required.";
    }
    if (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters.";
    }
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }
    if (!in_array($role, ['student', 'instructor', 'admin'])) {
        $role = 'student';
    }

    if (empty($errors)) {
        try {
            $db = getDBConnection();
            
            // Check if email already exists
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors['email'] = "This email is already registered.";
            } else {
                // Insert User
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $hashed_password, $role]);
                
                // Get inserted user id
                $userId = $db->lastInsertId();
                
                // Set Session
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_role'] = $role;
                $_SESSION['user_pic'] = 'default-profile.svg';
                $_SESSION['user_xp'] = 0;
                $_SESSION['success'] = "Registration successful! Welcome to SkillVerse.";
                
                // Redirect based on role
                header('Location: ' . SITE_URL . '/' . $role . '/dashboard.php');
                exit;
            }
        } catch (PDOException $e) {
            $errors['global'] = "An error occurred. Please try again later. Details: " . $e->getMessage();
        }
    }
}

$pageTitle = "Register";
include_once __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50 dark:bg-zinc-950">
    <div class="max-w-md w-full space-y-8 glass-panel p-8 shadow-lg border border-slate-200 dark:border-zinc-800 animate-fade-in-up">
        
        <div class="text-center">
            <a href="<?php echo SITE_URL; ?>/index.php" class="inline-flex items-center gap-2 text-2xl font-extrabold no-underline mb-4">
                <span class="fs-4 bg-indigo-600 text-white rounded px-2 py-0.5"><i class="bi bi-incognito"></i></span>
                <span class="gradient-text">SkillVerse</span>
            </a>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Create your account</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-zinc-400">
                Or <a href="login.php" class="font-medium text-indigo-600 hover:text-indigo-500">sign in to your existing account</a>
            </p>
        </div>

        <?php if (isset($errors['global'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo sanitize($errors['global']); ?>
            </div>
        <?php endif; ?>

        <form class="mt-8 space-y-4" action="register.php" method="POST" id="registerForm">
            <!-- Name -->
            <div>
                <label for="name" class="form-label text-slate-700 dark:text-zinc-300 font-semibold">Full Name</label>
                <input type="text" id="name" name="name" class="form-control bg-transparent border-slate-300 dark:border-zinc-700 text-slate-900 dark:text-white <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" value="<?php echo sanitize($name); ?>" required>
                <?php if (isset($errors['name'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                <?php endif; ?>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="form-label text-slate-700 dark:text-zinc-300 font-semibold">Email address</label>
                <input type="email" id="email" name="email" class="form-control bg-transparent border-slate-300 dark:border-zinc-700 text-slate-900 dark:text-white <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" value="<?php echo sanitize($email); ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                <?php endif; ?>
            </div>

            <!-- Role Select -->
            <div>
                <label for="role" class="form-label text-slate-700 dark:text-zinc-300 font-semibold">I want to register as a:</label>
                <select id="role" name="role" class="form-select bg-transparent border-slate-300 dark:border-zinc-700 text-slate-900 dark:text-white">
                    <option value="student" <?php echo $role === 'student' ? 'selected' : ''; ?>>Student (Learn & Solve)</option>
                    <option value="instructor" <?php echo $role === 'instructor' ? 'selected' : ''; ?>>Instructor (Create Content)</option>
                    <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Admin (Platform Administrator)</option>
                </select>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="form-label text-slate-700 dark:text-zinc-300 font-semibold">Password</label>
                <input type="password" id="password" name="password" class="form-control bg-transparent border-slate-300 dark:border-zinc-700 text-slate-900 dark:text-white <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" required>
                <?php if (isset($errors['password'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                <?php endif; ?>
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="confirm_password" class="form-label text-slate-700 dark:text-zinc-300 font-semibold">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control bg-transparent border-slate-300 dark:border-zinc-700 text-slate-900 dark:text-white <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" required>
                <?php if (isset($errors['confirm_password'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['confirm_password']; ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="w-full btn btn-primary-gradient py-2 mt-4 font-bold">
                Create Account
            </button>
        </form>
    </div>
</div>

<script>
    // Client-side quick password validation
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('confirm_password').value;
        
        if (password.length < 6) {
            e.preventDefault();
            alert("Password must be at least 6 characters long.");
            return false;
        }
        
        if (password !== confirm) {
            e.preventDefault();
            alert("Passwords do not match.");
            return false;
        }
    });
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
