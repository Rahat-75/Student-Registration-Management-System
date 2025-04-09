<?php
session_start();
require_once 'config.php';

if ($_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

$data = get_data();
$student_id = $_SESSION['user_id'];


if (isset($_POST['enroll_course'])) {
    $course_id = $_POST['course_id'];
    
    // Get current course info
    $courses = db_select("SELECT * FROM courses WHERE id = $course_id");
    
    if (count($courses) > 0) {
        $course = $courses[0];
        $enrolled_students = empty($course['enrolled_students']) ? [] : 
                             explode(',', $course['enrolled_students']);
        
        // Check if already enrolled
        if (!in_array($student_id, $enrolled_students)) {
            $enrolled_students[] = $student_id;
            $new_enrolled = implode(',', $enrolled_students);
            
            $query = "UPDATE courses SET enrolled_students = '$new_enrolled' WHERE id = $course_id";
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
    <title>Student Dashboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background: #f7f9fc;
            margin: 0;
            padding: 0;
        }
        .header {
            background: linear-gradient(90deg, #0078d7, #00a1e4);
            color: #ffffff;
            padding: 25px 40px;
            position: relative;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header h2 {
            margin: 0;
            font-weight: 400;
            font-size: 1.8rem;
        }
        .logout {
            position: absolute;
            top: 25px;
            right: 40px;
            background: #ffffff;
            color: #0078d7;
            padding: 8px 14px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: background 0.3s;
        }
        .logout:hover {
            background: #e0e0e0;
        }
        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 30px;
        }
        h3 {
            margin-top: 0;
            font-weight: 600;
            font-size: 1.8rem;
            color: #333;
        }
        .course-card {
            background: #ffffff;
            border-radius: 8px;
            padding: 20px 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        .course-card:hover {
            transform: translateY(-3px);
        }
        .course-header {
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 12px;
            color: #0078d7;
        }
        .course-action {
            margin-top: 15px;
        }
        button {
            background: #0078d7;
            color: #ffffff;
            border: none;
            padding: 10px 16px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: 600;
            transition: background 0.3s;
        }
        button:hover {
            background: #005a9e;
        }
        .enrolled-badge {
            color: #2ecc71;
            font-weight: 600;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Student Dashboard</h2>
        <a href="logout.php" class="logout">Logout</a>
    </div>
    <div class="container">
        <h3>Available Courses</h3>
        <?php
        foreach ($data['courses'] as $course) {
            echo '<div class="course-card">';
            echo '<div class="course-header">Course ID: ' . $course['id'] . ' — ' . $course['course_name'] . '</div>';
            
            $enrolled = in_array($student_id, $course['enrolled_students']);
            
            echo '<div class="course-action">';
            if (!$enrolled) {
                echo '<form method="post" style="display:inline-block; margin-right:10px;">
                        <input type="hidden" name="course_id" value="' . $course['id'] . '">
                        <button type="submit" name="enroll_course">Enroll</button>
                      </form>';
            } else {
                echo '<span class="enrolled-badge">[Enrolled]</span>';
            }
            echo '</div>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html> 