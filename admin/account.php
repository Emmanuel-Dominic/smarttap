<?php
include '../template.php';

$template = new Template($db);

$passId = $_GET['id'];
$stmt1 = $db->prepare('SELECT * FROM users u INNER JOIN (SELECT * FROM accounts WHERE user_id = ?) a ON u.id = ? Limit 1');
$stmt1->execute([$passId, $passId]);
$account = $stmt1->fetch(PDO::FETCH_ASSOC);

if (!$account) {
    header('Location: accounts.php');
}

$stmt2 = $db->prepare('SELECT DISTINCT meter_no, DATE(created_at) AS dates, SUM(meter_reading1) AS units FROM parameters WHERE meter_no=? GROUP BY DATE(created_at)');
$stmt2->execute([$account['meter_no']]);

while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    $units[] = $row['units'];
    $month[] = date_format(date_create($row["dates"]), "D m y");
}

?>

<?=$template->dashboard_header_template('Dashboard', $_SESSION['name'], $_SESSION['role'])?>
<div class="container-fluid Content-view">
    <script type="text/javascript" src="../vendor/jquery/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var passId = $('#passId').val();
            var shownIds = new Array();

            setInterval(function(){
                $.get(`details.php?user_id=${passId}`, function(data){
                    data = $.parseJSON(data);
                    for(var i=0; i<data.length; i++){
                        if($.inArray(data[i]["id"], shownIds) == -1){
                            var livedata=`<div class="col-xl-4 col-md-6 mb-4"><div class="card border-left-`+data[i]["color"]+` shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-`+data[i]["color"]+` text-uppercase mb-1">`+data[i]["name"]+`</div><div class="row no-gutters align-items-center"><div class="col-auto"><div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">`+data[i]["value"]+`<small class="text-gray-600">`+data[i]["unit"]+`</small></div></div></div></div><div class="col-auto"><i class="fas fa-`+data[i]["icon"]+` fa-2x text-gray-300"></i></div></div></div></div></div>`;
                            $("#livedata").append(livedata);
                            shownIds.push(data[i]["id"]);
                        }
                    }
                });
            }, 1000);
        });
    </script>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Clients Data</h1>
        <p><?=$account['category']?></p>
    </div>
    <div class="row">
        <div class="col-md-6 progress m-3">
            <div class="progress-bar" role="progressbar" style="width: <?=$account['battery_life']?>%;" aria-valuenow="<?=$account['battery_life']?>" aria-valuemin="0" aria-valuemax="100">Battery Life <?=$account['battery_life']?>%</div>
        </div>
    </div>
    <input type="text" id="passId" hidden="hidden" value="<?=$passId?>" />
    <div class="wrapper d-flex justify-content-center flex-column px-2 pb-2">
        <form class="register-form">
            <div class="row my-4">
                <div class="col-md-6">
                    <b>Full Name: <?=$account['name']?></b>
                </div>
                <div class="col-md-6 pt-md-0 pt-4">
                    <b>UserName: <?=$account['username']?></b>
                </div>
            </div>
            <div class="row my-md-4 my-2">
                <div class="col-md-6">
                    <b>Email: <?=$account['email']?></b>
                </div>
                <div class="col-md-6 pt-md-0 pt-4">
                    <b>PhoneNumber: <?=$account['phone']?></b>
                </div>
            </div>
            <div class="row my-md-4 my-2">
                <div class="col-md-6">
                    <b>GPS Cordinates: <?=$account['gps_cordinates']?></b>
                </div>
                <div class="col-md-6 pt-md-0 pt-4">
                    <b>MeterNumber: <?=$account['meter_no']?></b>
                </div>
            </div>
            <div class="row my-md-4 my-2">
                <div class="col-md-6">
                    <b>MeterReading: <?=$account['meter_reading']?></b>
                </div>
                <div class="col-md-6 pt-md-0 pt-4">
                    <b>Payment: <?=$account['payment']?></b>
                </div>
            </div>
        </form>
    </div>
    <div class="row" id="livedata"></div>
    <div style="width:50%;height:20%;text-align:center;align-items:center;justify-content:center;margin:auto;">
        <h2 class="page-header" >Consumption Graph</h2>
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
