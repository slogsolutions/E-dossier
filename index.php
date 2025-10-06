<?php
session_start();
include 'connect.php';

// Aggregate data for charts from database
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>E-Dossier Dashboard</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="js/chart.js"></script>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #eef2f3, #dfe9f3);
      min-height: 100vh;
    }
    .hero {
      text-align: center;
      padding: 80px 20px;
      background: linear-gradient(135deg, #0d6efd, #6610f2);
      color: white;
      border-radius: 0 0 40px 40px;
      margin-bottom: 40px;
    }
    .hero h1 { font-weight: 700; font-size: 3rem; }
    .quote { font-style: italic; margin-top: 15px; font-size: 1.1rem; }
    .search-box { max-width: 600px; margin: 0 auto; }
    .charts-container {
      display: grid;
      grid-template-columns: repeat(auto-fit,minmax(300px,1fr));
      gap: 20px;
      margin-top: 30px;
    }
    .chart-card { background: #fff; padding: 20px; border-radius: 20px; box-shadow: 0px 6px 15px rgba(0,0,0,0.1); }
  </style>
</head>
<body>

  <section class="hero">
    <h1>📘 E-Dossier Dashboard</h1>
    <p class="quote">"Discipline, Dedication & Documentation – The Pillars of a Strong Army."</p>
    <div class="search-box mt-4">
      <form class="d-flex" action="dashboard.php" method="GET">
        <input class="form-control me-2" type="search" name="army_no" placeholder="Enter Army No." required>
        <button class="btn btn-light btn-lg" type="submit">Search</button>
      </form>
    </div>
    <div class="mt-3">
      <a href="clerk_login.php" class="btn btn-warning btn-lg me-2">Clerk Login</a>
      <a href="co_login.php" class="btn btn-danger btn-lg">CO Login</a>
    </div>
  </section>

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

  <script>
    const pptData = <?= json_encode($pptData) ?>;
    const pptLabels = <?= json_encode($pptLabels) ?>;
    const bpetData = <?= json_encode($bpetData) ?>;
    const bpetLabels = <?= json_encode($bpetLabels) ?>;
    const firingData = <?= json_encode($firingData) ?>;
    const firingLabels = <?= json_encode($firingLabels) ?>;

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
  </script>

</body>
</html>
