<?php include('includes/auth.php');
checkRole('student');?>

<?php include('includes/config.php')?>
<?php include('includes/functions.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>

<?php

$institute_id   = $_SESSION['institute_id'];
$institute_type = $_SESSION['system_type'];
$student_id     = $_SESSION['user_id'];

if(!$student_id){
    echo "<script>alert('Student ID Missing');</script>";
    exit;
}

// ======================
// STUDENT INFO
// ======================

$student_q = mysqli_query($con,"
SELECT Name, roll_no 
FROM accounts 
WHERE id='$student_id'
");

$student = mysqli_fetch_assoc($student_q);

$student_name = $student['Name'] ?? '';
$student_roll = $student['roll_no'] ?? '';

// ======================
// COLLEGE DATA
// ======================

$course_id = get_usermeta($student_id,'course_name');
$branch_id = get_usermeta($student_id,'branch_name');
$session   = get_usermeta($student_id,'session');
$semester  = get_usermeta($student_id,'semester');

// ======================
// SCHOOL DATA
// ======================

$class_id  = get_usermeta($student_id,'st_class');
$section_id = get_usermeta($student_id,'st_section');
$st_session = get_usermeta($student_id,'st_session');

// ======================
// ALREADY SUBMITTED CHECK
// ======================

// ======================
// ALREADY SUBMITTED CHECK
// ======================

if($institute_type == 'college'){

    $check = mysqli_query($con,"
    SELECT *
    FROM student_feedback 
    WHERE user_id='$student_id'
    AND session='$session'
    AND semester='$semester'
    AND institute_id='$institute_id'
    ");

}
else{

    $check = mysqli_query($con,"
    SELECT *
    FROM student_feedback 
    WHERE user_id='$student_id'
    AND class_id='$class_id'
    AND section_id='$section_id'
    AND academic_session='$st_session'
    AND institute_id='$institute_id'
    ");

}

if(mysqli_num_rows($check) > 0){

    $feedback_data = mysqli_fetch_assoc($check);

    $created_date = date(
        "d M Y h:i A",
        strtotime($feedback_data['created_at'])
    );

    echo "
    <script>
        alert('Feedback already submitted on: $created_date');
        window.location.href='dashboard.php';
    </script>
    ";

    exit;
}

// ======================
// SUBMIT FEEDBACK
// ======================

if(isset($_POST['submit_feedback'])){

    if($institute_type == 'college'){

        $insert_feedback = mysqli_query($con,"
        INSERT INTO student_feedback
        (
            user_id,
            course_id,
            branch_id,
            semester,
            session,
            institute_id
        )
        VALUES
        (
            '$student_id',
            '$course_id',
            '$branch_id',
            '$semester',
            '$session',
            '$institute_id'
        )
        ");

    }else{

        $insert_feedback = mysqli_query($con,"
        INSERT INTO student_feedback
        (
            user_id,
            class_id,
            section_id,
            academic_session,
            institute_id
        )
        VALUES
        (
            '$student_id',
            '$class_id',
            '$section_id',
            '$st_session',
            '$institute_id'
        )
        ");
    }

    $feedback_id = mysqli_insert_id($con);

    // ======================
    // INSERT RATINGS
    // ======================

    if(isset($_POST['rating'])){

        foreach($_POST['rating'] as $question_id => $subjects){

            foreach($subjects as $subject_id => $rating){

                $rating = intval($rating);
                $question_id = intval($question_id);
                $subject_id = intval($subject_id);

                if($rating > 0){

                    mysqli_query($con,"
                    INSERT INTO meta_feedback
                    (
                        feedback_id,
                        subject_id,
                        question_id,
                        rating,
                        institute_id
                    )
                    VALUES
                    (
                        '$feedback_id',
                        '$subject_id',
                        '$question_id',
                        '$rating',
                        '$institute_id'
                    )
                    ");
                }
            }
        }
    }

    echo "<script>alert('Feedback Submitted Successfully');</script>";
    echo "<script>window.open('dashboard.php','_self')</script>";
    exit;
}

// ======================
// QUESTIONS
// ======================

$questions = [];

if($institute_type == 'college'){

    $question_q = mysqli_query($con,"
    SELECT *
    FROM feedback_questions
    WHERE course_id='$course_id'
    AND branch_id='$branch_id'
    AND semester='$semester'
    AND session='$session'
    AND audience='students'
    AND institute_id='$institute_id'
    ");

}else{

    $question_q = mysqli_query($con,"
    SELECT *
    FROM feedback_questions
    WHERE class_id='$class_id'
    AND section_id='$section_id'
    AND session='$st_session'
    AND audience='students'
    AND institute_id='$institute_id'
    ");
}

while($row = mysqli_fetch_assoc($question_q)){

    $questions[] = [
        'id' => $row['id'],
        'question' => $row['question']
    ];
}

// ======================
// SUBJECTS
// ======================

$subjects = [];

if($institute_type == 'college'){

    $subject_q = mysqli_query($con,"
    SELECT id,title 
    FROM posts
    WHERE parent='$branch_id'
    AND institute_id='$institute_id'
    ");

}else{

    $subject_q = mysqli_query($con,"
    SELECT id,title
    FROM posts
    WHERE parent='$class_id'
    AND type='subject'
    AND institute_id='$institute_id'
    ");
}

while($sub = mysqli_fetch_assoc($subject_q)){

    $subject_id = $sub['id'];

    $teacher_names = [];

    $teacher_q = mysqli_query($con,"
    SELECT a.Name
    FROM accounts a
    JOIN teacher_subjects ts 
    ON ts.teacher_id = a.id

    WHERE ts.subject_id='$subject_id'
    AND ts.institute_id='$institute_id'
    ");

    while($t = mysqli_fetch_assoc($teacher_q)){
        $teacher_names[] = $t['Name'];
    }

    $subjects[] = [
        'id' => $subject_id,
        'title' => $sub['title'],
        'teachers' => $teacher_names
    ];
}

?>

<style>
.rating-select{
    min-width:120px;
}

.question-col{
    min-width:300px;
}

.teacher-name{
    font-size:12px;
    color:#ddd;
}
</style>

<div class="container mt-4">

    <div class="card shadow-lg border-0">

        <!-- HEADER -->
        <div class="card-header bg-primary text-white text-center">

            <h4 class="mb-1">Student Feedback Form</h4>

            <div>
                <?= $student_name ?>
            </div>

            <small>
                Roll No : <?= $student_roll ?>
            </small>

        </div>

        <!-- BODY -->
        <div class="card-body">

            <?php if(empty($questions)){ ?>

                <div class="alert alert-warning">
                    No Feedback Questions Found
                </div>

            <?php } elseif(empty($subjects)){ ?>

                <div class="alert alert-warning">
                    No Subjects Found
                </div>

            <?php } else { ?>

            <form method="POST">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover text-center align-middle">

                        <thead class="table-dark">

                            <tr>

                                <th class="question-col text-start">
                                    Questions
                                </th>

                                <?php foreach($subjects as $subject){ ?>

                                    <th>

                                        <?= $subject['title'] ?>

                                        <br>

                                        <span class="teacher-name">
                                            <?= implode(', ', $subject['teachers']) ?>
                                        </span>

                                    </th>

                                <?php } ?>

                            </tr>

                        </thead>

                        <tbody>

                        <?php foreach($questions as $index => $question){ ?>

                            <tr>

                                <td class="text-start fw-bold">

                                    <?= ($index+1).". ".$question['question'] ?>

                                </td>

                                <?php foreach($subjects as $subject){ ?>

                                <td>

                                    <select
                                    name="rating[<?= $question['id'] ?>][<?= $subject['id'] ?>]"
                                    class="form-select rating-select">

                                        <option value="">
                                            Rate
                                        </option>

                                        <?php for($i=1;$i<=5;$i++){ ?>

                                            <option value="<?= $i ?>">
                                                <?= $i ?> ⭐
                                            </option>

                                        <?php } ?>

                                    </select>

                                </td>

                                <?php } ?>

                            </tr>

                        <?php } ?>

                        </tbody>

                    </table>

                </div>

                <div class="text-end mt-4">

                    <button
                    type="submit"
                    name="submit_feedback"
                    class="btn btn-success px-5">

                        Submit Feedback

                    </button>

                </div>

            </form>

            <?php } ?>

        </div>

    </div>

</div>

<?php include('footer.php')?>