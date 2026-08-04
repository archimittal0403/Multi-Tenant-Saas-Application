 <?php include('includes/auth.php'); 
checkRole('admin'); ?>
<?php include('includes/config.php'); ?>
<?php include('includes/functions.php'); ?>
<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>


<?php
$institute_id = $_SESSION['institute_id'];
    $institute_type=$_SESSION['system_type'];

    $class_id=$_GET['class'] ?? '';
    $section=$_GET['section'] ?? '';
    $academic_session=$_GET['academic_session'] ?? '';
$course   = $_GET['course'] ?? '';
$branch   = $_GET['branch'] ?? '';
$session  = $_GET['session'] ?? '';
$semester = $_GET['semester'] ?? '';
$subject_id = $_GET['subject_id'] ?? '';
$date     = $_GET['attendance_date'] ?? date('Y-m-d');
?>
<?php

if($institute_type == 'college'){

$students = mysqli_query($con,"

SELECT a.id, a.Name, a.roll_no
FROM accounts a

JOIN usermeta c 
ON c.user_id=a.id 
AND c.meta_key='course_name' 
AND c.meta_value='$course'

JOIN usermeta b 
ON b.user_id=a.id 
AND b.meta_key='branch_name' 
AND b.meta_value='$branch'

JOIN usermeta s 
ON s.user_id=a.id 
AND s.meta_key='session' 
AND s.meta_value='$session'

JOIN usermeta sem 
ON sem.user_id=a.id 
AND sem.meta_key='semester' 
AND sem.meta_value='$semester'

WHERE a.type='student'
AND a.institute_id='$institute_id'

");

}else{

$students = mysqli_query($con,"

SELECT a.id, a.Name, a.roll_no
FROM accounts a

JOIN usermeta c 
ON c.user_id=a.id 
AND c.meta_key='st_class' 
AND c.meta_value='$class_id'

JOIN usermeta s 
ON s.user_id=a.id 
AND s.meta_key='st_section' 
AND s.meta_value='$section'

WHERE a.type='student'
AND a.institute_id='$institute_id'

");

}

?>



<?php
if ($_GET['action'] ?? 'add-new' == 'add-new') { ?>

<div class="card">
  <div class="card-body">
   
   <h4><u>Mark the Attendance:-</u></h4>
   <input type="hidden" name="action" value="add-new">

<style>
.filter-advanced{
    background:#fff;
    border-radius:16px;
    padding:18px;
    box-shadow:0 4px 20px rgba(0,0,0,0.08);
}

.filter-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:10px;
}

.filter-title{
    font-size:18px;
    font-weight:600;
}

.btn-wrap{
    display:flex;
    flex-wrap:wrap;
    gap:8px;
}

.btn-wrap a{
    white-space:nowrap;
}

.search-box{
    width:220px;
    height:38px;
    border-radius:10px;
    border:1px solid #ddd;
    padding:5px 10px;
}

/* GRID */
.filter-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:12px;
    margin-top:15px;
}

.input-modern{
    height:40px;
    border-radius:10px;
    border:1px solid #ddd;
    padding:6px 10px;
    font-size:13px;
}

/* ACTION BUTTONS */
.filter-actions{
    display:flex;
    justify-content:flex-end;
    gap:10px;
    margin-top:15px;
}

/* TABLE BUTTON */
.toggle-attendance{
    width:100%;
}

/* MOBILE RESPONSIVE */
@media(max-width:768px){

    .filter-top{
        flex-direction:column;
        align-items:stretch;
    }

    .btn-wrap a{
        width:100%;
        text-align:center;
    }

    .search-box{
        width:100%;
    }

    .filter-actions{
        flex-direction:column;
    }

    .filter-actions button,
    .filter-actions a{
        width:100%;
    }
}
</style>

<div class="filter-top">
    <div class="filter-title">📊 Attendance Filters</div>

    <div class="btn-wrap">
        <a href="attendance.php?action=add-new" class="btn btn-primary btn-sm">
            Mark Attendance
        </a>

        <?php if($institute_type == 'college'){ ?>
            <a href="updateAttendance.php?course=<?= $course ?>&branch=<?= $branch ?>&session=<?= $session ?>&semester=<?= $semester ?>&subject_id=<?= $subject_id ?>&attendance_date=<?= $date ?>"
               class="btn btn-warning btn-sm">
               Update Attendance
            </a>

            <a href="semesterreport.php?course=<?= $course ?>&branch=<?= $branch ?>&session=<?= $session ?>&semester=<?= $semester ?>&subject_id=<?= $subject_id ?>"
               class="btn btn-success btn-sm">
               Semester Report
            </a>
        <?php } else { ?>
            <a href="updateAttendance.php?class=<?= $class_id ?>&section=<?= $section ?>&academic_session=<?= $academic_session ?>&subject_id=<?= $subject_id ?>&attendance_date=<?= $date ?>"
               class="btn btn-warning btn-sm">
               Update Attendance
            </a>

            <a href="semesterreport.php?class=<?= $class_id ?>&section=<?= $section ?>&session=<?= $session ?>&subject_id=<?= $subject_id ?>"
               class="btn btn-success btn-sm">
               Semester Report
            </a>
        <?php } ?>
    </div>

<input type="text" id="studentSearch" placeholder="🔍 Search student..." class="search-box">
</div>

    <form method="get" action="attendance.php">
        <input type="hidden" name="action" value="add-new">

        <div class="filter-grid">
<?php if($institute_type == 'college'){ ?>
        
            <select name="course" id="st_course" class="form-control">
<option value="">Select</option>
<?php
$courses = get_posts(['type'=>'course']);
foreach($courses as $c){
$sel = ($course==$c->id)?'selected':'';
echo "<option value='$c->id' $sel>$c->title</option>";
}
?>
</select>
         <select name="branch" id="st_branch" class="input-modern">
<option value="">Select Branch</option>
</select>

<select name="session" id="st_session" class="form-control">
<option value="">Select</option>
<?php
$sessions = get_posts(['type'=>'session','institute_id'=>$institute_id]);
foreach($sessions as $s){
$sel = ($session==$s->id)?'selected':'';
echo "<option value='$s->id' $sel>$s->title</option>";
}
?>
</select>
          <select name="semester" id="st_semester" class="input-modern">
<option value="">Select Semester</option>
</select>

          <select name="subject_id" id="st_subject" class="input-modern">
<option value="">Select Subject</option>
</select>
<input type="date"
       name="attendance_date"
       value="<?= $date ?>"
       max="<?= date('Y-m-d') ?>"
       class="input-modern">


        </div>

        <div class="filter-actions">
            <button class="btn btn-primary btn-modern">Apply</button>
            <a href="attendance.php?action=add-new" class="btn btn-light btn-modern">Reset</a>
        </div>

<?php }else{ ?>


<select name="class" id="st_class" class="form-control">

<option value="">Select Class</option>

<?php

$classes = get_posts(['type'=>'class']);

foreach($classes as $c){
?>

<option value="<?=$c->id?>" <?=($class_id==$c->id?'selected':'')?>>
<?=$c->title?>
</option>

<?php } ?>

</select>

<select name="section" id="st_section" class="form-control">
<option value="">Select Section</option>
</select>

<input
type="text"
id="academic_session"
name="academic_session"
value="<?= $academic_session ?>"
class="form-control"
placeholder="Enter Academic Session">


          <select name="subject_id" id="st_subject" class="input-modern">
<option value="">Select Subject</option>
</select>
<input type="date"
       name="attendance_date"
       value="<?= $date ?>"
       max="<?= date('Y-m-d') ?>"
       class="input-modern">

          <div class="filter-actions">
            <button class="btn btn-primary btn-modern">Apply</button>
            <a href="attendance.php?action=add-new" class="btn btn-light btn-modern">Reset</a>
        </div>
       <?php } ?>
    </form>
</div>
   
          <div class="table-responsive">
         <table class="table table-bordered text-center">
<thead class="bg-dark text-white">
<tr>
  <th>S.No</th>
<th>Roll No</th>
<th>Name</th>
<th>Attendance</th>
</tr>
</thead>

<tbody>
<?php 
$i=1;
while($stu = mysqli_fetch_assoc($students)){ ?>
<tr class="student-row">
  <td><?= $i++ ?></td>
<td><?= $stu['roll_no'] ?></td>
<td><?= $stu['Name'] ?></td>

<td>

<!-- Toggle Button -->
<button 
class="btn btn-sm toggle-attendance btn-success"
data-id="<?= $stu['id'] ?>"
data-status="present">
Present
</button>

</td>
</tr>

<?php } ?>
</tbody>
</table>

<button id="saveAttendance" class="btn btn-primary mt-3">
Save Attendance
</button>

                </div>
              </div>
            </div>

          <?php } ?>

          <?php include('footer.php');?>
