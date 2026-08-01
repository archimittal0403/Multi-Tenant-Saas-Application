<?php include('includes/auth.php');
checkRole('admin');?>

<?php include('includes/config.php')?>
<?php include('includes/functions.php')?>
<?php  $institute_id=$_SESSION['institute_id'];
$institute_type=$_SESSION['system_type'];
?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
 <section class="content pt-3">
    <div class="container-fluid">

      <!-- FILTER CARD -->
      <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">🎯 Filteration</h5>
          <button type="button" id="add_new" class="btn btn-success btn-sm">
            + Add Question
          </button>
        </div>

        <div class="card-body">
          <div class="row">

            <?php if($institute_type=='college'){?>

            <div class="col-md-3 mb-3">
              <label>Course</label>
              <select id="st_course" class="form-control">
                <option value="">Select Course</option>
                <?php
                $courses = get_posts(['type'=>'course']);
                foreach($courses as $c){
                  echo "<option value='$c->id'>$c->title</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-3 mb-3">
              <label>Branch</label>
              <select id="st_branch" class="form-control">
                <option>Select Branch</option>
              </select>
            </div>

            <div class="col-md-3 mb-3">
              <label>Session</label>
              <select id="st_session" class="form-control">
                <option>Select Session</option>
                <?php
                $sessions = get_posts(['type'=>'session']);
                foreach($sessions as $s){
                  echo "<option value='$s->id'>$s->title</option>";
                }
                ?>
              </select>
            </div>

       

            <?php } else { ?>

<!-- CLASS -->
<div class="col-lg-3 mb-3">

<label>Select Class</label>

<select id="st_class" class="form-control">

<option value="">Select Class</option>

<?php

$classes = get_posts(['type'=>'class']);

foreach($classes as $c){

?>

<option value="<?= $c->id ?>">

<?= $c->title ?>

</option>

<?php } ?>

</select>

</div>

<!-- SECTION -->
<div class="col-lg-3 mb-3">

<label>Select Section</label>

<select id="st_section" class="form-control">

<option value="">Select Section</option>

</select>

</div>
 <div class="col-lg-3">
    <label for="">Academic Session</label>
 <input type="text"
       id="academic_session"
       name="session_id"

       placeholder="Enter Academic Session"
       class="form-control">
 </div>
 <?php } ?>

          <!-- BUTTON -->
          <div class="text-right mt-2">
            <button id="apply_filter" class="btn btn-primary">
              Apply Filter
            </button>
          </div>

        </div>
      </div>
      <?php
include('footer.php');
?>
<script>

$(document).ready(function(){

// ================= LOAD SUBJECT =================
function loadSubject(){

    let course_id = $('#st_course').val();
    let branch_id = $('#st_branch').val();
    let semester  = $('#st_semester').val();
    let session   = $('#st_session').val();

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

  $('#st_class').on('change',function(){

    let class_id = $(this).val();

    $.post('ajax.php',{

        action:'get_sections',
        class_id:class_id

    },function(res){

        $('#st_section').html(res.options);

    },'json');

    $.ajax({

        url:'ajax.php',
        type:'POST',
        dataType:'json',

        data:{
            action:'get_subject',
            class_id:class_id
        },

        success:function(res){

            $('#st_subject').html(res.options);
        }
    });

});


}); // document ready end

</script>
<script>
$('#apply_filter').on('click',function(){

    let url = "";

    // COLLEGE
    if($('#st_course').length){

        let course_id = $('#st_course').val();
        let branch_id = $('#st_branch').val();
        let session   = $('#st_session').val();

        if(!course_id){
            alert("Select Course");
            return;
        }

        url = "add_question.php?action=add_new&course_id="+course_id+
              "&branch_id="+branch_id+
              "&session="+session;
    }

    // SCHOOL
    else{

        let class_id = $('#st_class').val();
        let section  = $('#st_section').val();
        let session  = $('#academic_session').val();

        if(!class_id){
            alert("Select Class");
            return;
        }

        url = "add_question.php?action=add_new&class="+class_id+
              "&section="+section+
              "&session="+session;
    }

    window.location.href = url;

});
</script>
<!-- <script>
$('#add_new').on('click',function(){
  let course_id=$('#st_course').val();
  let branch_id=$('#st_branch').val();
  let semester=$('#st_semester').val();

  let session=$('#st_session').val();

  let url = "add_question.php?action=add_new&course_id="+course_id+
              "&branch_id="+branch_id+
            
              "&session="+session;
           
              window.location.href=url;
});
</script> -->