<?php include('includes/auth.php');
checkRole('student');?>

<?php include('includes/config.php')?>
<?php include('includes/functions.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php
$institute_id=$_SESSION['institute_id'];
$user_id=$_SESSION['user_id'];
$institute_type=$_SESSION['system_type'];
?>
<?php
$type=get_usermeta($user_id,'type');
$course=get_usermeta($user_id,'course_name');
$branch=get_usermeta($user_id,'branch_name');
$semester=get_usermeta($user_id,'semester');
$session=get_usermeta($user_id,'session');
$class=get_usermeta($user_id,'st_class');
$section=get_usermeta($user_id,'st_section');
$academic_session=get_usermeta($user_id,'st_session');
?>
<style>
.important-card {
  background: #ffffff;
  border-radius: 14px;
  padding: 12px;
  border-left: 4px solid #ff4d4f;
  transition: all 0.3s ease;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

/* 🔥 Hover Effect */
.important-card:hover {
  transform: translateY(-6px) scale(1.01);
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
  border-left: 4px solid #007bff;
}

/* ✨ Light gradient glow */
.important-card::before {
  content: "";
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: -100%;
  background: linear-gradient(120deg, transparent, rgba(0,123,255,0.2), transparent);
  transition: 0.5s;
}

.important-card:hover::before {
  left: 100%;
  .badge.bg-danger {
  background: linear-gradient(45deg, #ff4d4f, #ff7875);
  font-size: 10px;
  padding: 5px 8px;
  border-radius: 20px;
}
}
</style>
<style>
.notice-img {
  width: 70px;
  height: 70px;
  object-fit: cover;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.2s;
}

.notice-img:hover {
  transform: scale(1.05);
}
</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notice Board</title>
</head>
<body>
    <h4>Notice Board:</h4>
    <!-- NOTICE BOARD UI ONLY -->

<div class="row mt-4">

  <!-- LEFT : IMPORTANT -->
  <div class="col-md-4">
    <div class="card border-0 shadow-lg rounded-4">
      
      <div class="card-header text-white fw-bold" style="background: linear-gradient(135deg,#667eea,#764ba2);">
        📌 Important Notices
      </div>

      <div class="card-body">

        <?php
  
        if($institute_type=='college'){
        $select_query=mysqli_query($con,"
        SELECT * FROM notices 
        WHERE FIND_IN_SET('teachers',audience)
        AND FIND_IN_SET('$semester',semester)
        AND course='$course'
        AND branch='$branch'     
        AND institute_id='$institute_id'
        ");
         } else {
   $select_query=mysqli_query($con,"
        SELECT * FROM notices 
        WHERE FIND_IN_SET('teachers',audience)
         AND class='$class'
        AND section='$section'     
        AND institute_id='$institute_id'
        ");
       } ?>
        <?php 
        while($row=mysqli_fetch_assoc($select_query)){
            if($row['is_pinned']==1){

            $img="../admin/uploads/annoucement/".$row['file'];

            echo "
            <div class='important-card mb-3 p-3'>
                
                <div class='d-flex justify-content-between'>
                    <span class='badge bg-danger'>PINNED</span>
                    <small class='text-muted'>".$row['publish_date']."</small>
                </div>

                <h6 class='fw-bold mt-2'>".$row['title']."</h6>

                <p class='small text-muted mb-2'>".$row['description']."</p>

               
            </div>
            ";
            }
        }
        ?>

      </div>
    </div>
  </div>

  <!-- RIGHT : All Notices -->

  <div class="col-md-8">
    <div class="card shadow border-0">

```
  <div class="card-header bg-dark text-white">
    📄 All Notices
  </div>

  <div class="card-body">
   <?php 
       if($institute_type=='college'){
        $select_query=mysqli_query($con,"
        SELECT * FROM notices 
        WHERE FIND_IN_SET('teachers',audience)
        AND FIND_IN_SET('$semester',semester)
        AND course='$course'
        AND branch='$branch'     
        AND institute_id='$institute_id'
        ");
} else {
   $select_query=mysqli_query($con,"
        SELECT * FROM notices 
        WHERE FIND_IN_SET('teachers',audience)
         AND class='$class'
        AND section='$section'     
        AND institute_id='$institute_id'
        ");
       } ?>
        <?php 

          while($row=mysqli_fetch_assoc($select_query)){
            $title=$row['title'];
                
            $description=$row['description'];
            $publish=$row['publish_date'];
            $expiry=$row['expiry_date'];
        $icon = "📢"; // default
        $title_lower=strtolower($title." ".$description);
        if(strpos($title_lower, 'exam') !==false){
          $icon="📝";
        }
        elseif(strpos($title_lower, 'sport') !==false){
          $icon="🏆";
        }
       elseif(strpos($title_lower, 'music') !== false || strpos($title_lower, 'dance') !== false){
    $icon = "🎵";
}
elseif(strpos($title_lower, 'holiday') !== false || strpos($title_lower, 'festival') !== false){
    $icon = "🎉";
}
elseif(strpos($title_lower, 'scholarship') !== false){
    $icon = "🎓";
}
elseif(strpos($title_lower, 'fee') !== false){
    $icon = "💰";
}
    $file=$row['file'];
$file_path="../admin/uploads/annoucement/".$file;
$file_ext=strtolower(pathinfo($file_path,PATHINFO_EXTENSION));
if(in_array($file_ext,['jpg','jpeg','png','gif','webp'])){
$file_html="<img src='$file_path' class='notice-img' onclick=\"openImageModal(this.src)\">";
          }
          elseif($file_ext=='pdf'){
         $file_html = "<a href='$file_path' target='_blank' class='btn btn-danger btn-sm'>📄 View PDF</a>";

          }
          else{
            $file_html="no preview";
          }


echo "
    <div class='notice-row d-flex align-items-center mb-3 p-3 rounded'>

      <div class='notice-icon mr-3'>
        $icon
      </div>

      <div class='flex-grow-1'>
        <h5 class='mb-1'>$title</h5>
    <p class='small text-muted mb-2'>$description</p>
        <small class='text-muted'>
         • Publish: $publish • Expiry: $expiry
        </small>
      </div>

  <div>
$file_html

</div>

    </div>";


          }
?>
</div>
```

  </div>

</div>

<!-- STYLE -->

<style>
.notice-card {
  background: #f8f9fa;
  transition: 0.3s;
}
.notice-card:hover {
  background: #e9f2ff;
  transform: translateY(-3px);
}

.notice-row {
  background: #ffffff;
  border: 1px solid #eee;
  transition: 0.3s;
}
.notice-row:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  transform: scale(1.01);
}

.notice-icon {
  font-size: 24px;
}

.card-header.bg-gradient-primary {
  background: linear-gradient(45deg, #007bff, #00c6ff);
}
</style>
<style>
.modal-backdrop.show {
  opacity: 0.4 !important; /* 👈 black kam karo */
}

#imageModal {
  background: transparent; /* 👈 full black hata diya */
}

#modalImage {
  max-height: 90vh;
  transition: transform 0.3s ease;
  cursor: zoom-in;
}
</style>

<!-- Image Popup -->
<div class="modal fade" id="imageModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content bg-transparent border-0">

      <div class="modal-body text-center p-0">
        <img id="modalImage" class="img-fluid rounded shadow">
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
let scale = 1;
let modalInstance;

function openImageModal(src){
    const img = document.getElementById("modalImage");
    img.src = src;
    scale = 1;
    img.style.transform = "scale(1)";

    modalInstance = new bootstrap.Modal(document.getElementById('imageModal'));
    modalInstance.show();
}

// 🔥 CLICK TO ZOOM
document.getElementById("modalImage").addEventListener("click", function(){
    scale = scale === 1 ? 2 : 1;
    this.style.transform = "scale(" + scale + ")";
});

// 🔥 SCROLL TO ZOOM
document.getElementById("modalImage").addEventListener("wheel", function(e){
    e.preventDefault();

    if(e.deltaY < 0){
        scale += 0.2;
    } else {
        scale -= 0.2;
    }

    if(scale < 1) scale = 1;
    if(scale > 5) scale = 5;

    this.style.transform = "scale(" + scale + ")";
});

// 🔥 CLICK OUTSIDE TO CLOSE
document.getElementById("imageModal").addEventListener("click", function(e){
    if(e.target.id === "imageModal"){
        modalInstance.hide();
    }
});
</script>

<?php include('footer.php');?>