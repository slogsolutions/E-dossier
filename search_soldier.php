<?php
include 'connect.php';

if(isset($_POST['army_no'])){
    $army_no = $conn->real_escape_string($_POST['army_no']);

    $sql = "
    SELECT 
        p.army_no, p.rank, p.trade, p.name, p.home_address, p.doe, p.dos, p.appt,
        p.icard_no, p.doi, p.issuing_auth, p.medical_cat, p.medical_specific, p.height, p.weight, p.overweight_percentage,

        f.father_name, f.father_pt2_no, f.father_pt2_date, f.mother_name, f.mother_pt2_no, f.mother_pt2_date,
        f.marital_status, f.nok_details, f.spouse_name, f.dom, f.num_children, f.children_details,

        c.education_level, c.passing_year, c.result AS civil_result, c.university_name, c.college_name,

        m.jn_cadre, m.n_cadre, m.mr, m.upgrading_class, m.driving_license, m.hill_driving, m.additional_course, m.language_qualification,

        ps.ppt_date, ps.ppt_result, ps.bpet_date, ps.bpet_result, ps.firing_date, ps.firing_result, ps.sports,

        b.dsp_account, b.joint_account, b.bank_name, b.loan_type, b.loan_amount, b.loan_emi, b.loan_duration, b.loan_percentage,

        pu.red_ink, pu.black_ink, pu.punishment

    FROM personal_details p
    LEFT JOIN family_details f ON p.army_no = f.army_no
    LEFT JOIN civil_qualification c ON p.army_no = c.army_no
    LEFT JOIN military_qualification m ON p.army_no = m.army_no
    LEFT JOIN physical_standards ps ON p.army_no = ps.army_no
    LEFT JOIN bank_details b ON p.army_no = b.army_no
    LEFT JOIN punishment pu ON p.army_no = pu.army_no
    WHERE p.army_no = '$army_no'
    ";

    $res = $conn->query($sql);

    if($res->num_rows > 0){
        $row = $res->fetch_assoc();

        echo "<form method='post' action='update_soldier.php'>";
        
        // ---- Personal Details ----
        echo "<h5 class='mt-3 text-primary'>🪖 Personal Details</h5><table class='table table-bordered table-sm'>";
        $personal = ['rank','trade','name','home_address','doe','dos','appt','icard_no','doi','issuing_auth','medical_cat','medical_specific','height','weight','overweight_percentage'];
        foreach($personal as $field){
            $val = htmlspecialchars($row[$field] ?? '');
            $label = ucwords(str_replace('_',' ',$field));
            echo "<tr><th>$label</th><td><input type='text' class='form-control' name='$field' value='$val'></td></tr>";
        }
        echo "</table>";

        // ---- Family Details ----
        echo "<h5 class='mt-3 text-success'>👨‍👩‍👧 Family Details</h5><table class='table table-bordered table-sm'>";
        $family = ['father_name','father_pt2_no','father_pt2_date','mother_name','mother_pt2_no','mother_pt2_date','marital_status','nok_details','spouse_name','dom','num_children'];
        foreach($family as $field){
            $val = htmlspecialchars($row[$field] ?? '');
            $label = ucwords(str_replace('_',' ',$field));
            if($field == 'children_details'){
                echo "<tr><th>$label</th><td><textarea class='form-control' name='$field' rows='3'>$val</textarea></td></tr>";
            } else {
                echo "<tr><th>$label</th><td><input type='text' class='form-control' name='$field' value='$val'></td></tr>";
            }
        }
        echo "</table>";

        // ---- Civil Qualification ----
        echo "<h5 class='mt-3 text-info'>🎓 Civil Qualification</h5><table class='table table-bordered table-sm'>";
        $civil = ['education_level','passing_year','civil_result','university_name','college_name'];
        foreach($civil as $field){
            $val = htmlspecialchars($row[$field] ?? '');
            $label = ucwords(str_replace('_',' ',$field));
            echo "<tr><th>$label</th><td><input type='text' class='form-control' name='$field' value='$val'></td></tr>";
        }
        echo "</table>";

        // ---- Military Qualification ----
        echo "<h5 class='mt-3 text-warning'>🎖 Military Qualification</h5><table class='table table-bordered table-sm'>";
        $military = ['jn_cadre','n_cadre','mr','upgrading_class','driving_license','hill_driving','additional_course','language_qualification'];
        foreach($military as $field){
            $val = htmlspecialchars($row[$field] ?? '');
            $label = ucwords(str_replace('_',' ',$field));
            echo "<tr><th>$label</th><td><input type='text' class='form-control' name='$field' value='$val'></td></tr>";
        }
        echo "</table>";

        // ---- Physical Standards ----
        echo "<h5 class='mt-3 text-secondary'>💪 Physical Standards</h5><table class='table table-bordered table-sm'>";
        $physical = ['ppt_date','ppt_result','bpet_date','bpet_result','firing_date','firing_result','sports'];
        foreach($physical as $field){
            $val = htmlspecialchars($row[$field] ?? '');
            $label = ucwords(str_replace('_',' ',$field));
            echo "<tr><th>$label</th><td><input type='text' class='form-control' name='$field' value='$val'></td></tr>";
        }
        echo "</table>";

        // ---- Bank Details ----
        echo "<h5 class='mt-3 text-dark'>🏦 Bank Details</h5><table class='table table-bordered table-sm'>";
        $bank = ['dsp_account','joint_account','bank_name','loan_type','loan_amount','loan_emi','loan_duration','loan_percentage'];
        foreach($bank as $field){
            $val = htmlspecialchars($row[$field] ?? '');
            $label = ucwords(str_replace('_',' ',$field));
            echo "<tr><th>$label</th><td><input type='text' class='form-control' name='$field' value='$val'></td></tr>";
        }
        echo "</table>";

        // ---- Punishment ----
        echo "<h5 class='mt-3 text-danger'>⚖ Punishment</h5><table class='table table-bordered table-sm'>";
        $punish = ['red_ink','black_ink','punishment'];
        foreach($punish as $field){
            $val = htmlspecialchars($row[$field] ?? '');
            $label = ucwords(str_replace('_',' ',$field));
            echo "<tr><th>$label</th><td><input type='text' class='form-control' name='$field' value='$val'></td></tr>";
        }
        echo "</table>";

        echo "<input type='hidden' name='army_no' value='".htmlspecialchars($army_no)."'>";
        echo "<button type='submit' class='btn btn-success mt-3'>Update Soldier Data</button>";
        echo "</form>";
    } else {
        echo "<p class='text-danger'>Army No not found in the database.</p>";
    }
}
?>
