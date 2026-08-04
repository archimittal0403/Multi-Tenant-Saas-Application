<?php include('includes/auth.php');
checkRole('admin');?>
<?php include('includes/config.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
<?php include('includes/functions.php')?>
<?php
$institute_id=$_SESSION['institute_id'];
?>

<?php
if(isset($_POST['create_room'])){
    $room=$_POST['room_no'];
    $building=$_POST['building'];
    $floor=$_POST['floor'];
    $row=$_POST['row'];
    $columns=$_POST['columns'];
    $capacity=$_POST['capacity'];
    $whiteboard=$_POST['white'];
    $door=$_POST['door_pos'];
    

    $insert=mysqli_query($con,"INSERT INTO create_room (room_no,building,floor,row,columns,capacity,whiteboard,door_pos,institute_id) 
    VALUES('$room','$building','$floor','$row','$columns','$capacity','$whiteboard','$door','$institute_id')");

    if($insert){
        echo "<script>alert('Room Added Successfully')</script>";
        echo "<script>window.location.href='add_room.php'</script>";
    }
}
?>

<style>
    .card-custom {
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .form-control {
        border-radius: 10px;
    }
    .btn-custom {
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 600;
    }
    .header-title {
        font-weight: 700;
        color: #2c3e50;
    }
</style>

<div class="container-fluid mt-4">

    <div class="card card-custom p-4">
        <h3 class="header-title mb-4">🏫 Add Room Details</h3>

        <form method="post">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Room No</label>
                    <input type="text" name="room_no" class="form-control" placeholder="Enter Room Number" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Building / Block</label>
                    <input type="text" name="building" class="form-control" placeholder="Enter Building Name" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Floor</label>
                    <input type="text" name="floor" class="form-control" placeholder="e.g. 1st Floor">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Rows</label>
                    <input type="number" name="row" class="form-control" placeholder="No. of Rows">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Columns</label>
                    <input type="number" name="columns" class="form-control" placeholder="No. of Columns">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Capacity</label>
                    <input type="number" name="capacity" class="form-control" placeholder="Total Students">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Whiteboard</label>
                    <select name="white" class="form-control">
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Door Position</label>
                    <select name="door_pos" class="form-control">
                        <option value="Left">Left</option>
                        <option value="Right">Right</option>
                        <option value="Front">Front</option>
                        <option value="Back">Back</option>
                    </select>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" name="create_room" class="btn btn-success btn-custom">
                    ➕ Add Room
                </button>
            </div>

        </form>
    </div>

</div>
