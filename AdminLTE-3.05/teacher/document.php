<?php include('includes/auth.php'); ?>
<?php checkRole('teacher'); ?>
<?php include('includes/config.php')?>
<?php include('includes/functions.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php
 $institute_id=$_SESSION['institute_id'];
 $institute_code=$_SESSION['institute_code'];
 $institute_type=$_SESSION['system_type'];
 $std_id=$_SESSION['user_id'];
 ?>
 <div class="card">
    <div class="card-header bg-primary">
        <h3 class="card-title">My Documents</h3>
          <a href="feilds.php" class="btn btn-dark btn-sm ml-3">
    +Add Field
</a>
    </div>

    <div class="card-body">

        <ul class="list-group">

            <?php
     $fixed_docs = [
    'aadhaar' => 'Aadhaar Card',
    'pan' => 'PAN Card',
    'character' => 'Character Certificate',
    'tc' => 'Transfer Certificate (TC)',
    'migration' => 'Migration Certificate',
    'covid' => 'Covid Vaccination Certificate'
];

$dynamic_doc = get_dynamic_fields('document','student-document') ?? [];

$dynamic_map = [];   // 👈 IMPORTANT FIX

if (!empty($dynamic_doc)) {
    foreach ($dynamic_doc as $f) {
        $dynamic_map[$f['field_key']] = $f['field_name'];
    }
}

$all_docs = array_merge($fixed_docs, $dynamic_map);
                ?>
            
            <?php 
           if($_SERVER['REQUEST_METHOD'] == 'POST'){

    foreach($all_docs as $key => $label){

        if(isset($_POST['upload_'.$key])){

            if(!empty($_FILES[$key]['name'])){

                $upload_dir = __DIR__ . "/uploads/documents/";

                if(!is_dir($upload_dir)){
                    mkdir($upload_dir, 0777, true);
                }


                // now we will do the the background the iamge the white do get the professional image 
                if(strpos($_FILES[$key]['type'], 'image') !== false){
                $image=imagecreatefromstring(file_get_contents($_FILES[$key]['tmp_name']));
                // now create the blank page of same sizeop
                $bg=imagecreatetruecolor(imagesx($image),imagesy($image));
                $white=imagecolorallocate($bg,255,255,255);
imagefill($bg,0,0,$white);
imagecopy($bg,$image,0,0,0,0,imagesx($image),imagesy($image));
$filename = time().'.jpg';

                if(imagejpeg($bg, $upload_dir.$filename)){

             

                    update_usermeta($std_id, $key, $filename);

                    echo "<script>alert('".$label." Uploaded Successfully'); window.location.href='';</script>";
                }
                else{
                    echo "<script>alert('Upload Failed');</script>";
                }
// free memory
imagedestroy($image);
imagedestroy($bg);

            }
else{
    echo "<script>alert('Only image files allowed (JPG/PNG)');</script>";
}
        }

    }

}
           }
                ?>
                  <?php foreach($dynamic_doc as $f){
                  $dynamic_map[$f['field_key']]=$f['field_name'];

                  }
                  $all_docs= array_merge($fixed_docs,$dynamic_map);
                ?>
                <?php foreach($all_docs as $key => $label): ?>
                  
            <li class="list-group-item d-flex justify-content-between align-items-center">

                <!-- LABEL -->
                <div>
                    <b><?php echo $label; ?></b>
                </div>

                <!-- STATUS + ACTION -->
                <div>
                    <?php 
                        $file = get_usermeta($std_id, $key);

                        if($file):
                    ?>
                        <span class="badge badge-success mr-2">Uploaded</span>

                        <a href="uploads/documents/<?php echo $file; ?>" style="background-color:white: padding:10px;" target="_blank" class="btn btn-sm btn-info">
                            View
                        </a>
<a href="download.php?file=<?php echo urlencode($file); ?>" download class="btn btn-sm btn-danger">Download</a>
                    <?php else: ?>
                        <span class="badge badge-danger mr-2">Not Uploaded</span>

                        <!-- Upload Button -->
                        <button class="btn btn-sm btn-primary" data-toggle="modal" name="upload" data-target="#upload_<?php echo $key; ?>">
                            Upload
                        </button>
                    <?php endif; ?>
                </div>

            </li>


            <!-- 🔥 Upload Modal -->
            <div class="modal fade" id="upload_<?php echo $key; ?>">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <form method="post" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title">Upload <?php echo $label; ?></h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>

                            <div class="modal-body">
                                <input type="file" name="<?php echo $key; ?>" class="form-control" required>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" name="upload_<?php echo $key; ?>" class="btn btn-success">
                                    Upload
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
<?php endforeach; ?>
         

        </ul>

    </div>
</div>
<?php 
include('footer.php');
?>