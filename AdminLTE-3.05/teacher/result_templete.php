<h2 style="text-align:center;">Ajay Kumar Garg Engineering College</h2>

<table border="1" width="100%" cellpadding="6">
<tr>
<td><b>Student Name</b></td>
<td><?php echo ucfirst($std_name); ?></td>

<td><b>Roll No</b></td>
<td><?php echo $std_id; ?></td>
</tr>

<tr>
<td><b>Class</b></td>
<td><?php echo $class_name; ?></td>

<td><b>Section</b></td>
<td><?php echo $section_name; ?></td>
</tr>
</table>
<br>
<table border="1" width="100%" cellpadding="6">
<tr>
<th>Subject</th>
<th>Term 1</th>
<th>Term 2</th>
<th>Total</th>
</tr>

<?php foreach($subjects as $subject){ ?>
<tr>
<td><?php echo $subject['name']; ?></td>
<td><?php echo $marks; ?></td>
<td><?php echo $marks2; ?></td>
<td><?php echo $marks+$marks2; ?></td>
</tr>
<?php } ?>

</table>

<p>Total Marks: <?php echo $count*200; ?></p>
<p>Scored Marks: <?php echo $score; ?></p>
<p>Percentage: <?php echo $percentage; ?>%</p>
<p>Grade: <?php echo $grade; ?></p>
<p>Remark: <?php echo $remark; ?></p>