<script>
          $(document).on('click', '.toggle-attendance', function(){

    let btn = $(this);
    let current = btn.data('status');

    if(current === 'present'){
        btn.removeClass('btn-success')
           .addClass('btn-danger')
           .text('Absent')
           .data('status','absent');
    } else {
        btn.removeClass('btn-danger')
           .addClass('btn-success')
           .text('Present')
           .data('status','present');
    }

});
</script>
<script>
    $(document).ready(function(){

    $('#studentSearch').on('keyup', function(){

        let value = $(this).val().toLowerCase();

        $('.student-row').filter(function(){

            $(this).toggle(
                $(this).text().toLowerCase().indexOf(value) > -1
            );

        });

    });

});
</script>
<script>
$(document).ready(function(){

// AUTO LOAD IF VALUES ALREADY SELECTED
let selected_branch   = "<?= $branch ?>";
let selected_semester = "<?= $semester ?>";
let selected_subject  = "<?= $subject_id ?>";


if($('#st_class').val() != ''){

    // LOAD SUBJECT AUTO
    $.ajax({
        url:'ajax.php',
        type:'POST',
        dataType:'json',
        data:{
            action:'get_subject',
            class_id: $('#st_class').val()
        },
        success:function(res){

            if(res.status){

                $('#st_subject').html(res.options);

                $('#st_subject').val(selected_subject);

            }
        }
    });
}
if($('#st_course').val() !== ''){

    // LOAD BRANCH
    $.post('ajax.php',{
        action:'get_branch',
        course_id: $('#st_course').val()
    },function(res){

        $('#st_branch').html(res.options);

        $('#st_branch').val(selected_branch);

        // LOAD SEMESTER
        $.post('ajax.php',{
            action:'get_semester',
            course_id: $('#st_course').val()
        },function(res2){

            $('#st_semester').html(res2.options);

            $('#st_semester').val(selected_semester);

            // LOAD SUBJECT
            loadSubject();

            setTimeout(function(){
                $('#st_subject').val(selected_subject);
            },500);

        },'json');

    },'json');
}
$('#st_class').on('change', function(){

    let class_id = $(this).val();

    loadSections(class_id);

});
let selected_section = "<?= $section ?>";

if($('#st_class').val()!=''){
    loadSections($('#st_class').val(), selected_section);
}
$('#st_class').on('change', function(){

    let class_id = $(this).val();

    // LOAD SECTION
    loadSections(class_id);

    // LOAD SUBJECT
    if(class_id!=''){

        $('#st_subject').html('<option>Loading...</option>');

        $.ajax({
            url:'ajax.php',
            type:'POST',
            dataType:'json',
            data:{
                action:'get_subject',
                class_id:class_id
            },
            success:function(res){

                if(res.status){

                    $('#st_subject').html(res.options);

                }else{

                    $('#st_subject').html('<option>No Subject Found</option>');
                }
            }
        });

    }

});
// ================= LOAD SUBJECT =================
function loadSubject(){

    let course_id = $('#st_course').val();
    let branch_id = $('#st_branch').val();
    let semester  = $('#st_semester').val();
    let session   = $('#st_session').val();
    let subject =$('#st_subject').val();
   // let exam=$('#st_exam').val();

    if(course_id && branch_id && semester && session){

        $('#st_subject').html('<option>Loading...</option>');

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_subject',
                course_id: course_id,
                branch_id: branch_id,
                semester: semester,
                session: session
            },
            success: function(res){
                if(res.status){
                    $('#st_subject').html(res.options);
                } else {
                    $('#st_subject').html('<option>No Subject Found</option>');
                }
            }
        });
    }
        if(course_id && branch_id && semester && session){

     //   $('#st_exam').html('<option>Loading...</option>');

    $.ajax({
    url: 'ajax.php',
    type: 'POST',
    dataType: 'json',
    data: {
        action: 'get_exam',
        course_id: course_id,
        branch_id: branch_id,
        semester_id: semester,
        session_id: session
    },
  success: function(res){

    if(res.status){

        // exam dropdown
        $('#st_exam').html(
            '<option value="">Select Exam</option>' +
            '<option value="'+res.exam_id+'">'+res.exam_type+'</option>'
        );

        // 🔥 DATE RANGE GENERATE
        let start = new Date(res.start_date);
        let end   = new Date(res.end_date);

        let options = '<option value="">Select Exam Date</option>';

        while(start <= end){

            let year  = start.getFullYear();
            let month = String(start.getMonth() + 1).padStart(2, '0');
            let day   = String(start.getDate()).padStart(2, '0');

            let formatted = `${year}-${month}-${day}`;

            options += `<option value="${formatted}">${formatted}</option>`;

            start.setDate(start.getDate() + 1);
        }

        // 🔥 IMPORTANT: dropdown me set karo
      //  $('#exam_date').html(options);

    } else {

        $('#st_exam').html('<option>No Exam Found</option>');
        $('#exam_date').html('<option>No Dates</option>');
    }
}
});
    }
}


