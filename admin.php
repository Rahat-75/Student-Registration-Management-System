<?php
session_start();
require_once 'config.php';
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
$data = get_data();

if (isset($_POST['insert_course'])) {
    $course_name = $_POST['course_name'];
    $teacher_id = $_POST['teacher_id'];
    $query = "INSERT INTO courses (course_name, teacher_id) VALUES ('$course_name', $teacher_id)";
    db_query($query);
}

if (isset($_POST['update_course'])) {
    $course_id = $_POST['course_id'];
    $course_name = $_POST['course_name'];
    $query = "UPDATE courses SET course_name = '$course_name' WHERE id = $course_id";
    db_query($query);
}

if (isset($_POST['delete_course'])) {
    $course_id = $_POST['course_id'];
    $query = "DELETE FROM courses WHERE id = $course_id";
    db_query($query);
}

$data = get_data();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f4f8;
            margin: 0;
            padding: 0;
        }
        .header {
            background: linear-gradient(90deg, #1e3c72, #2a5298);
            color: #fff;
            padding: 20px 40px;
            position: relative;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .header h2 {
            margin: 0;
            font-weight: 400;
            font-size: 2rem;
        }
        .logout {
            position: absolute;
            top: 20px;
            right: 40px;
            background: #fff;
            color: #1e3c72;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: background 0.3s ease;
        }
        .logout:hover {
            background: #e0e0e0;
        }
        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .box {
            background: #fff;
            margin-bottom: 20px;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .box:hover {
            transform: translateY(-3px);
        }
        .box h3 {
            margin-top: 0;
            margin-bottom: 15px;
            font-weight: 600;
            font-size: 1.6rem;
            color: #1e3c72;
        }
        input, select, button {
            display: block;
            width: 100%;
            margin: 10px 0;
            padding: 12px 15px;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            background: #1e3c72;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #16325c;
        }
        .course-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .course-item {
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .course-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Admin Dashboard</h2>
        <a href="logout.php" class="logout">Logout</a>
    </div>
    <div class="container">
        <div class="box">
            <h3>Insert Course / Section</h3>
            <form method="post">
                <input type="text" name="course_name" placeholder="Course/Section name" required>
                <select name="teacher_id" required>
                    <option value="">Select Teacher</option>
                    <?php
                    foreach ($data['users'] as $user) {
                        if (isset($user['role']) && $user['role'] === 'teacher') {
                            echo '<option value="' . $user['id'] . '">' . htmlentities($user['username']) . ' (ID: ' . $user['id'] . ')</option>';
                        }
                    }
                    ?>
                </select>
                <button type="submit" name="insert_course">Insert</button>
            </form>
        </div>

        <div class="box">
            <h3>Update Course</h3>
            <form method="post">
                <select name="course_id" required>
                    <option value="">Select Course</option>
                    <?php
                    foreach ($data['courses'] as $course) {
                        echo '<option value="' . $course['id'] . '">' . htmlentities($course['course_name']) 
                             . ' (ID: ' . $course['id'] . ')</option>';
                    }
                    ?>
                </select>
                <input type="text" name="course_name" placeholder="New course name" required>
                <button type="submit" name="update_course">Update</button>
            </form>
        </div>

        <div class="box">
            <h3>Delete Course</h3>
            <form method="post">
                <select name="course_id" required>
                    <option value="">Select Course</option>
                    <?php
                    foreach ($data['courses'] as $course) {
                        echo '<option value="' . $course['id'] . '">' . htmlentities($course['course_name']) 
                             . ' (ID: ' . $course['id'] . ')</option>';
                    }
                    ?>
                </select>
                <button type="submit" name="delete_course">Delete</button>
            </form>
        </div>

        <div class="box">
            <h3>Current Courses</h3>
            <ul class="course-list">
                <?php
                foreach ($data['courses'] as $course) {
                    echo '<li class="course-item">Course ID: ' . $course['id'] . ' — '
                         . htmlentities($course['course_name']) . ' (Teacher ID: ' . $course['teacher_id'] . ')</li>';
                }
                ?>
            </ul>
        </div>
    </div>
</body>
</html> 