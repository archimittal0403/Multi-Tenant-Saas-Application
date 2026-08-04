<?php

include('includes/config.php');
include('header.php');
include('sidebar.php');

if(isset($_POST['add_faq']))
{
    $roles = isset($_POST['role']) ? $_POST['role'] : [];
    $role = implode(',', $roles);

    $question = $_POST['question'];
    $keyword = $_POST['keyword'];
    $answer = $_POST['answer'];

    $insert_faq = mysqli_query($con,"
    INSERT INTO chatbot_faq
    (
        role,
        question,
        keyword,
        answer,
        institute_id
    )
    VALUES
    (
        '$role',
        '$question',
        '$keyword',
        '$answer',
        '$institute_id'
    )
    ");

    if($insert_faq)
    {
        echo "<script>alert('FAQ Added Successfully')</script>";
        echo "<script>window.location.href='Faq.php?success=1'</script>";
    }
}

?>

<style>

.faq-wrapper{
    padding:40px 20px;
    background:#f1f5f9;
    min-height:100vh;
}

.faq-card{
    max-width:750px;
    margin:auto;
    background:#ffffff;
    border-radius:28px;
    overflow:hidden;
    box-shadow:0 10px 40px rgba(0,0,0,0.08);
    border:1px solid #e2e8f0;
}

.faq-header{
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    padding:35px;
    color:#fff;
}

.faq-header h2{
    margin:0;
    font-size:32px;
    font-weight:bold;
}

.faq-header p{
    margin-top:10px;
    opacity:0.9;
    font-size:15px;
}

.faq-body{
    padding:35px;
}

.form-group{
    margin-bottom:25px;
}

.form-label{
    display:block;
    margin-bottom:10px;
    font-weight:600;
    color:#0f172a;
    font-size:15px;
}

.custom-input,
.custom-textarea,
.custom-select{
    width:100%;
    border:1px solid #cbd5e1;
    border-radius:16px;
    padding:14px 18px;
    font-size:15px;
    transition:0.3s;
    background:#f8fafc;
}

.custom-input:focus,
.custom-textarea:focus,
.custom-select:focus{
    outline:none;
    border-color:#2563eb;
    background:#fff;
    box-shadow:0 0 0 4px rgba(37,99,235,0.1);
}

.custom-textarea{
    min-height:140px;
    resize:none;
}

.multi-note{
    margin-top:8px;
    font-size:13px;
    color:#64748b;
}

.submit-btn{
    width:100%;
    border:none;
    background:linear-gradient(135deg,#16a34a,#15803d);
    color:#fff;
    padding:16px;
    border-radius:18px;
    font-size:18px;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

.submit-btn:hover{
    transform:translateY(-2px);
}

.success-box{
    background:#dcfce7;
    color:#166534;
    padding:15px 20px;
    border-radius:14px;
    margin-bottom:25px;
    font-weight:600;
}

@media(max-width:768px){

    .faq-header h2{
        font-size:24px;
    }

    .faq-body{
        padding:25px;
    }

}

</style>

<div class="faq-wrapper">

    <div class="faq-card">

        <div class="faq-header">

            <h2>Add Chatbot FAQ</h2>

            <p>
                Create intelligent FAQ responses for students, teachers and admins
            </p>

        </div>

        <div class="faq-body">

            <?php if(isset($_GET['success'])) { ?>

                <div class="success-box">
                    FAQ Added Successfully
                </div>

            <?php } ?>

            <form action="" method="POST">

                <div class="form-group">

                    <label class="form-label">
                        Select Role
                    </label>

                    <select
                    name="role[]"
                    multiple
                    class="custom-select">

                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                        <option value="admin">Admin</option>

                    </select>

                    <div class="multi-note">
                        Hold CTRL to select multiple roles
                    </div>

                </div>

                <div class="form-group">

                    <label class="form-label">
                        Question
                    </label>

                    <input
                    type="text"
                    name="question"
                    class="custom-input"
                    placeholder="Enter FAQ Question"
                    required>

                </div>

                <div class="form-group">

                    <label class="form-label">
                        Keywords
                    </label>

                    <input
                    type="text"
                    name="keyword"
                    class="custom-input"
                    placeholder="Example: admission, fees, exam"
                    required>

                </div>

                <div class="form-group">

                    <label class="form-label">
                        Answer
                    </label>

                    <textarea
                    name="answer"
                    class="custom-textarea"
                    placeholder="Enter detailed FAQ answer"
                    required></textarea>

                </div>

                <button
                type="submit"
                name="add_faq"
                class="submit-btn">

                    Add FAQ

                </button>

            </form>

        </div>

    </div>

</div>