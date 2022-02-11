<?php
include '../template.php';

$template = new Template($db);
$template->session_check('client');

$passId = $_SESSION['user_id'];
$stmt0 = $db->prepare('SELECT * FROM users u INNER JOIN (SELECT * FROM accounts WHERE user_id = ?) a ON u.id = ? Limit 1');
$stmt0->execute([$passId, $passId]);
$account = $stmt0->fetch(PDO::FETCH_ASSOC);

$stmt01 = $db->prepare('SELECT DISTINCT meter_no, DATE(created_at) AS dates, SUM(meter_reading1) AS units FROM parameters WHERE meter_no=? GROUP BY DATE(created_at)');
$stmt01->execute([$account['meter_no']]);

$stmt = $db->prepare("SELECT * FROM users INNER JOIN accounts ON users.id=accounts.user_id INNER JOIN wallet ON users.id=wallet.user_id WHERE id =$passId  Limit 1");
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

while ($row = $stmt01->fetch(PDO::FETCH_ASSOC)) {
    $units[] = $row['units'];
    $month[] = date_format(date_create($row["dates"]), "D m y");
}

$clearance = $db->prepare('SELECT * FROM clearance Limit 1');
$clearance->execute([]);
$timedate = $clearance->fetch(PDO::FETCH_ASSOC);

$end = new DateTime($timedate['clearance_date']);
$now = new DateTime();

function secondsToTime($inputSeconds) {
    $secondsInAMinute = 60;
    $secondsInAnHour  = 60 * $secondsInAMinute;
    $secondsInADay    = 24 * $secondsInAnHour;
    $secondsInAWeek    = 7 * $secondsInADay;
    // extract weeks
    $weeks = floor($inputSeconds / $secondsInAWeek);
    // extract days
    $days = floor($inputSeconds / $secondsInADay);
    // extract hours
    $hourSeconds = $inputSeconds % $secondsInADay;
    $hours = floor($hourSeconds / $secondsInAnHour);
    // extract minutes
    $minuteSeconds = $hourSeconds % $secondsInAnHour;
    $minutes = floor($minuteSeconds / $secondsInAMinute);
    // extract the remaining seconds
    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
    $seconds = ceil($remainingSeconds);

    // return the final array
    $obj = array(
        'w' => (int) $weeks,
        'd' => (int) $days,
        'h' => (int) $hours,
        'm' => (int) $minutes,
        's' => (int) $seconds,
    );
    return $obj;
}

$timeDiference = $end->getTimestamp()-$now->getTimestamp();
$updated_at = new DateTime($timedate['updated_at']);
$updated = $updated_at->getTimestamp()-$now->getTimestamp();


if (secondsToTime($timeDiference)['w'] > 0) {
    $time = secondsToTime($timeDiference)['w']." weeks";
}elseif (secondsToTime($timeDiference)['d'] > 0) {
    $time = secondsToTime($timeDiference)['d']." days";
}elseif (secondsToTime($timeDiference)['h'] > 0) {
    $time = secondsToTime($timeDiference)['h']." hrs";
}elseif (secondsToTime($timeDiference)['m'] > 0) {
    $time = secondsToTime($timeDiference)['m']." mins";
}elseif (secondsToTime($timeDiference)['s'] > 0) {
    $time = secondsToTime($timeDiference)['s']." seconds";
}else{
    $time = secondsToTime($timeDiference)['s']." seconds";
}


if (secondsToTime($updated)['w'] > 0) {
    $setTime = secondsToTime($updated)['w']." weeks";
}elseif (secondsToTime($updated)['d'] > 0) {
    $setTime = secondsToTime($updated)['d']." days";
}elseif (secondsToTime($updated)['h'] > 0) {
    $setTime = secondsToTime($updated)['h']." hrs";
}elseif (secondsToTime($updated)['m'] > 0) {
    $setTime = secondsToTime($updated)['m']." mins";
}elseif (secondsToTime($updated)['s'] > 0) {
    $setTime = secondsToTime($updated)['s']." seconds";
}else{
    $setTime = secondsToTime($updated)['s']." seconds";
}


?>

