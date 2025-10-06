<?php
session_start();
include 'connect.php';

// --- Chart Data --- //
$pptQuery = $conn->query("SELECT ppt_result, COUNT(*) as count FROM physical_standards GROUP BY ppt_result");
$bpetQuery = $conn->query("SELECT bpet_result, COUNT(*) as count FROM physical_standards GROUP BY bpet_result");
$firingQuery = $conn->query("SELECT firing_result, COUNT(*) as count FROM physical_standards GROUP BY firing_result");

$pptLabels = $pptData = [];
while($row = $pptQuery->fetch_assoc()){
    $pptLabels[] = $row['ppt_result'];
    $pptData[] = $row['count'];
}

$bpetLabels = $bpetData = [];
while($row = $bpetQuery->fetch_assoc()){
    $bpetLabels[] = $row['bpet_result'];
    $bpetData[] = $row['count'];
}

$firingLabels = $firingData = [];
while($row = $firingQuery->fetch_assoc()){
    $firingLabels[] = $row['firing_result'];
    $firingData[] = $row['count'];
}

// --- Full Soldier Data --- //
$sql = "
SELECT 
    p.army_no, p.rank, p.trade, p.name, p.home_address, p.doe, p.dos, p.appt, p.icard_no, p.doi, p.issuing_auth, p.medical_cat, p.medical_specific, p.height, p.weight, p.overweight_percentage,
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

