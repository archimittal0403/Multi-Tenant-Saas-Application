<?php include('includes/auth.php');
checkRole('admin');?>

<?php include('includes/config.php')?>
<?php include('includes/functions.php')?>
<?php include('header.php')?>
<?php include('sidebar.php')?>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body { background:#f5f6fa; }
        .card { border-radius:12px; }
        .star {
            font-size: 30px;
            cursor: pointer;
            color: #ccc;
        }
        .star.active { color: gold; }
    </style>
<!-- <!DOCTYPE html>
<html>
<head>
    <title>ERP Feedback Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body { background:#f5f6fa; }
        .card { border-radius:12px; }
        .star {
            font-size: 30px;
            cursor: pointer;
            color: #ccc;
        }
        .star.active { color: gold; }
    </style>
</head> -->



<div class="container mt-5">

    <div class="card shadow">

        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">⭐ ERP System Feedback Form</h4>
        </div>

        <div class="card-body">

            <div class="form-group">
                <label>Your Name</label>
                <input type="text" id="name" class="form-control">
            </div>

            <div class="form-group">
                <label>Your Email</label>
                <input type="email" id="email" class="form-control" placholder="Enter ">
            </div>

            <div class="form-group">
                <label>Institute Name</label>
                <input type="text" id="institute" class="form-control">
            </div>

            <div class="form-group">
                <label>ERP Rating</label><br>

                <span class="star" data="1">★</span>
                <span class="star" data="2">★</span>
                <span class="star" data="3">★</span>
                <span class="star" data="4">★</span>
                <span class="star" data="5">★</span>

                <input type="hidden" id="rating" value="0">
            </div>

            <div class="form-group">
                <label>ERP Experience (Detail Feedback)</label>
                <textarea id="message" class="form-control" rows="5"
                placeholder="ERP kaisa chal raha hai? Kya problems hain? Kya improve hona chahiye?"></textarea>
            </div>

            <button class="btn btn-success btn-block" id="submit">
                Submit Feedback
            </button>

            <div id="msg" class="mt-3"></div>

        </div>

    </div>

</div>
<?php include('footer.php')?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

// STAR RATING
$('.star').on('click', function(){

    let val = $(this).attr('data');
    $('#rating').val(val);

    $('.star').removeClass('active');

    $('.star').each(function(){
        if($(this).attr('data') <= val){
            $(this).addClass('active');
        }
    });

});

// SUBMIT
$(document).ready(function(){

  $('#submit').on('click', function(){

      let data = {
          name: $('#name').val(),
          email: $('#email').val(),
          institute: $('#institute').val(),
          rating: $('#rating').val(),
          message: $('#message').val()
      };

      if(data.rating == 0){
          alert("Please give rating");
          return;
      }

    $.ajax({
    url: 'send_feedback.php',
    type: 'POST',
    dataType: 'json',
    data: data,

    success: function(response){

        console.log(response);

        if(response.status){
            alert("Feedback Submitted Successfully");
            window.location.href = "dashboard.php";
        }else{
            alert(response.message);
        }

    },

    error: function(xhr){
        console.log(xhr.responseText);
        alert("AJAX Error");
    }

});

  });

});
</script>

</body>
</html>