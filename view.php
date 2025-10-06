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
    p.army_no, p.rank, p.trade, p.name, p.home_address, p.doe, p.dos, p.appt,
    f.father_name, f.mother_name, f.marital_status, f.nok_details, f.spouse_name, f.dom, f.num_children,
    c.education_level, c.passing_year, c.result AS civil_result, c.university_name, c.college_name,
    m.jn_cadre, m.n_cadre, m.mr, m.upgrading_class, m.driving_license, m.hill_driving, m.additional_course, m.language_qualification,
    ps.ppt_date, ps.ppt_result, ps.bpet_date, ps.bpet_result, ps.firing_date, ps.firing_result, ps.sports,
    b.dsp_account, b.joint_account, b.bank_name, b.loan_type, b.loan_amount, b.loan_emi, b.loan_duration, b.loan_percentage
FROM personal_details p
LEFT JOIN family_details f ON p.army_no = f.army_no
LEFT JOIN civil_qualification c ON p.army_no = c.army_no
LEFT JOIN military_qualification m ON p.army_no = m.army_no
LEFT JOIN physical_standards ps ON p.army_no = ps.army_no
LEFT JOIN bank_details b ON p.army_no = b.army_no
ORDER BY p.army_no ASC
";

$result = $conn->query($sql);
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
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f4f6f9;
    }
    
    .charts-container {
      display: grid;
      grid-template-columns: repeat(auto-fit,minmax(300px,1fr));
      gap: 20px;
      margin-bottom: 40px;
    }
    .chart-card {
      background: #fff;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
    }
    .table-container {
      background: #fff;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
    }
    table.dataTable th {
      background: #0d6efd;
      color: white;
      text-align: center;
    }
    table.dataTable td {
      text-align: center;
    }.hero { text-align:center; padding:50px 20px; background: linear-gradient(135deg,#0d6efd,#6610f2); color:white; border-radius:0 0 40px 40px; margin-bottom:30px; }
.hero h1 { font-weight:700; font-size:2.5rem; }
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
    </div>
    <div class="chart-card">
      <h5 class="text-center text-success">BPET Results</h5>
      <canvas id="bpetChart"></canvas>
    </div>
    <div class="chart-card">
      <h5 class="text-center text-danger">Firing Results</h5>
      <canvas id="firingChart"></canvas>
    </div>
  </div>

  <!-- Table -->
  <div class="container table-container mb-5">
    <h3 class="mb-3">📋 All Soldiers Data</h3>
    <table id="soldierTable" class="table table-bordered table-striped table-hover">
      <thead>
        <tr>
          <th>Army No</th><th>Rank</th><th>Trade</th><th>Name</th><th>Home Address</th><th>DOE</th><th>DOS</th><th>Appt</th>
          <th>Father</th><th>Mother</th><th>Marital Status</th><th>NOK</th><th>Spouse</th><th>DOM</th><th>Children</th>
          <th>Education</th><th>Year</th><th>Result</th><th>University</th><th>College</th>
          <th>JN Cadre</th><th>N Cadre</th><th>MR</th><th>Upgrading Class</th><th>Driving License</th><th>Hill Driving</th><th>Additional Course</th><th>Language</th>
          <th>PPT Date</th><th>PPT Result</th><th>BPET Date</th><th>BPET Result</th><th>Firing Date</th><th>Firing Result</th><th>Sports</th>
          <th>DSP Account</th><th>Joint Account</th><th>Bank Name</th><th>Loan Type</th><th>Loan Amt</th><th>EMI</th><th>Duration</th><th>%</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['army_no']) ?></td>
          <td><?= htmlspecialchars($row['rank']) ?></td>
          <td><?= htmlspecialchars($row['trade']) ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['home_address']) ?></td>
          <td><?= htmlspecialchars($row['doe']) ?></td>
          <td><?= htmlspecialchars($row['dos']) ?></td>
          <td><?= htmlspecialchars($row['appt']) ?></td>
          <td><?= htmlspecialchars($row['father_name']) ?></td>
          <td><?= htmlspecialchars($row['mother_name']) ?></td>
          <td><?= htmlspecialchars($row['marital_status']) ?></td>
          <td><?= htmlspecialchars($row['nok_details']) ?></td>
          <td><?= htmlspecialchars($row['spouse_name']) ?></td>
          <td><?= htmlspecialchars($row['dom']) ?></td>
          <td><?= htmlspecialchars($row['num_children']) ?></td>
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
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <script>
    // Chart Data
    const pptData = <?= json_encode($pptData) ?>;
    const pptLabels = <?= json_encode($pptLabels) ?>;
    const bpetData = <?= json_encode($bpetData) ?>;
    const bpetLabels = <?= json_encode($bpetLabels) ?>;
    const firingData = <?= json_encode($firingData) ?>;
    const firingLabels = <?= json_encode($firingLabels) ?>;

    // Charts
    new Chart(document.getElementById('pptChart'), {
      type: 'doughnut',
      data: { labels: pptLabels, datasets: [{ data: pptData, backgroundColor: ['#0d6efd','#20c997','#ffc107','#dc3545'] }] }
    });
    new Chart(document.getElementById('bpetChart'), {
      type: 'bar',
      data: { labels: bpetLabels, datasets: [{ data: bpetData, backgroundColor: ['#198754','#0dcaf0','#ffc107','#dc3545'] }] }
    });
    new Chart(document.getElementById('firingChart'), {
      type: 'pie',
      data: { labels: firingLabels, datasets: [{ data: firingData, backgroundColor: ['#6f42c1','#0d6efd','#20c997','#ffc107'] }] }
    });

    // DataTables
    $(document).ready(function () {
        $('#soldierTable').DataTable({
            scrollX: true,
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
        });
    });
  </script>

</body>
</html>