<?=$template->dashboard_header_template('Dashboard', $_SESSION['name'], $_SESSION['role'])?>
<div class="container-fluid Content-view">
    <script type="text/javascript" src="../vendor/jquery/jquery.min.js"></script>
    
    <script type="text/javascript">

        $(document).ready(function(){
            $('.toast').toast('show');

            var shownIds = new Array();

            setInterval(function(){
            
                $.get("params.php", function(data){
                    data = $.parseJSON(data);
                    for(var i=0; i<=data.length; i++){
                        var element1 = '';
                        var element2 = 'text-gray-800';
                        var element3 = '';
                        var element4 = '';
                        
                        if(data[i]['title']=='Meter'){
                            element1='<span class="text-'+data[i]['ncolor']+'">'+data[i]['head']+'</span>';
                            element4='<div class="'+data[i]['htag']+' mb-0 mr-3 font-weight-bold text-gray-800">'+data[i]['name']+'<small class="text-gray-600">'+' '+data[i]['nunit']+'</small></div>';
                        }

                        if(data[i]['title']=='Water Safety'){
                            element2=data[i]['safecolor'];
                            element3='<span class="fas fa-dollar-sign '+data[i]['safecolor']+'"></span>';
                        }
                        if($.inArray(data[i]["id"], shownIds) == -1){
                            console.log(data[i]['id']);
                            var livedata=`<div class="col-md-3 col-sm-12 mb-4"><div class="card border-left-`+data[i]['color']+` shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-`+data[i]['color']+` text-uppercase mb-1">`+data[i]['title']+` `+element1+`</div><div class="row no-gutters align-items-center"><div class="col-auto"><div class="`+data[i]['htag']+` `+element2+` mb-0 mr-3 font-weight-bold">`+data[i]['value']+` `+element3+`<small class="text-gray-600">`+data[i]['unit']+`</small></div>`+element4+`</div></div></div><div class="col-auto"><i class="fas fa-`+data[i]['icon']+` fa-2x text-gray-300"></i></div></div></div></div></div>`;
                            $("#livedata").append(livedata);
                            shownIds.push(data[i]["id"]);
                        }
                    }
                });
            }, 1000);
        });
    </script>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <?php
            echo(($end>$now && $account['category']=='post-paid' && $account['balance']>0) ? '
                <div role="alert" aria-live="assertive" aria-atomic="true" class="toast" data-autohide="false">
                    <div class="toast-header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bell mr-2" viewBox="0 0 16 16">
                            <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z"/>
                        </svg>
                        <strong class="mr-auto">Notification!</strong>
                        <small>'.$setTime.' ago</small>
                        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="toast-body">
                        Dear '.$_SESSION["name"].', your payment deadline is due and you will be disconnected in '.$time.' from now.
                    </div>
                </div>'
             : '')
        ?>
    </div>
    <?php
        if($user['category']=="post-paid"){
            if ($user['balance']>0) {
                echo 'You have an out-standing balance of: <span>'.$user['balance'].'</span>,&nbsp;&nbsp; <a class="text-danger" href="pay_balance.php?id='.$_SESSION['user_id'].'">Pay Balance</a>';
            }
            if ($user['balance']>0 && $user['wallet_amount']>0 && $user['wallet_amount']>=$user['balance']) {
                echo ' | OR You have a wallet balance of: <span>'.$user['wallet_amount'].'</span>,&nbsp;&nbsp; <a class="text-danger" href="pay_with_wallet.php?id='.$_SESSION['user_id'].'">Pay with Wallet</a>';
            }  
        }elseif($user['category']=="pre-paid"){
            if ($user['balance']==0) {
                echo 'Please make payment before usage : &nbsp;&nbsp; <a class="text-danger" href="prepaid_payment.php?id='.$_SESSION['user_id'].'">Make Payment</a> | OR Pay with wallet balance: &nbsp;&nbsp; <a class="text-danger" href="pay_with_wallet.php?id='.$_SESSION['user_id'].'">Pay with Wallet</a>';
            }elseif ($user['balance']>0) {
                echo 'You can make payment before expiry : &nbsp;&nbsp; <a class="text-danger" href="prepaid_payment.php?id='.$_SESSION['user_id'].'">Make Payment</a> | OR Pay with wallet balance: &nbsp;&nbsp; <a class="text-danger" href="pay_with_wallet.php?id='.$_SESSION['user_id'].'">Pay with Wallet</a>';
            }    
        }else{

        }
    ?>
    <div class="row" id="livedata">
    </div>
    
    <div class="row">
        <div class="col-lg-6">
            <div class="card-wrap">
                <div class="card wallet-card animate">
                    <div class="number">
                        <div class="label">Wallet Amount</div>
                        <span><?php echo $user["wallet_amount"] ?></span>
                    </div>
                    <div class="owner-data">
                        <div class="name">
                            <div class="label">Cardholder name</div>
                            <div class="value"><?php echo $user["name"] ?></div>
                        </div>
                        <div class="validate">
                            <div class="label">Created</div>
                            <div class="value"><?php echo date("M d, Y", strtotime($user["first_deposit"])) ?></div>	
                        </div>
                    </div>
                    <div class="flag"><img src="../img/wallet.png">	  
                    </div>
                </div>
            </div>
            <br>
            <div class="wallet-button">
                <a href="load_wallet.php?id=<?=$_SESSION['user_id'] ?>" class="btn btn-danger btn-sm">Add to Wallet</a>
            </div>
        </div>

        <div class="col-lg-6">
            <div style="width:100%;height:20%;text-align:center;align-items:center;justify-content:center;">
                <h2 class="page-header" >Consumption Graph</h2>
                <canvas class="center" id="chartjs_line"></canvas>
            </div>  
        </div>
    </div>
    <br>
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
