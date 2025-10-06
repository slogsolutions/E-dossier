<?php
// insert.php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: hero.php');
    exit;
}

function post($name, $default = '') {
    return isset($_POST[$name]) ? trim($_POST[$name]) : $default;
}

// Normalize numeric inputs
$loan_amount = (float)(post('loan_amount', 0));
$loan_emi = (float)(post('loan_emi', 0));
$loan_duration = (int)(post('loan_duration', 0));
$loan_percentage = (float)(post('loan_percentage', 0));

$num_children = post('num_children', '0');
$num_children = ($num_children === '' || strtoupper($num_children) === 'NONE') ? 0 : (int)$num_children;

// Children JSON
$children = [];
for ($i = 1; $i <= $num_children; $i++) {
    $cname = post("child_name_$i", '');
    $cdob  = post("child_dob_$i", '');
    $cpt2no = post("child_pt2no_$i", '');
    $cpt2date = post("child_pt2date_$i", '');
    if ($cname !== '' || $cdob !== '') {
        $children[] = [
            'name' => $cname,
            'dob'  => $cdob,
            'pt2no'=> $cpt2no,
            'pt2date' => $cpt2date
        ];
    }
}
$children_json = json_encode($children, JSON_UNESCAPED_UNICODE);

// Arrays
$medical_cat = isset($_POST['medical_cat']) ? implode(',', $_POST['medical_cat']) : '';
$sports = isset($_POST['sports']) ? implode(',', $_POST['sports']) : '';

// Personal
$army_no = post('army_no');
$rank = post('rank');
$trade = post('trade');
$name = post('name');
$home_address = post('home_address');
$doe = post('doe');
$dos = post('dos');
$appt = post('appt');
$icard_no = post('icard_no');
$doi = post('doi');
$issuing_auth = post('issuing_auth');
$medical_specific = post('medical_specific');
$height=post('height');
$weight=post('weight');
$overweight=post('overweight');

// Family
$father_name = post('father_name');
$father_pt2_no = post('father_pt2_no');
$father_pt2_date = post('father_pt2_date');
$mother_name = post('mother_name');
$mother_pt2_no = post('mother_pt2_no');
$mother_pt2_date = post('mother_pt2_date');
$marital_status = post('marital_status');
$nok_details = post('nok_details');
$spouse_name = post('spouse_name');
$dom = post('dom');
$pt2_no = post('pt2_no');
$pt2_date = post('pt2_date');

// Education
$education_level = post('education_level');
$passing_year = (int)post('passing_year', 0);
$result_val = post('result');
$university_name = post('university_name');
$college_name = post('college_name');

// Military
$jn_cadre = post('jn_cadre');
$n_cadre = post('n_cadre');
$mr = post('mr');
$upgrading_class = post('upgrading_class');
$driving_license = post('driving_license');
$hill_driving = post('hill_driving');
$additional_course = post('additional_course');
$language_qualification = post('language_qualification');

// Physical
$ppt_date = post('ppt_date');
$ppt_result = post('ppt_result');
$bpet_date = post('bpet_date');
$bpet_result = post('bpet_result');
$firing_date = post('firing_date');
$firing_result = post('firing_result');

// Bank
$dsp_account = post('dsp_account');
$joint_account = post('joint_account');
$bank_name = post('bank_name');
$loan_type = post('loan_type');

// Punishment
$red_ink = (int)post('red_ink', 0);
$black_ink = (int)post('black_ink', 0);
$punishment = post('punishment');

$conn->begin_transaction();

try {
    // 1) personal_details
    $sql1 = "INSERT INTO personal_details
        (army_no, rank, trade, name, home_address, doe, dos, appt, icard_no, doi, issuing_auth, medical_cat, medical_specific, height, weight, overweight_percentage)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("ssssssssssssssss", $army_no, $rank, $trade, $name, $home_address, $doe, $dos, $appt, $icard_no, $doi, $issuing_auth, $medical_cat, $medical_specific,$height,$weight,$overweight);
    $stmt1->execute();
    $stmt1->close();

    // 2) family_details
    $sql2 = "INSERT INTO family_details
        (army_no, father_name, father_pt2_no, father_pt2_date, mother_name, mother_pt2_no, mother_pt2_date, marital_status, nok_details, spouse_name, dom, pt2_no, pt2_date, num_children, children_details)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("ssssssssssssiss",
        $army_no, $father_name, $father_pt2_no, $father_pt2_date,
        $mother_name, $mother_pt2_no, $mother_pt2_date,
        $marital_status, $nok_details, $spouse_name, $dom,
        $pt2_no, $pt2_date, $num_children, $children_json
    );
    $stmt2->execute();
    $stmt2->close();

    // 3) civil_qualification
    $sql3 = "INSERT INTO civil_qualification
        (army_no, education_level, passing_year, result, university_name, college_name)
        VALUES (?, ?, ?, ?, ?, ?)";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("ssisss", $army_no, $education_level, $passing_year, $result_val, $university_name, $college_name);
    $stmt3->execute();
    $stmt3->close();

    // 4) military_qualification
    $sql4 = "INSERT INTO military_qualification
        (army_no, jn_cadre, n_cadre, mr, upgrading_class, driving_license, hill_driving, additional_course, language_qualification)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt4 = $conn->prepare($sql4);
    $stmt4->bind_param("sssssssss", $army_no, $jn_cadre, $n_cadre, $mr, $upgrading_class, $driving_license, $hill_driving, $additional_course, $language_qualification);
    $stmt4->execute();
    $stmt4->close();

    // 5) physical_standards
    $sql5 = "INSERT INTO physical_standards
        (army_no, ppt_date, ppt_result, bpet_date, bpet_result, firing_date, firing_result, sports)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt5 = $conn->prepare($sql5);
    $stmt5->bind_param("ssssssss", $army_no, $ppt_date, $ppt_result, $bpet_date, $bpet_result, $firing_date, $firing_result, $sports);
    $stmt5->execute();
    $stmt5->close();

    // 6) bank_details
    $sql6 = "INSERT INTO bank_details
        (army_no, dsp_account, joint_account, bank_name, loan_type, loan_amount, loan_emi, loan_duration, loan_percentage)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt6 = $conn->prepare($sql6);
    $stmt6->bind_param("sssssddid", $army_no, $dsp_account, $joint_account, $bank_name, $loan_type, $loan_amount, $loan_emi, $loan_duration, $loan_percentage);
    $stmt6->execute();
    $stmt6->close();

    // 7) punishment
    $sql7 = "INSERT INTO punishment (army_no, red_ink, black_ink, punishment)
             VALUES (?, ?, ?, ?)";
    $stmt7 = $conn->prepare($sql7);
    $stmt7->bind_param("siis", $army_no, $red_ink, $black_ink, $punishment);
    $stmt7->execute();
    $stmt7->close();

    // ✅ Commit if everything is fine
    $conn->commit();

    // Redirect to hero.php after success
    header("Location: cleark_dashboard.php?success=1");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    echo "❌ Insert failed: " . htmlspecialchars($e->getMessage());
}
?>
