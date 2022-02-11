<?php
include '../template.php';

$template = new Template($db);

$clientId = $_GET['id'];
$meter_no = $_GET['meter_no'];

if ($_GET['parameter']=='ph') {
    $stmt2 = $db->prepare('SELECT DISTINCT meter_no, DATE(created_at) AS dates, ph AS units FROM parameters WHERE meter_no=? GROUP BY DATE(created_at) LIMIT 7');
} else if ($_GET['parameter']=='tds') {
    $stmt2 = $db->prepare('SELECT DISTINCT meter_no, DATE(created_at) AS dates, tds AS units FROM parameters WHERE meter_no=? GROUP BY DATE(created_at) LIMIT 7');
} else if ($_GET['parameter']=='pressure') {
    $stmt2 = $db->prepare('SELECT DISTINCT meter_no, DATE(created_at) AS dates, pressure AS units FROM parameters WHERE meter_no=? GROUP BY DATE(created_at) LIMIT 7');
} else if ($_GET['parameter']=='temperature') {
    $stmt2 = $db->prepare('SELECT DISTINCT meter_no, DATE(created_at) AS dates, temperature AS units FROM parameters WHERE meter_no=? GROUP BY DATE(created_at) LIMIT 7');
} else if ($_GET['parameter']=='turbidity') {
    $stmt2 = $db->prepare('SELECT DISTINCT meter_no, DATE(created_at) AS dates, turbidity AS units FROM parameters WHERE meter_no=? GROUP BY DATE(created_at) LIMIT 7');
} 
// else {
//     header('Location: account.php');
// }

$stmt2->execute([$meter_no]);

while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    $units[] = $row['units'];
    $month[] = date_format(date_create($row["dates"]), "D m y");
}

?>

<?=$template->dashboard_header_template('Dashboard', $_SESSION['name'], $_SESSION['role'])?>
<div class="container-fluid Content-view">
    <a href="account.php?id=<?=$clientId?>" class="btn btn-sm btn-primary btn-icon-split float-left mt-3">
        <span class="icon text-white-50">
            <i class="fas fa-arrow-left"></i>
        </span>
        <span class="text">Back</span>
    </a>
    <br>
    <br>
    <div class="mt-3" style="margin:auto;width:50%;height:20%;text-align:center;align-items:center;justify-content:center;">
        <h2 class="page-header" ><?=ucwords($_GET['parameter'])?> Graph</h2>
        <canvas class="center" id="chartjs_line"></canvas>
    </div>  
    <script src="//code.jquery.com/jquery-1.9.1.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <script type="text/javascript">
        var ctx = document.getElementById("chartjs_line").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels:<?php echo json_encode($month); ?>,
                datasets: [{
                    backgroundColor: [
                        "#5969ff",
                        "#ff407b",
                        "#25d5f2",
                        "#ffc750",
                        "#2ec551",
                        "#7040fa",
                        "#ff004e"
                    ],
                    data:<?php echo json_encode($units); ?>,
                }]
            },
            options: {
                legend: {
                    display: true,
                    position: 'bottom',

                    labels: {
                        fontColor: '#71748d',
                        fontFamily: 'Circular Std Book',
                        fontSize: 14,
                    }
                },
            }
        });
    </script>
</div>
<?=$template->footer_template('Dashboard')?>
