<?php
session_start();
require_once 'config.php';

if ($_SESSION['role'] !== 'teacher') {
    header("Location: index.php");
    exit;
}

$data = get_data();
$teacher_id = $_SESSION['user_id'];


if (isset($_POST['assign_course'])) {
    $course_name = $_POST['course_name'];
    $query = "INSERT INTO courses (course_name, teacher_id) VALUES ('$course_name', $teacher_id)";
    db_query($query);
}

if (isset($_POST['update_course'])) {
    $course_id = $_POST['course_id'];
    $course_name = $_POST['course_name'];
    $query = "UPDATE courses SET course_name = '$course_name' 
              WHERE id = $course_id AND teacher_id = $teacher_id";
    db_query($query);
}

if (isset($_POST['delete_course'])) {
    $course_id = $_POST['course_id'];
    $query = "DELETE FROM courses WHERE id = $course_id AND teacher_id = $teacher_id";
    db_query($query);
}

if (isset($_POST['enroll_in_course'])) {
    $course_id = $_POST['course_id'];
    $student_id = $_POST['student_id'];
    
    // Check if course belongs to teacher
    $courses = db_select("SELECT * FROM courses WHERE id = $course_id AND teacher_id = $teacher_id");
    
    if (count($courses) > 0) {
        $course = $courses[0];
        $enrolled_students = empty($course['enrolled_students']) ? [] : 
                             explode(',', $course['enrolled_students']);
        
        // Check if student is already enrolled
        if (!in_array($student_id, $enrolled_students)) {
            $enrolled_students[] = $student_id;
            $new_enrolled = implode(',', $enrolled_students);
            
            $query = "UPDATE courses SET enrolled_students = '$new_enrolled' 
                     WHERE id = $course_id AND teacher_id = $teacher_id";
            db_query($query);
        }
    }
}

$data = get_data();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Instructor Control Panel</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            background: #eef2f3;
            color: #333;
        }
        header {
            background: linear-gradient(90deg, #0078d7, #00a1e4);
            color: #fff;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h1 {
            margin: 0;
            font-size: 24px;
        }
        header a.logout-btn {
            background: #fff;
            color: #0078d7;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
        }
        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .card {
            background: #fff;
            margin-bottom: 25px;
            border-radius: 6px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .card h2 {
            margin-top: 0;
            border-bottom: 2px solid #0078d7;
            padding-bottom: 10px;
            font-size: 20px;
        }
        .form-group {
            margin: 15px 0;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
        }
        .form-group input[type="text"],
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #bbb;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            background: #0078d7;
            border: none;
            padding: 10px 15px;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }
        .btn:hover {
            background: #005a9e;
        }
        .course-item {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 15px;
            background: #f9fbfc;
        }
        .course-actions {
            margin-top: 10px;
        }
        .course-actions form {
            display: inline-block;
            margin-right: 10px;
        }
        .enroll-section {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        .enroll-section ul {
            margin-top: 8px;
            padding-left: 20px;
        }
        .enroll-section li {
            list-style: disc;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Instructor Control Panel</h1>
        <a href="logout.php" class="logout-btn">Sign Out</a>
    </header>
    <div class="container">
        <div class="card">
            <h2>Add New Course</h2>
            <form method="post">
                <div class="form-group">
                    <label for="new-course">Course Name</label>
                    <input type="text" id="new-course" name="course_name" placeholder="Enter course title" required>
                </div>
                <button type="submit" name="assign_course" class="btn">Create Course</button>
            </form>
        </div>

        <div class="card">
            <h2>Your Courses</h2>
            <?php
            $foundAnyCourse = false;
            foreach ($data['courses'] as $course) {
                if ($course['teacher_id'] == $teacher_id) {
                    $foundAnyCourse = true;
                    echo '<div class="course-item">';
                    echo '<h3>' . $course['course_name'] . ' <small>(ID: ' . $course['id'] . ')</small></h3>';
                    
                    echo '<div class="course-actions">';
                    echo '<form method="post" class="inline-form">
                            <input type="hidden" name="course_id" value="' . $course['id'] . '">
                            <input type="text" name="course_name" placeholder="New name" required style="padding:6px; margin-right:5px;">
                            <button type="submit" name="update_course" class="btn" style="padding:6px 10px;">Update</button>
                          </form>';
                          
                    echo '<form method="post" class="inline-form">
                            <input type="hidden" name="course_id" value="' . $course['id'] . '">
                            <button type="submit" name="delete_course" class="btn" style="background:#d70022;">Delete</button>
                          </form>';
                    echo '</div>';

                    echo '<div class="enroll-section">';
                    echo '<strong>Enrolled Students:</strong>';
                    echo '<ul>';
                    $hasEnrolled = false;

                    if (!empty($course['enrolled_students'])) {
                        foreach ($course['enrolled_students'] as $student_id) {
                            $hasEnrolled = true;
                            foreach ($data['users'] as $u) {
                                if ($u['id'] == $student_id) {
                                    echo '<li>' . htmlentities($u['username']) . '</li>';
                                    break;
                                }
                            }
                        }
                    }
                    if (!$hasEnrolled) {
                        echo '<li>No students yet.</li>';
                    }
                    echo '</ul>';

                    echo '<form method="post" style="margin-top:10px;">';
                    echo '<input type="hidden" name="course_id" value="' . $course['id'] . '">';
                    echo '<div class="form-group">';
                    echo '<label>Enroll Student</label>';
                    echo '<select name="student_id" required>';
                    foreach ($data['users'] as $u) {
                        if ($u['role'] === 'student') {
                            $alreadyInCourse = in_array($u['id'], $course['enrolled_students']);
                            if (!$alreadyInCourse) {
                                echo '<option value="' . $u['id'] . '">' . htmlentities($u['username']) . '</option>';
                            }
                        }
                    }
                    echo '</select>';
                    echo '</div>';
                    echo '<button type="submit" name="enroll_in_course" class="btn">Enroll</button>';
                    echo '</form>';

                    echo '</div>';
                    echo '</div>';
                }
            }
            if (!$foundAnyCourse) {
                echo '<p>You have not created any courses yet.</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>