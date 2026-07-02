<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Verify run permissions or just let anyone run it locally
try {
    $db = getDBConnection();
    echo "Connected to the database successfully.<br>";
    
    // Clear old tables in order to avoid duplicates (safeguard)
    $db->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $db->exec("TRUNCATE TABLE certificates;");
    $db->exec("TRUNCATE TABLE assignment_submissions;");
    $db->exec("TRUNCATE TABLE assignments;");
    $db->exec("TRUNCATE TABLE forum_replies;");
    $db->exec("TRUNCATE TABLE forum_topics;");
    $db->exec("TRUNCATE TABLE contest_challenges;");
    $db->exec("TRUNCATE TABLE contests;");
    $db->exec("TRUNCATE TABLE quiz_attempts;");
    $db->exec("TRUNCATE TABLE quiz_questions;");
    $db->exec("TRUNCATE TABLE quizzes;");
    $db->exec("TRUNCATE TABLE submissions;");
    $db->exec("TRUNCATE TABLE coding_challenges;");
    $db->exec("TRUNCATE TABLE enrollments;");
    $db->exec("TRUNCATE TABLE lessons;");
    $db->exec("TRUNCATE TABLE courses;");
    $db->exec("TRUNCATE TABLE users;");
    $db->exec("SET FOREIGN_KEY_CHECKS = 1;");
    
    echo "Cleaned existing data tables.<br>";

    // 1. Insert Users
    $passwordHash = password_hash('password123', PASSWORD_BCRYPT);
    
    $users = [
        ['Admin User', 'admin@skillverse.com', $passwordHash, 'admin', 'default-profile.svg', 'Platform Administrator with complete oversight.', 0],
        ['Jane Doe', 'instructor@skillverse.com', $passwordHash, 'instructor', 'default-profile.svg', 'Senior Software Architect and Course Author.', 0],
        ['Alex Smith', 'student@skillverse.com', $passwordHash, 'student', 'default-profile.svg', 'Computer Science Undergraduate & coding enthusiast.', 350],
        ['SpongeBob SquarePants', 'student2@skillverse.com', $passwordHash, 'student', 'default-profile.svg', 'Living in a pineapple, learning to code web apps.', 120]
    ];
    
    $stmt = $db->prepare("INSERT INTO users (name, email, password, role, profile_pic, bio, xp) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($users as $user) {
        $stmt->execute($user);
    }
    echo "Seeded users successfully.<br>";
    
    // Retrieve User IDs for foreign keys
    $adminId = $db->query("SELECT id FROM users WHERE role='admin' LIMIT 1")->fetchColumn();
    $instructorId = $db->query("SELECT id FROM users WHERE role='instructor' LIMIT 1")->fetchColumn();
    $studentId = $db->query("SELECT id FROM users WHERE email='student@skillverse.com' LIMIT 1")->fetchColumn();
    
    // 2. Insert Courses
    $courses = [
        ['Mastering JavaScript ES6+', 'Gain advanced understanding of JavaScript arrays, async operations, closures, and object structures.', 'Web Development', 'js-banner.jpg', $instructorId],
        ['Introduction to PHP Programming', 'A comprehensive course detailing variables, control loops, custom functions, arrays, and MySQL database actions in Core PHP.', 'Backend Development', 'php-banner.jpg', $instructorId]
    ];
    
    $stmt = $db->prepare("INSERT INTO courses (title, description, category, banner_image, instructor_id) VALUES (?, ?, ?, ?, ?)");
    foreach ($courses as $c) {
        $stmt->execute($c);
    }
    echo "Seeded courses successfully.<br>";
    
    $jsCourseId = $db->query("SELECT id FROM courses WHERE title LIKE '%JavaScript%' LIMIT 1")->fetchColumn();
    $phpCourseId = $db->query("SELECT id FROM courses WHERE title LIKE '%PHP%' LIMIT 1")->fetchColumn();
    
    // 3. Insert Lessons
    $lessons = [
        [$jsCourseId, 'JavaScript Variables: var, let, and const', 'rich_text', NULL, NULL, '<h3>Overview</h3><p>In ES6, JavaScript introduced two new keyword types to declare variables: <code>let</code> and <code>const</code>, fixing block scope vulnerabilities inherent in <code>var</code> declarations.</p>', 15],
        [$jsCourseId, 'Promises and Async/Await Syntax', 'rich_text', NULL, NULL, '<h3>Promises vs Callbacks</h3><p>Learn how to escape callback hell by utilizing ES6 Promises and the modern async/await wrappers.</p>', 25],
        [$phpCourseId, 'Introduction to PDO Connections', 'rich_text', NULL, NULL, '<h3>Why PDO?</h3><p>PDO offers a highly secure database access layer that works with multiple engines and natively enforces prepared statements to stop SQL Injection attacks dead in their tracks.</p>', 20]
    ];
    
    $stmt = $db->prepare("INSERT INTO lessons (course_id, title, content_type, video_url, document_path, rich_text, duration_mins) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($lessons as $lesson) {
        $stmt->execute($lesson);
    }
    echo "Seeded course lessons successfully.<br>";

    // Enroll Alex in both courses
    $enrollStmt = $db->prepare("INSERT INTO enrollments (student_id, course_id, progress) VALUES (?, ?, ?)");
    $enrollStmt->execute([$studentId, $jsCourseId, 40]);
    $enrollStmt->execute([$studentId, $phpCourseId, 10]);
    echo "Seeded course enrollments successfully.<br>";

    // 4. Insert Coding Challenges
    $reverseStrTestCases = json_encode([
        ["input" => "hello", "output" => "olleh", "is_hidden" => false],
        ["input" => "world", "output" => "dlrow", "is_hidden" => false],
        ["input" => "algorithm", "output" => "mhtirogla", "is_hidden" => true]
    ]);
    
    $reverseStrBaseCode = json_encode([
        "javascript" => "function reverseString(str) {\n    // Write your code here\n    return str.split('').reverse().join('');\n}",
        "python" => "def reverse_string(s):\n    # Write your code here\n    return s[::-1]",
        "php" => "function reverseString(\$str) {\n    // Write your code here\n    return strrev(\$str);\n}"
    ]);

    $twoSumTestCases = json_encode([
        ["input" => "[2,7,11,15], 9", "output" => "[0,1]", "is_hidden" => false],
        ["input" => "[3,2,4], 6", "output" => "[1,2]", "is_hidden" => false],
        ["input" => "[3,3], 6", "output" => "[0,1]", "is_hidden" => true]
    ]);
    
    $twoSumBaseCode = json_encode([
        "javascript" => "function twoSum(nums, target) {\n    // Write your code here\n    const map = new Map();\n    for (let i = 0; i < nums.length; i++) {\n        const complement = target - nums[i];\n        if (map.has(complement)) return [map.get(complement), i];\n        map.set(nums[i], i);\n    }\n    return [];\n}",
        "python" => "def two_sum(nums, target):\n    # Write your code here\n    dct = {}\n    for i, num in enumerate(nums):\n        if target - num in dct:\n            return [dct[target - num], i]\n        dct[num] = i\n    return []",
        "php" => "function twoSum(\$nums, \$target) {\n    // Write your code here\n    \$map = [];\n    foreach (\$nums as \$i => \$num) {\n        \$complement = \$target - \$num;\n        if (array_key_exists(\$complement, \$map)) {\n            return [\$map[\$complement], \$i];\n        }\n        \$map[\$num] = \$i;\n    }\n    return [];\n}"
    ]);

    $challenges = [
        [
            'Reverse a String', 
            'Write a function that takes a string as input and returns the string reversed.', 
            'easy', 
            'Input length <= 1000', 
            'A single string.', 
            'The reversed string.', 
            'hello', 
            'olleh', 
            $reverseStrTestCases, 
            $reverseStrBaseCode, 
            100, 
            $instructorId
        ],
        [
            'Two Sum Solver', 
            'Given an array of integers `nums` and an integer `target`, return indices of the two numbers such that they add up to `target`. You may assume that each input would have exactly one solution, and you may not use the same element twice.', 
            'medium', 
            'Array size <= 10^4', 
            'An array of integers, and a target integer.', 
            'Indices of the two numbers.', 
            '[2,7,11,15], 9', 
            '[0,1]', 
            $twoSumTestCases, 
            $twoSumBaseCode, 
            200, 
            $instructorId
        ]
    ];

    $challengeStmt = $db->prepare("INSERT INTO coding_challenges (title, description, difficulty, constraints, input_format, output_format, sample_input, sample_output, test_cases, base_code, xp_reward, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($challenges as $ch) {
        $challengeStmt->execute($ch);
    }
    echo "Seeded coding challenges successfully.<br>";
    
    // 5. Seed Quizzes and MCQ Questions
    $quizStmt = $db->prepare("INSERT INTO quizzes (course_id, title, duration_mins) VALUES (?, ?, ?)");
    $quizStmt->execute([$jsCourseId, 'JavaScript Basics MCQ Quiz', 10]);
    $quizId = $db->lastInsertId();
    
    $questions = [
        [$quizId, 'Which of the following is NOT a JavaScript data type?', 'Undefined', 'Boolean', 'Float', 'Number', 'C', 10],
        [$quizId, 'What is the correct way to write a comment in JavaScript?', '// Comment', '<!-- Comment -->', '/* Comment', '# Comment', 'A', 10],
        [$quizId, 'Which method is used to add an element at the end of an array?', 'pop()', 'push()', 'shift()', 'unshift()', 'B', 10]
    ];
    
    $qStmt = $db->prepare("INSERT INTO quiz_questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($questions as $q) {
        $qStmt->execute([$q[0], $q[1], $q[2], $q[3], $q[4], $q[5], $q[6]]);
    }
    echo "Seeded quizzes and MCQ questions successfully.<br>";

    // 6. Seed Contests
    $startTime = date('Y-m-d H:i:s', strtotime('+1 hour'));
    $endTime = date('Y-m-d H:i:s', strtotime('+4 hours'));
    
    $contestStmt = $db->prepare("INSERT INTO contests (title, description, start_time, end_time, created_by) VALUES (?, ?, ?, ?, ?)");
    $contestStmt->execute(['SkillVerse Launch Tournament', 'Welcome to the inaugural coding tournament on SkillVerse! Test your skills against other students in real-time.', $startTime, $endTime, $adminId]);
    $contestId = $db->lastInsertId();
    
    // Bind challenges to contest
    $cId1 = $db->query("SELECT id FROM coding_challenges LIMIT 1")->fetchColumn();
    $cId2 = $db->query("SELECT id FROM coding_challenges LIMIT 1 OFFSET 1")->fetchColumn();
    
    $mapStmt = $db->prepare("INSERT INTO contest_challenges (contest_id, challenge_id) VALUES (?, ?)");
    if ($cId1) $mapStmt->execute([$contestId, $cId1]);
    if ($cId2) $mapStmt->execute([$contestId, $cId2]);
    echo "Seeded contest and linked challenges successfully.<br>";

    // 7. Seed Forum Topics
    $forumStmt = $db->prepare("INSERT INTO forum_topics (title, content, user_id, category) VALUES (?, ?, ?, ?)");
    $forumStmt->execute(['Why does let and var compile differently?', 'I was coding a loop in JavaScript and noticed that my index returns differently if I declare it with var vs let. Can anyone explain closures here?', $studentId, 'JavaScript']);
    $topicId = $db->lastInsertId();
    
    $replyStmt = $db->prepare("INSERT INTO forum_replies (topic_id, content, user_id) VALUES (?, ?, ?)");
    $replyStmt->execute([$topicId, 'That is because var is function-scoped while let is block-scoped. Inside a loop, var leaks its index and uses the same referenced variable, whereas let creates a binding for each loop iteration!', $instructorId]);
    echo "Seeded discussion forums successfully.<br>";

    // 8. Seed Assignments
    $assignmentStmt = $db->prepare("INSERT INTO assignments (course_id, title, description, due_date, max_points) VALUES (?, ?, ?, ?, ?)");
    $assignmentStmt->execute([$jsCourseId, 'ES6 Array Methods Implementation', 'Implement custom versions of map, filter, and reduce methods using pure vanilla loops. Save the file as a JavaScript document and upload here.', date('Y-m-d H:i:s', strtotime('+7 days')), 100]);
    echo "Seeded assignments successfully.<br>";

    echo "<h3 style='color:green;'>All mock data seeded successfully! You can now log in using Alex Smith (student@skillverse.com) or Jane Doe (instructor@skillverse.com) with password 'password123'.</h3>";
    
} catch (PDOException $e) {
    die("Seeding failed: " . $e->getMessage());
}
?>