// --- Drilldown soldier data for charts --- //
$chartSoldiers = [];
$q = $conn->query("SELECT p.army_no, p.rank, p.name, ps.ppt_result, ps.bpet_result, ps.firing_result 
                   FROM personal_details p 
                   LEFT JOIN physical_standards ps ON p.army_no=ps.army_no");
while($row = $q->fetch_assoc()){
    $chartSoldiers[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>E-Dossier Dashboard</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <!-- DataTables CSS & JS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="fonts/google.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

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
  .charts-container { display: grid; grid-template-columns: repeat(auto-fit,minmax(300px,1fr)); gap: 25px; margin-bottom: 50px; }
  .chart-card { background: rgba(255,255,255,0.85); padding: 25px; border-radius: 20px; box-shadow: 0px 6px 15px rgba(0,0,0,0.25); }
  .table-container { background: rgba(255,255,255,0.9); padding: 25px; border-radius: 20px; box-shadow: 0px 6px 20px rgba(0,0,0,0.3); }
  table.dataTable th { background: #212529; color: #f8f9fa; text-align: center; }
  table.dataTable td { text-align: center; vertical-align: middle; }
  table.dataTable tbody tr:hover { background: rgba(13,110,253,0.15); }
  .modal-content { border-radius: 15px; }
  </style>
</head>
<body>
<section class="hero">
  <h1>📘 CO Dashboard</h1>
  <p>"Discipline, Dedication & Documentation – The Pillars of a Strong Army."</p>
  <a href="hero.php" class="btn btn-warning btn-lg mt-2">Logout</a>
</section>

<!-- Charts -->
<div class="container charts-container">
  <div class="chart-card">
    <h5 class="text-center text-primary">PPT Results</h5>
    <canvas id="pptChart"></canvas>
    <div id="pptDrilldown" class="drilldown"></div>
  </div>
  <div class="chart-card">
    <h5 class="text-center text-success">BPET Results</h5>
    <canvas id="bpetChart"></canvas>
    <div id="bpetDrilldown" class="drilldown"></div>
  </div>
  <div class="chart-card">
    <h5 class="text-center text-danger">Firing Results</h5>
    <canvas id="firingChart"></canvas>
    <div id="firingDrilldown" class="drilldown"></div>
  </div>
</div>

<!-- Table -->
<div class="container table-container mb-5">
  <h3 class="mb-3">📋 All Soldiers Data</h3>
  <table id="soldierTable" class="table table-bordered table-striped table-hover">
    <thead>
      <tr>
        <th>Sr No</th><th>Army No</th><th>Rank</th><th>Trade</th><th>Name</th><th>Home Address</th><th>DOE</th><th>DOS</th><th>Appt</th><th>icard_no</th><th>doi</th><th>issuing_auth</th><th>medical_cat</th><th>medical_specific</th><th>height</th><th>weight</th><th>overweight_percentage</th>
        <th>Father</th><th>Father PT-II No</th><th>Father PT-II Date</th>
        <th>Mother</th><th>Mother PT-II No</th><th>Mother PT-II Date</th>
        <th>Marital Status</th><th>NOK</th><th>Spouse</th><th>DOM</th>
        <th>Children</th>
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
          <?php if($row['num_children']>0): ?>
            <a href="#" class="btn btn-sm btn-outline-primary view-children" 
               data-children='<?= htmlspecialchars($row['children_details']) ?>'>
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
      <div class="modal-header"><h5 class="modal-title">Children Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="childrenModalBody"></div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const pptData = <?= json_encode($pptData) ?>;
const pptLabels = <?= json_encode($pptLabels) ?>;
const bpetData = <?= json_encode($bpetData) ?>;
const bpetLabels = <?= json_encode($bpetLabels) ?>;
const firingData = <?= json_encode($firingData) ?>;
const firingLabels = <?= json_encode($firingLabels) ?>;
const soldiers = <?= json_encode($chartSoldiers) ?>;

// Drilldown
function showDrilldown(containerId, resultType, value) {
  let filtered = soldiers.filter(s => s[resultType] === value);
  if(filtered.length === 0) {
    document.getElementById(containerId).innerHTML = "<p class='text-muted'>No data available</p>";
    document.getElementById(containerId).style.display = "block";
    return;
  }
  let html = "<table class='table table-sm table-bordered mt-2'><thead><tr><th>Army No</th><th>Rank</th><th>Name</th></tr></thead><tbody>";
  filtered.forEach(s => { html += `<tr><td>${s.army_no}</td><td>${s.rank}</td><td>${s.name}</td></tr>`; });
  html += "</tbody></table>";
  document.getElementById(containerId).innerHTML = html;
  document.getElementById(containerId).style.display = "block";
}

// Charts
new Chart(document.getElementById('pptChart'), {
  type: 'doughnut', data: { labels: pptLabels, datasets: [{ data: pptData, backgroundColor: ['#0d6efd','#20c997','#ffc107','#dc3545'] }] },
  options: { onClick: (e, items) => { if(items.length){ showDrilldown("pptDrilldown","ppt_result",pptLabels[items[0].index]); } } }
});
new Chart(document.getElementById('bpetChart'), {
  type: 'bar', data: { labels: bpetLabels, datasets: [{ data: bpetData, backgroundColor: ['#198754','#0dcaf0','#ffc107','#dc3545'] }] },
  options: { onClick: (e, items) => { if(items.length){ showDrilldown("bpetDrilldown","bpet_result",bpetLabels[items[0].index]); } } }
});
new Chart(document.getElementById('firingChart'), {
  type: 'pie', data: { labels: firingLabels, datasets: [{ data: firingData, backgroundColor: ['#6f42c1','#0d6efd','#20c997','#ffc107'] }] },
  options: { onClick: (e, items) => { if(items.length){ showDrilldown("firingDrilldown","firing_result",firingLabels[items[0].index]); } } }
});

// DataTables
$(document).ready(function () {
  $('#soldierTable').DataTable({ scrollX: true, pageLength: 10, lengthMenu: [5, 10, 25, 50, 100] });
});

// Children Modal
document.querySelectorAll('.view-children').forEach(btn => {
  btn.addEventListener('click', function(e){
    e.preventDefault();
    let children = JSON.parse(this.dataset.children || "[]");
    if(children.length === 0){ document.getElementById("childrenModalBody").innerHTML="<p>No children data</p>"; }
    else {
      let html = "<table class='table table-bordered'><thead><tr><th>Name</th><th>DOB</th><th>PT-II No</th><th>PT-II Date</th></tr></thead><tbody>";
      children.forEach(c => { html += `<tr><td>${c.name}</td><td>${c.dob}</td><td>${c.pt2no}</td><td>${c.pt2date}</td></tr>`; });
      html += "</tbody></table>";
      document.getElementById("childrenModalBody").innerHTML = html;
    }
    new bootstrap.Modal(document.getElementById('childrenModal')).show();
  });
});
</script>
</body>
</html>
