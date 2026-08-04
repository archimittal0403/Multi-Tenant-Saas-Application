<?php
$file = $_GET['file'];
?>

<script>
window.onload = function(){

    // download start
    var a = document.createElement('a');
    a.href = '<?php echo $file; ?>';
    a.download = '';
    document.body.appendChild(a);
    a.click();

    // redirect back
    setTimeout(function(){
        window.location.href='admit_card.php';
    },1000);

}
</script>