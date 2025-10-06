<?php
include 'connect.php';

if(isset($_POST['army_no'])){
    $army_no = $conn->real_escape_string($_POST['army_no']);

    // --- Personal Details ---
    $rank = $conn->real_escape_string($_POST['rank']);
    $trade = $conn->real_escape_string($_POST['trade']);
    $name = $conn->real_escape_string($_POST['name']);
    $home_address = $conn->real_escape_string($_POST['home_address']);
    $doe = $conn->real_escape_string($_POST['doe']);
    $dos = $conn->real_escape_string($_POST['dos']);
    $appt = $conn->real_escape_string($_POST['appt']);
    $icard_no = $conn->real_escape_string($_POST['icard_no']);
    $doi = $conn->real_escape_string($_POST['doi']);
    $issuing_auth = $conn->real_escape_string($_POST['issuing_auth']);
    $medical_cat = $conn->real_escape_string($_POST['medical_cat']);
    $medical_specific = $conn->real_escape_string($_POST['medical_specific']);
    $height = $conn->real_escape_string($_POST['height']);
    $weight = $conn->real_escape_string($_POST['weight']);
    $overweight_percentage = $conn->real_escape_string($_POST['overweight_percentage']);

    // --- Family Details ---
    $father_name = $conn->real_escape_string($_POST['father_name']);
    $father_pt2_no = $conn->real_escape_string($_POST['father_pt2_no']);
    $father_pt2_date = $conn->real_escape_string($_POST['father_pt2_date']);
    $mother_name = $conn->real_escape_string($_POST['mother_name']);
    $mother_pt2_no = $conn->real_escape_string($_POST['mother_pt2_no']);
    $mother_pt2_date = $conn->real_escape_string($_POST['mother_pt2_date']);
    $marital_status = $conn->real_escape_string($_POST['marital_status']);
    $nok_details = $conn->real_escape_string($_POST['nok_details']);
    $spouse_name = $conn->real_escape_string($_POST['spouse_name']);
    $dom = $conn->real_escape_string($_POST['dom']);
    $num_children = $conn->real_escape_string($_POST['num_children']);

    // --- Civil Qualification ---
    $education_level = $conn->real_escape_string($_POST['education_level']);
    $passing_year = $conn->real_escape_string($_POST['passing_year']);
    $civil_result = $conn->real_escape_string($_POST['civil_result']);
    $university_name = $conn->real_escape_string($_POST['university_name']);
    $college_name = $conn->real_escape_string($_POST['college_name']);

    // --- Military Qualification ---
    $jn_cadre = $conn->real_escape_string($_POST['jn_cadre']);
    $n_cadre = $conn->real_escape_string($_POST['n_cadre']);
    $mr = $conn->real_escape_string($_POST['mr']);
    $upgrading_class = $conn->real_escape_string($_POST['upgrading_class']);
    $driving_license = $conn->real_escape_string($_POST['driving_license']);
    $hill_driving = $conn->real_escape_string($_POST['hill_driving']);
    $additional_course = $conn->real_escape_string($_POST['additional_course']);
    $language_qualification = $conn->real_escape_string($_POST['language_qualification']);

    // --- Physical Standards ---
    $ppt_date = $conn->real_escape_string($_POST['ppt_date']);
    $ppt_result = $conn->real_escape_string($_POST['ppt_result']);
    $bpet_date = $conn->real_escape_string($_POST['bpet_date']);
    $bpet_result = $conn->real_escape_string($_POST['bpet_result']);
    $firing_date = $conn->real_escape_string($_POST['firing_date']);
    $firing_result = $conn->real_escape_string($_POST['firing_result']);
    $sports = $conn->real_escape_string($_POST['sports']);

    // --- Bank Details ---
    $dsp_account = $conn->real_escape_string($_POST['dsp_account']);
    $joint_account = $conn->real_escape_string($_POST['joint_account']);
    $bank_name = $conn->real_escape_string($_POST['bank_name']);
    $loan_type = $conn->real_escape_string($_POST['loan_type']);
    $loan_amount = $conn->real_escape_string($_POST['loan_amount']);
    $loan_emi = $conn->real_escape_string($_POST['loan_emi']);
    $loan_duration = $conn->real_escape_string($_POST['loan_duration']);
    $loan_percentage = $conn->real_escape_string($_POST['loan_percentage']);

    // --- Punishment Details ---
    $red_ink = $conn->real_escape_string($_POST['red_ink']);
    $black_ink = $conn->real_escape_string($_POST['black_ink']);
    $punishment = $conn->real_escape_string($_POST['punishment']);

    // --- Update Queries ---
    $p_sql = "UPDATE personal_details SET rank='$rank', trade='$trade', name='$name', home_address='$home_address', doe='$doe', dos='$dos', appt='$appt', icard_no='$icard_no', doi='$doi', issuing_auth='$issuing_auth', medical_cat='$medical_cat', medical_specific='$medical_specific', height='$height', weight='$weight', overweight_percentage='$overweight_percentage' WHERE army_no='$army_no'";
    
    $f_sql = "UPDATE family_details SET father_name='$father_name', father_pt2_no='$father_pt2_no', father_pt2_date='$father_pt2_date', mother_name='$mother_name', mother_pt2_no='$mother_pt2_no', mother_pt2_date='$mother_pt2_date', marital_status='$marital_status', nok_details='$nok_details', spouse_name='$spouse_name', dom='$dom', num_children='$num_children' WHERE army_no='$army_no'";
    
    $c_sql = "UPDATE civil_qualification SET education_level='$education_level', passing_year='$passing_year', result='$civil_result', university_name='$university_name', college_name='$college_name' WHERE army_no='$army_no'";
    
    $m_sql = "UPDATE military_qualification SET jn_cadre='$jn_cadre', n_cadre='$n_cadre', mr='$mr', upgrading_class='$upgrading_class', driving_license='$driving_license', hill_driving='$hill_driving', additional_course='$additional_course', language_qualification='$language_qualification' WHERE army_no='$army_no'";
    
    $ps_sql = "UPDATE physical_standards SET ppt_date='$ppt_date', ppt_result='$ppt_result', bpet_date='$bpet_date', bpet_result='$bpet_result', firing_date='$firing_date', firing_result='$firing_result', sports='$sports' WHERE army_no='$army_no'";
    
    $b_sql = "UPDATE bank_details SET dsp_account='$dsp_account', joint_account='$joint_account', bank_name='$bank_name', loan_type='$loan_type', loan_amount='$loan_amount', loan_emi='$loan_emi', loan_duration='$loan_duration', loan_percentage='$loan_percentage' WHERE army_no='$army_no'";

    $pu_sql = "UPDATE punishment SET red_ink='$red_ink', black_ink='$black_ink', punishment='$punishment' WHERE army_no='$army_no'";

    // --- Execute Updates ---
    if($conn->query($p_sql) && $conn->query($f_sql) && $conn->query($c_sql) && $conn->query($m_sql) && $conn->query($ps_sql) && $conn->query($b_sql) && $conn->query($pu_sql)){
        echo "<script>alert('Soldier data updated successfully'); window.location='cleark_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating data: ".$conn->error."'); window.history.back();</script>";
    }
}
?>
