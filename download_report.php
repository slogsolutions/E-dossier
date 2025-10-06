<?php
include 'connect.php';

// Get type parameter
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Query based on type (JOIN with personal_details)
switch($type){
    case 'bpet':
        $query = "SELECT ps.army_no, pd.rank, pd.name, pd.trade, ps.bpet_date, ps.bpet_result
                  FROM physical_standards ps
                  JOIN personal_details pd ON ps.army_no = pd.army_no";
        $title = "BPET Report";
        $dateField = "ps.bpet_date";
        break;
    case 'ppt':
        $query = "SELECT ps.army_no, pd.rank, pd.name, pd.trade, ps.ppt_date, ps.ppt_result
                  FROM physical_standards ps
                  JOIN personal_details pd ON ps.army_no = pd.army_no";
        $title = "PPT Report";
        $dateField = "ps.ppt_date";
        break;
    case 'firing':
        $query = "SELECT ps.army_no, pd.rank, pd.name, pd.trade, ps.firing_date, ps.firing_result
                  FROM physical_standards ps
                  JOIN personal_details pd ON ps.army_no = pd.army_no";
        $title = "Firing Report";
        $dateField = "ps.firing_date";
        break;
    case 'overweight':
        $query = "SELECT ps.army_no, pd.rank, pd.name, pd.trade, pd.height, pd.weight, pd.medical_specific, pd.overweight_percentage
                  FROM physical_standards ps
                  JOIN personal_details pd ON ps.army_no = pd.army_no";
        $title = "Overweight Report";
        $dateField = ""; // no date field here
        break;
    default:
        die("Invalid report type.");
}

$result = $conn->query($query);
$rows = $result->fetch_all(MYSQLI_ASSOC);

// Format dates (DD-MM-YYYY)
foreach ($rows as &$row) {
    foreach ($row as $key => $val) {
        if (preg_match("/\d{4}-\d{2}-\d{2}/", $val)) {
            $row[$key] = date("d-m-Y", strtotime($val));
        }
    }
}
unset($row);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $title ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg,#f0f4f7,#e0eafc);
      padding: 30px;
      animation: fadeInBody 1s ease-in;
    }
    @keyframes fadeInBody { from { opacity: 0; } to { opacity: 1; } }

    .container {
      background: #fff;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      animation: slideUp 0.8s ease-out;
    }
    @keyframes slideUp { from { transform: translateY(40px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

    h2 {
      text-align:center;
      margin-bottom: 20px;
      font-weight: 600;
      color: #198754;
      animation: glow 2s infinite alternate;
    }
    @keyframes glow { from { text-shadow: 0 0 5px #19875455; } to { text-shadow: 0 0 15px #198754aa; } }

    .btn {
      margin: 5px;
      border-radius: 25px;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }

    table {
      margin-top: 20px;
      border-radius: 10px;
      overflow: hidden;
    }
    th {
      background: linear-gradient(135deg,#198754,#28a745);
      color: white;
      text-align: center;
    }
    td { vertical-align: middle; }
    tr:hover td { background: #f1fdf6; }

    .filters {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
<div class="container">
  <h2><?= $title ?></h2>

  <div class="filters">
    <input type="text" id="searchBox" class="form-control" placeholder="🔍 Search..." style="max-width:250px;">
    <?php if ($dateField): ?>
    <div class="d-flex gap-2">
      <input type="date" id="minDate" class="form-control">
      <input type="date" id="maxDate" class="form-control">
    </div>
    <?php endif; ?>
    <div>
      <button id="exportExcel" class="btn btn-success px-4">📊 Excel</button>
      <button id="exportPDF" class="btn btn-danger px-4">📄 PDF</button>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="reportTable">
      <thead>
        <tr>
          <th>Sr No</th>
          <?php foreach(array_keys($rows[0]) as $col): ?>
            <th><?= ucfirst(str_replace("_"," ",$col)) ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
      <?php $sr=1; foreach($rows as $row): ?>
        <tr>
          <td><?= $sr++ ?></td>
          <?php foreach($row as $val): ?>
            <td><?= htmlspecialchars($val) ?></td>
          <?php endforeach; ?>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

<script>
$(document).ready(function(){
    let table = $('#reportTable').DataTable({
        pageLength: 10,
        order: [[0,'asc']],
        dom: 'rtip'
    });

    // Search box
    $('#searchBox').on('keyup', function(){
        table.search(this.value).draw();
    });

    // Date filter
    <?php if ($dateField): ?>
    $.fn.dataTable.ext.search.push(function(settings, data){
        let min = $('#minDate').val();
        let max = $('#maxDate').val();
        let dateCol = data[4] || ""; // adjust based on query (date field position)

        if (!dateCol) return true;

        let parts = dateCol.split("-");
        let jsDate = new Date(parts[2], parts[1]-1, parts[0]);

        if ((min=="" || new Date(min) <= jsDate) &&
            (max=="" || new Date(max) >= jsDate)) {
            return true;
        }
        return false;
    });

    $('#minDate,#maxDate').on('change', function(){ table.draw(); });
    <?php endif; ?>
});

// Excel Export
$('#exportExcel').click(function(){
    let ws = XLSX.utils.table_to_sheet(document.getElementById('reportTable'));
    for (let cell in ws) {
        if (ws.hasOwnProperty(cell) && cell[0] !== '!') {
            let v = ws[cell].v;
            if (typeof v === "string" && v.match(/^(\d{2}-\d{2}-\d{4})$/)) {
                let parts = v.split('-');
                let jsDate = new Date(parts[2], parts[1]-1, parts[0]);
                ws[cell].v = jsDate;
                ws[cell].t = 'd';
            }
        }
    }
    let wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
    XLSX.writeFile(wb, "<?= $title ?>.xlsx");
});

// PDF Export
$('#exportPDF').click(function(){
    const { jsPDF } = window.jspdf;
    let doc = new jsPDF('l','pt','a4');
    doc.setFontSize(16);
    doc.text("<?= $title ?>", 40, 40);
    doc.autoTable({
        html: '#reportTable',
        startY: 60,
        theme: 'grid',
        headStyles: { fillColor: [25,135,84] }
    });
    doc.save("<?= $title ?>.pdf");
});
</script>
</body>
</html>
