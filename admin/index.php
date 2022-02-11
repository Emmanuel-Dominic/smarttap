<?php
include '../template.php';

$template = new Template($db);
?>

<?=$template->dashboard_header_template('Dashboard', $_SESSION['name'], $_SESSION['role'])?>
<div class="container-fluid Content-view"> 
    <script type="text/javascript" src="../vendor/jquery/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var shownIds = new Array();

            setInterval(function(){
                $.get("params.php", function(data){
                    data = $.parseJSON(data);
                    for(var i=0; i<data.length; i++){
                        if($.inArray(data[i]["id"], shownIds) == -1){
                            var livedata=`<div class="col-md-3 col-sm-6 mb-4"><div class="card border-left-`+data[i]["color"]+` shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-`+data[i]["color"]+` text-uppercase mb-1">`+data[i]["name"]+`</div><div class="row no-gutters align-items-center"><div class="col-auto"><div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">`+data[i]["value"]+`<small class="text-gray-600">`+data[i]["unit"]+`</small></div></div></div></div><div class="col-auto"><i class="fas fa-`+data[i]["icon"]+` fa-2x text-gray-300"></i></div></div></div></div></div>`;
                            $("#livedata").append(livedata);
                            shownIds.push(data[i]["id"]);
                            // break;
                        }
                    }
                });
            }, 1000);
        });
    </script>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>
    <div class="row" id="livedata"></div>
</div>
<?=$template->footer_template('Dashboard')?>
