
<!-- Main Footer -->
<!-- Main Footer -->
<footer class="main-footer text-sm">

  <div class="float-right d-none d-sm-inline-block">
    <b>IRISERP</b> v1.0
  </div>

  <strong>
    &copy; <?= date('Y') ?>
    
     <a href="https://www.instagram.com/archimittal7/" target="_blank">
    IRISERP
    </a>.
  </strong>

  All rights reserved.

</footer>

<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<!-- Excel dependency -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script src="dist/js/adminlte.js"></script>
 <!-- HERE WE SHOW AN ERROR WEHER WE FIND THAT THE SCRIPT WE USES LOCALLY NOT GLOBALLY -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
function initDragUpload(boxId, inputId, previewId, contentId){

  const uploadBox = document.getElementById(boxId);
  const fileInput = document.getElementById(inputId);
  const preview = document.getElementById(previewId);
  const uploadContent = document.getElementById(contentId);

  if(!uploadBox || !fileInput) return;

  uploadBox.addEventListener('click', () => fileInput.click());

  fileInput.addEventListener('change', handleFile);

  uploadBox.addEventListener('dragover', (e) => {
      e.preventDefault();
      uploadBox.style.borderColor = '#2563eb';
      uploadBox.style.background = '#dbeafe';
  });

  uploadBox.addEventListener('dragleave', () => {
      uploadBox.style.borderColor = '#3b82f6';
      uploadBox.style.background = '#f8fbff';
  });

  uploadBox.addEventListener('drop', (e) => {
      e.preventDefault();
      fileInput.files = e.dataTransfer.files;
      handleFile({ target: fileInput });
  });

  function handleFile(e){
      const file = e.target.files[0];
      if(!file) return;

      const reader = new FileReader();

      reader.onload = function(ev){
          preview.src = ev.target.result;
          preview.style.display = 'block';
          uploadContent.style.display = 'none';
      };

      reader.readAsDataURL(file);
  }
}
initDragUpload('uploadBox', 'st_image', 'previewImage', 'uploadContent');
initDragUpload('uploadBox', 'th_image', 'previewImage', 'uploadContent');
initDragUpload('uploadBox_sign', 'th_sign', 'previewImage_sign', 'uploadContent_sign');
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

  var calendarEl = document.getElementById('calendar');
  if (!calendarEl) return;

  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    events: [
      { title: 'Meeting', start: '2025-01-12' },
      { title: 'Holiday', start: '2025-01-15' }
    ]
  });

  calendar.render();
});
</script>


<script>

(function(){
  var path = window.location.href;
  //console.log(path);
  $(".nav-link").each(function () {
var href= $(this).attr('href');
//console.log(href);
 if(path === decodeURIComponent(href))
 {
   $(this).addClass('active');
   var parent = $(this).closest('.has-treeview');
   parent.addClass('menu-open');
    $(parent).find('.nav-link').first().addClass('active');

 };
  });
}());


  </script>

  <script>
    jQuery(document).ready(function(){
jQuery('#class').change(function(){
 // alert(jQuery(this).val());
  jQuery.ajax({
    url:'ajax.php',
    type:'POST',
    data: {'class_id':jQuery(this).val()},
    dataType :'json',
    success:function(response){
      
 if(response.count > 0){
   jQuery('#section-container').show();
 
 }
  

 else{
  jQuery('#section-container').hide();
 }

  jQuery('#section').html(response.options);
    }
  });
});
    })
    </script>


</body>
</html>
