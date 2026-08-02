<?php

session_start();

include('includes/config.php');
include('includes/functions.php');

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors',1);

$institute_id   = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];

$data  = [];
$count = 1;

$role        = $_POST['role'] ?? '';

$class_id    = $_POST['class_id'] ?? '';
$section_id  = $_POST['section_id'] ?? '';

$course_id   = $_POST['course_id'] ?? '';
$branch_id   = $_POST['branch_id'] ?? '';
$semester_id = $_POST['semester_id'] ?? '';

try{

    // =========================
    // BASE QUERY
    // =========================

    $sql = "
    SELECT DISTINCT 
        a.id,
        a.Name,
        a.roll_no
    FROM accounts a

    LEFT JOIN teacher_subjects ts
    ON ts.teacher_id = a.id

    WHERE a.institute_id=?
    ";

    $params = [$institute_id];
    $types  = "i";

    // =========================
    // ROLE FILTER
    // =========================

    if(!empty($role)){

        $sql .= " AND a.type=? ";

        $params[] = $role;
        $types   .= "s";

    }else{

        $sql .= "
        AND a.type IN
        (
            'teacher',
            'sports_teacher',
            'accountant',
            'librarian',
            'receptionist'
        )
        ";
    }

    // =========================
    // SCHOOL FILTERS
    // =========================

    if($institute_type == 'school'){

        if(!empty($class_id)){

            $sql .= " AND ts.class_id=? ";

            $params[] = $class_id;
            $types   .= "s";
        }

        if(!empty($section_id)){

            $sql .= " AND ts.section_id=? ";

            $params[] = $section_id;
            $types   .= "s";
        }

    }

    // =========================
    // COLLEGE FILTERS
    // =========================

    else{

        if(!empty($course_id)){

            $sql .= " AND ts.course_id=? ";

            $params[] = $course_id;
            $types   .= "s";
        }

        if(!empty($branch_id)){

            $sql .= " AND ts.branch_id=? ";

            $params[] = $branch_id;
            $types   .= "s";
        }

        if(!empty($semester_id)){

            $sql .= " AND ts.semester=? ";

            $params[] = $semester_id;
            $types   .= "s";
        }
    }

    $sql .= " ORDER BY a.id DESC ";

    // =========================
    // PREPARE QUERY
    // =========================

    $stmt = $con->prepare($sql);

    if(!$stmt){

        throw new Exception($con->error);
    }

    $stmt->bind_param($types, ...$params);

    $stmt->execute();

    $result = $stmt->get_result();

    // =========================
    // DATA LOOP
    // =========================

    while($row = $result->fetch_assoc()){

        $teacher_id = $row['id'];
        $teacher_roll=$row['roll_no'];

        $qr_btn = '
        <a 
            href="teacher_qr_test.php?roll_no='.$teacher_roll.'" 
            target="_blank"
            class="btn btn-primary btn-sm"
        >
            Generate QR
        </a>';

        $data[] = [

            'sno'        => $count++,

            'teacher_id' => !empty($row['roll_no']) 
                                ? $row['roll_no'] 
                                : $teacher_id,

            'name'       => $row['Name'],

            'qr'         => $qr_btn
        ];
    }

    echo json_encode([
        "data" => $data
    ]);

}catch(Exception $e){

    echo json_encode([
        "data"  => [],
        "error" => $e->getMessage()
    ]);
}
?>