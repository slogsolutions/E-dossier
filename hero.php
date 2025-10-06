<?php
session_start();
include 'connect.php';

// Aggregate counts for charts
$pptQuery = $conn->query("SELECT ppt_result, COUNT(*) as count FROM physical_standards GROUP BY ppt_result");
$bpetQuery = $conn->query("SELECT bpet_result, COUNT(*) as count FROM physical_standards GROUP BY bpet_result");
$firingQuery = $conn->query("SELECT firing_result, COUNT(*) as count FROM physical_standards GROUP BY firing_result");

// Chart Data
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

// Detailed records for drill down
$pptDetails = [];
$res = $conn->query("SELECT pd.army_no, pd.rank, pd.name, ps.ppt_result 
                     FROM personal_details pd 
                     JOIN physical_standards ps ON pd.army_no = ps.army_no");
while($r = $res->fetch_assoc()){
    $pptDetails[$r['ppt_result']][] = $r;
}

$bpetDetails = [];
$res = $conn->query("SELECT pd.army_no, pd.rank, pd.name, ps.bpet_result 
                     FROM personal_details pd 
                     JOIN physical_standards ps ON pd.army_no = ps.army_no");
while($r = $res->fetch_assoc()){
    $bpetDetails[$r['bpet_result']][] = $r;
}

$firingDetails = [];
$res = $conn->query("SELECT pd.army_no, pd.rank, pd.name, ps.firing_result 
                     FROM personal_details pd 
                     JOIN physical_standards ps ON pd.army_no = ps.army_no");
while($r = $res->fetch_assoc()){
    $firingDetails[$r['firing_result']][] = $r;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  

  <title>E-Dossier Dashboard</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="fonts/google.css" rel="stylesheet">
  <script src="js/chart.js"></script>

  <style>
    body {
    font-family: 'Poppins', sans-serif;
    background: url("images/camo.png") repeat center center fixed;
    background-size: cover;
    margin: 0;
    animation: fadeInBody 1.2s ease-in-out;
  }

  @keyframes fadeInBody {
    from { opacity: 0; }
    to { opacity: 1; }
  }

  /* Hero Section with Glass Effect */
  .hero {
    text-align: center;
    padding: 70px 20px;
    background: rgba(255, 255, 255, 0.12); /* subtle glass */
    backdrop-filter: blur(10px);
    border-radius: 0 0 40px 40px;
    margin-bottom: 50px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.35);
    border: 2px solid rgba(255, 255, 255, 0.25);
    position: relative;
    overflow: hidden;
  }

  /* Gradient Glow Border Effect */
  .hero::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(13,110,253,0.6), rgba(103, 16, 242, 0.47));
    z-index: -1;
    filter: blur(40px);
  }

  .hero h1 {
    font-weight: 800;
    font-size: 3rem;
    color: #fff;
    text-shadow: 2px 2px 10px rgba(0,0,0,0.7);
  }
  .hero h2 {
    font-weight: 600;
    font-size: 2rem;
    color: #fff;
    text-shadow: 2px 2px 10px rgba(0,0,0,0.7);
  }
  .hero p {
    font-size: 1.2rem;
    margin-top: 10px;
    opacity: 0.95;
    color: #f8f9fa;
  }
  .hero .btn {
    padding: 12px 25px;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 10px;
    transition: all 0.3s ease;
  }
  .hero .btn-warning {
    background: #ffc107;
    border: none;
    box-shadow: 0 0 12px rgba(255,193,7,0.6);
  }
  .hero .btn-warning:hover {
    background: #e0a800;
    transform: translateY(-3px);
    box-shadow: 0 0 18px rgba(255,193,7,0.9);
  }
  .hero .btn-danger {
    background: #dc3545;
    border: none;
    box-shadow: 0 0 12px rgba(220,53,69,0.6);
  }
  .hero .btn-danger:hover {
    background: #b02a37;
    transform: translateY(-3px);
    box-shadow: 0 0 18px rgba(220,53,69,0.9);
  }

  /* Charts Container */
  .charts-container {
    display: flex;
    justify-content: center;
    gap: 40px;
    flex-wrap: wrap;
    max-width: 1200px;
    margin: 0 auto 60px auto;
  }

  .chart-card {
    background: rgba(255,255,255,0.85);
    padding: 25px;
    border-radius: 20px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.25);
    width: 360px;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .chart-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 28px rgba(0,0,0,0.3);
  }
  .chart-card h5 {
    margin-bottom: 15px;
    font-weight: 600;
  }

  /* Drill-down Details */
  .details-box {
    margin-top: 15px;
    padding: 12px;
    background: rgba(255,255,255,0.92);
    border-radius: 12px;
    max-height: 200px;
    overflow-y: auto;
    font-size: 0.9rem;
    text-align: left;
    box-shadow: inset 0 0 8px rgba(0,0,0,0.1);
  }
  .details-box ul {
    margin: 0;
    padding-left: 18px;
  }
  .details-box li {
    margin-bottom: 5px;
  }
  </style>