// ================= COURSE CHANGE =================
$('#st_course').on('change', function(){

    let course_id = $(this).val();

    $('#st_branch').html('<option>Loading...</option>');
    $('#st_semester').html('<option>Loading...</option>');
  

    $.post('ajax.php',{
        action:'get_branch',
        course_id:course_id
    },function(res){
        $('#st_branch').html(res.options);
    },'json');

    $.post('ajax.php',{
        action:'get_semester',
        course_id:course_id
    },function(res){
        $('#st_semester').html(res.options);
    },'json');

    // 🔥 IMPORTANT: reset subject properly

}); // ✅ YE MISSING THA
function loadSections(class_id, selected_section=''){

        $.post('ajax.php',{

            action:'get_sections',
            class_id:class_id

        },function(res){

            $('#st_section').html(res.options);

            if(selected_section!=''){
                $('#st_section').val(selected_section);
            }

        },'json');

    }


// ================= AUTO SUBJECT LOAD =================
$('#st_branch, #st_semester, #st_session').on('change', function(){
    loadSubject();
});

}); // document ready end

</script>
<script>
$('#saveAttendance').click(function(){

    let btn = $(this);

    btn.prop('disabled', true).text('Saving...');

    let attendance = [];

    $('.toggle-attendance').each(function(){

        attendance.push({
            user_id: $(this).data('id'),
            status: $(this).data('status')
        });

    });

    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        dataType: 'json',
    data: {
    action: 'save_attendance',
class_id:$('#st_class').val(),
section_id:$('#st_section').val(),
academic_session:$('input[name="academic_session"]').val(),
    course_id: $('#st_course').val(),
    branch_id: $('#st_branch').val(),
    session_id: $('#st_session').val(),
    semester: $('#st_semester').val(),
    subject_id: $('#st_subject').val(),

  attendance_date: $('input[name="attendance_date"]').val(),

        institute_id: '<?= $institute_id ?>',

    attendance: attendance
},
success: function(res){

    alert(res.message);

    if(res.status){

        location.href = "attendance.php?action=add-new";

    } else {

        btn.prop('disabled', false)
           .text('Save Attendance');
    }

},

        error: function(){

            alert("Server Error");

            btn.prop('disabled', false).text('Save Attendance');
        }
    });

});
</script>