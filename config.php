<?php 
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'class_portal';
 
function get_db_connection() {
    global $db_host, $db_user, $db_pass, $db_name;
    
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
     
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    return $conn;
}
 
function db_select($query) {
    $conn = get_db_connection();
    $result = mysqli_query($conn, $query);
    
    $data = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $data;
}
 
function db_query($query) {
    $conn = get_db_connection();
    $result = mysqli_query($conn, $query);
    $last_id = mysqli_insert_id($conn);
    mysqli_close($conn);
    return $last_id > 0 ? $last_id : $result;
}
 
function get_data() {
    $data = [];
     
    $data['users'] = db_select("SELECT * FROM users");
     
    $data['courses'] = db_select("SELECT * FROM courses"); 
     
    foreach ($data['courses'] as &$course) {
        if (!empty($course['enrolled_students'])) {
            $course['enrolled_students'] = explode(',', $course['enrolled_students']);
        } else {
            $course['enrolled_students'] = [];
        }
    }
    
    return $data;
}

function save_data($data) {
    $file = __DIR__ . '/data.json';
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}
?>