</head>
<body>

<section class="hero">
  <h1>59 Inf Div Signal Regiment</h1>
  <h2>📘 E-Dossier Dashboard 📘</h2>
  <p class="lead">"Discipline, Dedication & Documentation – The Pillars of a Strong Army."</p>
  <div class="mt-4">
    <a href="clerk_login.php" class="btn btn-warning btn-lg me-2">Clerk Login</a>
    <a href="co_login.php" class="btn btn-danger btn-lg">CO Login</a>
  </div>
</section>

<div class="container charts-container">
  <div class="chart-card">
    <h5 class="text-primary">PPT Results</h5>
    <canvas id="pptChart"></canvas>
    <div id="pptDetails" class="details-box"></div>
  </div>
  <div class="chart-card">
    <h5 class="text-success">BPET Results</h5>
    <canvas id="bpetChart"></canvas>
    <div id="bpetDetails" class="details-box"></div>
  </div>
  <div class="chart-card">
    <h5 class="text-danger">Firing Results</h5>
    <canvas id="firingChart"></canvas>
    <div id="firingDetails" class="details-box"></div>
  </div>
</div>

<script>
  // Chart Data
  const pptData = <?= json_encode($pptData) ?>;
  const pptLabels = <?= json_encode($pptLabels) ?>;
  const bpetData = <?= json_encode($bpetData) ?>;
  const bpetLabels = <?= json_encode($bpetLabels) ?>;
  const firingData = <?= json_encode($firingData) ?>;
  const firingLabels = <?= json_encode($firingLabels) ?>;

  // Drill-down Data
  const pptDetails = <?= json_encode($pptDetails) ?>;
  const bpetDetails = <?= json_encode($bpetDetails) ?>;
  const firingDetails = <?= json_encode($firingDetails) ?>;

  // Helper to show details
  function showDetails(containerId, records, label) {
    const box = document.getElementById(containerId);
    if (!records || records.length === 0) {
      box.innerHTML = `<em>No records for ${label}</em>`;
      return;
    }
    let html = `<strong>${label}:</strong><ul>`;
    records.forEach(r => {
      html += `<li>${r.army_no} - ${r.rank} ${r.name}</li>`;
    });
    html += '</ul>';
    box.innerHTML = html;
  }

  // PPT Chart
  const pptChart = new Chart(document.getElementById('pptChart'), {
    type: 'doughnut',
    data: { labels: pptLabels, datasets: [{ data: pptData, backgroundColor: ['#0d6efd','#20c997','#ffc107','#dc3545'] }] },
    options: {
      onClick: (evt, elements) => {
        if (elements.length > 0) {
          const idx = elements[0].index;
          const label = pptLabels[idx];
          showDetails('pptDetails', pptDetails[label], label);
        }
      }
    }
  });

  // BPET Chart
const bpetChart = new Chart(document.getElementById('bpetChart'), {
  type: 'bar',
  data: { 
    labels: bpetLabels, 
    datasets: [{ 
      data: bpetData, 
      backgroundColor: ['#198754','#0dcaf0','#ffc107','#dc3545'] 
    }] 
  },
  options: {
    plugins: {
      legend: {
        display: false   // 🔴 Legend removed
      }
    },
    onClick: (evt, elements) => {
      if (elements.length > 0) {
        const idx = elements[0].index;
        const label = bpetLabels[idx];
        showDetails('bpetDetails', bpetDetails[label], label);
      }
    }
  }
});


  // Firing Chart
  const firingChart = new Chart(document.getElementById('firingChart'), {
    type: 'pie',
    data: { labels: firingLabels, datasets: [{ data: firingData, backgroundColor: ['#6f42c1','#0d6efd','#20c997','#ffc107'] }] },
    options: {
      onClick: (evt, elements) => {
        if (elements.length > 0) {
          const idx = elements[0].index;
          const label = firingLabels[idx];
          showDetails('firingDetails', firingDetails[label], label);
        }
      }
    }
  });
</script>

</body>
</html>
