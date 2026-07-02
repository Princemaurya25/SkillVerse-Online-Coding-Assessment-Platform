CREATE DATABASE IF NOT EXISTS skillverse DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE skillverse;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'instructor', 'admin') DEFAULT 'student',
    profile_pic VARCHAR(255) DEFAULT 'default-profile.png',
    bio TEXT NULL,
    xp INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Courses Table
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100) NOT NULL,
    banner_image VARCHAR(255) DEFAULT 'default-course.jpg',
    instructor_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Lessons Table
CREATE TABLE IF NOT EXISTS lessons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content_type ENUM('video', 'document', 'rich_text') NOT NULL,
    video_url VARCHAR(255) NULL,
    document_path VARCHAR(255) NULL,
    rich_text TEXT NULL,
    duration_mins INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Enrollments Table
CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    progress INT DEFAULT 0,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (student_id, course_id),
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Coding Challenges Table
CREATE TABLE IF NOT EXISTS coding_challenges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'easy',
    constraints TEXT NULL,
    input_format TEXT NULL,
    output_format TEXT NULL,
    sample_input TEXT NULL,
    sample_output TEXT NULL,
    test_cases JSON NOT NULL, -- Format: [{"input": "...", "output": "...", "is_hidden": false}]
    base_code JSON NOT NULL,  -- Format: {"javascript": "...", "python": "...", "php": "..."}
    xp_reward INT DEFAULT 100,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Submissions Table
CREATE TABLE IF NOT EXISTS submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    challenge_id INT NOT NULL,
    student_id INT NOT NULL,
    code TEXT NOT NULL,
    language VARCHAR(50) NOT NULL,
    status ENUM('passed', 'failed', 'compile_error') DEFAULT 'failed',
    passed_cases INT DEFAULT 0,
    total_cases INT DEFAULT 0,
    execution_time FLOAT DEFAULT 0.0,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (challenge_id) REFERENCES coding_challenges(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Quizzes Table
CREATE TABLE IF NOT EXISTS quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    duration_mins INT DEFAULT 15,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Quiz Questions Table
CREATE TABLE IF NOT EXISTS quiz_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    question_text TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_option CHAR(1) NOT NULL, -- 'A', 'B', 'C', or 'D'
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Quiz Attempts Table
CREATE TABLE IF NOT EXISTS quiz_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    student_id INT NOT NULL,
    score INT NOT NULL,
    total_questions INT NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Contests Table
CREATE TABLE IF NOT EXISTS contests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Contest Challenges Map Table
CREATE TABLE IF NOT EXISTS contest_challenges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contest_id INT NOT NULL,
    challenge_id INT NOT NULL,
    FOREIGN KEY (contest_id) REFERENCES contests(id) ON DELETE CASCADE,
    FOREIGN KEY (challenge_id) REFERENCES coding_challenges(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Forum Topics Table
CREATE TABLE IF NOT EXISTS forum_topics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    user_id INT NOT NULL,
    category VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Forum Replies Table
CREATE TABLE IF NOT EXISTS forum_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    topic_id INT NOT NULL,
    content TEXT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (topic_id) REFERENCES forum_topics(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Assignments Table
CREATE TABLE IF NOT EXISTS assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    file_path VARCHAR(255) DEFAULT NULL, -- PDF or instruction file
    due_date DATETIME NOT NULL,
    max_points INT DEFAULT 100,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Assignment Submissions Table
CREATE TABLE IF NOT EXISTS assignment_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    student_id INT NOT NULL,
    submission_file VARCHAR(255) NOT NULL,
    grade INT DEFAULT NULL,
    feedback TEXT DEFAULT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (assignment_id, student_id),
    FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Certificates Table
CREATE TABLE IF NOT EXISTS certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    certificate_code VARCHAR(100) UNIQUE NOT NULL,
    issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;
