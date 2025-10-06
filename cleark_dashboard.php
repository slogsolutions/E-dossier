<?php
session_start();
include 'connect.php';

// --- Full Soldier Data --- //
$sql = "
SELECT 
    p.army_no, p.rank, p.trade, p.name, p.home_address, p.doe, p.dos, p.appt, 
    p.icard_no, p.doi, p.issuing_auth, p.medical_cat, p.medical_specific, 
    p.height, p.weight, p.overweight_percentage,
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
ORDER BY p.army_no ASC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Clerk Dashboard</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="fonts/google.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: url("images/camo.png") repeat center center fixed;
    background-size: cover;
    animation: fadeInBody 1.2s ease-in-out;
}
@keyframes fadeInBody { from { opacity: 0; } to { opacity: 1; } }

.hero {
    text-align: center;
    padding: 60px 20px;
    background: linear-gradient(135deg, rgba(13,110,253,0.6), rgba(102,16,242,0.6));
    color: #f8f9fa;
    border-radius: 0 0 50px 50px;
    margin-bottom: 40px;
    box-shadow: 0px 6px 20px rgba(0,0,0,0.6);
}
.hero h1 { font-weight: 800; font-size: 2.8rem; }
.hero p { font-size: 1.1rem; opacity: 0.9; }
.hero .btn { margin: 0 5px; }

