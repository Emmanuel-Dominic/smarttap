<?php
include '../template.php';

$template = new Template($db);
$template->session_check("client");

$passId = $_GET['id'];
$stmt1 = $db->prepare('SELECT * FROM customers WHERE id = ? Limit 1');
$stmt1->execute([$passId]);
$customer = $stmt1->fetch(PDO::FETCH_ASSOC);
if (!$customer) {
    header('location: customers.php');
    // exit('Account doesn\'t exist with that ID!');
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
                $.get(`details.php?id=${passId}`, function(data){
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
        <h1 class="h3 mb-0 text-gray-800">SubClient's Data</h1>
    </div>
    <input type="text" id="passId" hidden="hidden" value="<?=$passId?>" />
    <div class="row" id="livedata"></div>
    <div class="wrapper d-flex justify-content-center flex-column px-2 pb-2">
        <form class="register-form">
            <div class="row my-4">
                <div class="col-md-6">
                    <b>Full Name: <?=$customer['name']?></b>
                </div>
                <div class="col-md-6 pt-md-0 pt-4">
                    <b>UserName: <?=$customer['username']?></b>
                </div>
            </div>
            <div class="row my-md-4 my-2">
                <div class="col-md-6">
                    <b>Email: <?=$customer['email']?></b>
                </div>
                <div class="col-md-6 pt-md-0 pt-4">
                    <b>PhoneNumber: <?=$customer['phone']?></b>
                </div>
            </div>
            <div class="row my-md-4 my-2">
                <div class="col-md-6">
                    <b>CardNumber: <?=$customer['card_no']?></b>
                </div>
                <div class="col-md-6 pt-md-0 pt-4">
                    <b>MeterReading: <?=$customer['meter_reading']?></b>
                </div>
            </div>
            <div class="row my-md-4 my-2">

                <div class="col-md-6">

                    <b>Amount: <?=$customer['amount']?></b>

                </div>

                <div class="col-md-6 pt-md-0 pt-4">

                    <b>Balance: <?=$customer['balance']?></b>

                </div>

            </div>

        </form>

    </div>   

</div>

<?=$template->footer_template('Dashboard')?>

