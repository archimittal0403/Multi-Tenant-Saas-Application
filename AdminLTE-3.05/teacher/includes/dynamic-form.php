
<?php
function render_dynamic_form($form_type, $teacher_id = ''){
    global $con;

    if(empty($form_type)) return;

    $inst_id = $_SESSION['institute_id'] ?? 0;
    if(!$inst_id) return;

    $fields = get_dynamic_fields($form_type);

    if(!$fields || mysqli_num_rows($fields)==0){
        echo "<div class='col-12'><p>No extra fields</p></div>";
        return;
    }

    while($field = mysqli_fetch_assoc($fields)){

        $value = '';
        if(!empty($teacher_id)){
            $value = get_usermeta($teacher_id, $field['field_key']);
        }

        $field_key = htmlspecialchars($field['field_key']);
        $field_name = htmlspecialchars($field['field_name']);
        $field_type = $field['field_type'];

        echo '<div class="col-lg-4">';
        echo '<div class="form-group">';
        echo "<label>$field_name</label>";

        // TEXT FIELD
        if($field_type == 'text'){
     echo '
<div class="col-lg-12">
    <input type="text"
    name="'.$field_key.'"
    value="'.htmlspecialchars($value).'"
    class="form-control"
    style="width:100%;">
</div>';
        }

        // SELECT FIELD
      elseif($field_type == 'select'){
    echo "<select name='$field_key' class='form-control'>";
 echo "<option value=''>--Select {$field['field_name']} --</option>";
    $source = $field['source'] ?? '';

    if(!empty($source)){
        $select = mysqli_query($con, "SELECT title, id FROM `posts` WHERE type='$source' AND institute_id=$inst_id");
        while($select_fetch = mysqli_fetch_assoc($select)){
            $selected = ($value == $select_fetch['id']) ? 'selected' : '';
      
            echo 
                 
            "<option value='{$select_fetch['id']}' $selected>{$select_fetch['title']}</option>";
        }
    } else {
        $options = !empty($field['options']) ? explode(',', $field['options']) : [];
        foreach($options as $opt){
            $opt = trim($opt);
            $selected = ($value == $opt) ? 'selected' : '';
            echo "<option value='$opt' $selected>$opt</option>";
        }
    }

    echo "</select>";
}
        echo '</div></div>';
    }
}
?>