.table-container {
    background: rgba(255,255,255,0.95);
    padding: 20px;
    border-radius: 20px;
    box-shadow: 0px 6px 20px rgba(0,0,0,0.3);
}
table.dataTable th { background: #212529; color: #f8f9fa; text-align: center; }
table.dataTable td { text-align: center; vertical-align: middle; }
table.dataTable tbody tr:hover { background: rgba(13,110,253,0.15); }

#updateSection { display: none; margin-top: 20px; }
</style>
</head>
<body>
<section class="hero">
<h1>📘 Clerk Dashboard</h1>
<p>"Manage Records Efficiently."</p>
<a href="form.html" class="btn btn-warning btn-lg">New Registration</a>
<button class="btn btn-info btn-lg" id="updateBtn">Update Data</button>
</section>

<div class="container table-container mb-5">
<h3 class="mb-3">📋 All Soldiers Data</h3>

<div class="row mb-3">
    <div class="col-md-4">
        <select id="downloadSelect" class="form-select">
            <option value="">-- Download Particular Data --</option>
            <option value="bpet">BPET Result</option>
            <option value="ppt">PPT Result</option>
            <option value="firing">Firing Result</option>
            <option value="overweight">Overweight Person</option>
        </select>
    </div>
</div>

<div id="updateSection">
    <input type="text" id="armySearch" class="form-control mb-2" placeholder="Enter Army No">
    <button class="btn btn-primary mb-3" id="searchBtn">Search</button>
    <div id="updateTable"></div>
</div>

<table id="soldierTable" class="table table-bordered table-striped table-hover display nowrap">
<thead>
<tr>
<th>Sr No</th>
<th>Army No</th><th>Rank</th><th>Trade</th><th>Name</th><th>Home Address</th><th>DOE</th><th>DOS</th><th>Appt</th>
<th>icard_no</th><th>doi</th><th>issuing_auth</th><th>medical_cat</th><th>medical_specific</th><th>height</th><th>weight</th><th>overweight_percentage</th>
<th>Father</th><th>Father PT-II No</th><th>Father PT-II Date</th>
<th>Mother</th><th>Mother PT-II No</th><th>Mother PT-II Date</th>
<th>Marital Status</th><th>NOK</th><th>Spouse</th><th>DOM</th><th>Children</th>
<th>Education</th><th>Year</th><th>Result</th><th>University</th><th>College</th>
<th>JN Cadre</th><th>N Cadre</th><th>MR</th><th>Upgrading Class</th><th>Driving License</th><th>Hill Driving</th><th>Additional Course</th><th>Lacking Course</th>
<th>PPT Date</th><th>PPT Result</th><th>BPET Date</th><th>BPET Result</th><th>Firing Date</th><th>Firing Result</th><th>Sports</th>
<th>DSP Account</th><th>Joint Account</th><th>Bank Name</th><th>Loan Type</th><th>Loan Amt</th><th>EMI</th><th>Duration</th><th>%</th>
<th>Red Ink</th><th>Black Ink</th><th>Punishment</th>
</tr>
</thead>
<tbody>
<?php $sr=1; while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $sr++ ?></td>
<td><?= htmlspecialchars($row['army_no']) ?></td>
<td><?= htmlspecialchars($row['rank']) ?></td>
<td><?= htmlspecialchars($row['trade']) ?></td>
<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= htmlspecialchars($row['home_address']) ?></td>
<td><?= htmlspecialchars($row['doe']) ?></td>
<td><?= htmlspecialchars($row['dos']) ?></td>
<td><?= htmlspecialchars($row['appt']) ?></td>
<td><?= htmlspecialchars($row['icard_no']) ?></td>
<td><?= htmlspecialchars($row['doi']) ?></td>
<td><?= htmlspecialchars($row['issuing_auth']) ?></td>
<td><?= htmlspecialchars($row['medical_cat']) ?></td>
<td><?= htmlspecialchars($row['medical_specific']) ?></td>
<td><?= htmlspecialchars($row['height']) ?></td>
<td><?= htmlspecialchars($row['weight']) ?></td>
<td><?= htmlspecialchars($row['overweight_percentage']) ?></td>
<td><?= htmlspecialchars($row['father_name']) ?></td>
<td><?= htmlspecialchars($row['father_pt2_no']) ?></td>
<td><?= htmlspecialchars($row['father_pt2_date']) ?></td>
<td><?= htmlspecialchars($row['mother_name']) ?></td>
<td><?= htmlspecialchars($row['mother_pt2_no']) ?></td>
<td><?= htmlspecialchars($row['mother_pt2_date']) ?></td>
<td><?= htmlspecialchars($row['marital_status']) ?></td>
<td><?= htmlspecialchars($row['nok_details']) ?></td>
<td><?= htmlspecialchars($row['spouse_name']) ?></td>
<td><?= htmlspecialchars($row['dom']) ?></td>
<td>
<?php 
$childrenJson = !empty($row['children_details']) ? htmlspecialchars($row['children_details'], ENT_QUOTES) : '[]';
if($row['num_children']>0): ?>
<a href="#" class="btn btn-sm btn-outline-primary view-children" data-children='<?= $childrenJson ?>'>
<?= $row['num_children'] ?> 👁
</a>
<?php else: ?>0<?php endif; ?>
</td>
<td><?= htmlspecialchars($row['education_level']) ?></td>
<td><?= htmlspecialchars($row['passing_year']) ?></td>
<td><?= htmlspecialchars($row['civil_result']) ?></td>
<td><?= htmlspecialchars($row['university_name']) ?></td>
<td><?= htmlspecialchars($row['college_name']) ?></td>
<td><?= htmlspecialchars($row['jn_cadre']) ?></td>
<td><?= htmlspecialchars($row['n_cadre']) ?></td>
<td><?= htmlspecialchars($row['mr']) ?></td>
<td><?= htmlspecialchars($row['upgrading_class']) ?></td>
<td><?= htmlspecialchars($row['driving_license']) ?></td>
<td><?= htmlspecialchars($row['hill_driving']) ?></td>
<td><?= htmlspecialchars($row['additional_course']) ?></td>
<td><?= htmlspecialchars($row['language_qualification']) ?></td>
<td><?= htmlspecialchars($row['ppt_date']) ?></td>
<td><?= htmlspecialchars($row['ppt_result']) ?></td>
<td><?= htmlspecialchars($row['bpet_date']) ?></td>
<td><?= htmlspecialchars($row['bpet_result']) ?></td>
<td><?= htmlspecialchars($row['firing_date']) ?></td>
<td><?= htmlspecialchars($row['firing_result']) ?></td>
<td><?= htmlspecialchars($row['sports']) ?></td>
<td><?= htmlspecialchars($row['dsp_account']) ?></td>
<td><?= htmlspecialchars($row['joint_account']) ?></td>
<td><?= htmlspecialchars($row['bank_name']) ?></td>
<td><?= htmlspecialchars($row['loan_type']) ?></td>
<td><?= htmlspecialchars($row['loan_amount']) ?></td>
<td><?= htmlspecialchars($row['loan_emi']) ?></td>
<td><?= htmlspecialchars($row['loan_duration']) ?></td>
<td><?= htmlspecialchars($row['loan_percentage']) ?></td>
<td><?= htmlspecialchars($row['red_ink']) ?></td>
<td><?= htmlspecialchars($row['black_ink']) ?></td>
<td><?= htmlspecialchars($row['punishment']) ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

<!-- Children Modal -->
<div class="modal fade" id="childrenModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Children Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="childrenModalBody"></div>
    </div>
  </div>
</div>

<script>
$(document).ready(function () {
    $('#soldierTable').DataTable({ scrollX: true, pageLength: 10, lengthMenu: [5,10,25,50,100] });

    $('#updateBtn').click(function(){ $('#updateSection').slideToggle(); });

    $('#searchBtn').click(function(){
        let army_no = $('#armySearch').val().trim();
        if(army_no === '') { alert("Enter Army No"); return; }
        $.ajax({
            url: 'search_soldier.php',
            method: 'POST',
            data: {army_no: army_no},
            success: function(response){ $('#updateTable').html(response); }
        });
    });

    // Children Modal
    $('#soldierTable tbody').on('click', '.view-children', function(e){
        e.preventDefault();
        let childrenData = $(this).data('children');
        let children = [];
        try {
            if(typeof childrenData === 'string') children = JSON.parse(childrenData);
            else children = childrenData;
        } catch(e){ children = []; console.error("Children JSON parse error:", e); }

        if(children.length === 0){
            $('#childrenModalBody').html("<p>No children data</p>");
        } else {
            let html = "<table class='table table-bordered table-striped'><thead><tr><th>Name</th><th>DOB</th><th>PT-II No</th><th>PT-II Date</th></tr></thead><tbody>";
            children.forEach(c => html += `<tr><td>${c.name}</td><td>${c.dob}</td><td>${c.pt2no}</td><td>${c.pt2date}</td></tr>`);
            html += "</tbody></table>";
            $('#childrenModalBody').html(html);
        }
        new bootstrap.Modal(document.getElementById('childrenModal')).show();
    });

    // Download select change
    $('#downloadSelect').change(function(){
        let val = $(this).val();
        if(val === '') return;
        window.open('download_report.php?type='+val,'_blank');
    });
});
</script>
</body>
</html>
