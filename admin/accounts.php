<?php
include '../template.php';

$template = new Template($db);
$template->session_check("admin");

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;
$record_id = 0;
$amount = 0;
$balance = 0;

$stmt = $db->prepare('SELECT * FROM accounts WHERE user_id!=:user_id LIMIT :current_page, :record_per_page');
$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->bindValue(':current_page', ($page-1)*$records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':record_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

function users_id($user_id)
{
	$database = new DB();
	$db = $database->connect();
	$stmt1 = $db->prepare('SELECT * FROM users WHERE id=? LIMIT 1');
	$stmt1->execute([$user_id]);
	$user = $stmt1->fetch(PDO::FETCH_ASSOC);
	return $user['name'];
}

$num_accounts = $db->query('SELECT COUNT(*) FROM accounts')->fetchColumn();

$stmt1 = $db->prepare('SELECT * FROM clearance Limit 1');
$stmt1->execute();
$clearance = $stmt1->fetch(PDO::FETCH_ASSOC);
if (!$clearance) {
	exit('clearance table doesn\'t exist!');
}

if (isset($_REQUEST['clearBtn'])) {
	$periods = isset($_POST['periods']) ? $_POST['periods'] : '';
	$numbers = isset($_POST['numbers']) ? $_POST['numbers'] : '';
	$setTimeFromNow = date("Y-m-d h:i:s", strtotime("+".$numbers." ".$periods, strtotime(date("Y-M-d h:i:s"))));
	$stmt2 = $db->prepare('UPDATE clearance SET clearance_date=?, updated_at=now() WHERE id=?');
	$stmt2->execute([$setTimeFromNow, $clearance['id']]);
	$msg = 'Successfully Updated!';
	header('Location: accounts.php');
}
?>

<?=$template->dashboard_header_template('Accounts', $_SESSION['name'], $_SESSION['role'])?>
<div class="container">
	<div class="content read">
		<h2>Clients</h2>
		<script type="text/javascript" src="../vendor/jquery/jquery.min.js"></script>
    	<script>
			$(document).ready(function () {
				var ddlNums = $("#numbers > option");
				$("#periods").on('change', function () {
					var selectedItem = $(this).val();
					if (selectedItem == "months") {
						ddlNums.slice(0).show();
						ddlNums.slice(-47).hide();
					} else if(selectedItem == "days") {
						ddlNums.slice(0).show();
						ddlNums.slice(-30).hide();
					} else if(selectedItem == "hours") {
						ddlNums.slice(0).show();
						ddlNums.slice(-36).hide();
					} else {
						ddlNums.slice(0).show();
					}
				});
			});
		</script>
		<form class="col-md-4 float-left in-line" action="accounts.php" method="post">
			<select class="col-md-4 m-1" id="periods" name="periods">
				<option value="minutes" selected>Minutes</option>
				<option value="hours">Hours</option>
				<option value="days">Days</option>
				<option value="months">Months</option>
			</select>
			<select class="col-md-3 m-1" id="numbers" name="numbers">
				<option value="1" selected>1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
				<option value="13">13</option>
				<option value="14">14</option>
				<option value="15">15</option>
				<option value="16">16</option>
				<option value="17">17</option>
				<option value="18">18</option>
				<option value="19">19</option>
				<option value="20">20</option>
				<option value="21">21</option>
				<option value="22">22</option>
				<option value="23">23</option>
				<option value="24">24</option>
				<option value="25">25</option>
				<option value="26">26</option>
				<option value="27">27</option>
				<option value="28">28</option>
				<option value="29">29</option>
				<option value="30">30</option>
				<option value="31">31</option>
				<option value="32">32</option>
				<option value="33">33</option>
				<option value="34">34</option>
				<option value="35">35</option>
				<option value="36">36</option>
				<option value="37">37</option>
				<option value="38">38</option>
				<option value="39">39</option>
				<option value="40">40</option>
				<option value="41">41</option>
				<option value="42">42</option>
				<option value="43">34</option>
				<option value="44">44</option>
				<option value="45">45</option>
				<option value="46">46</option>
				<option value="47">47</option>
				<option value="48">48</option>
				<option value="49">49</option>
				<option value="50">50</option>
				<option value="51">51</option>
				<option value="52">52</option>
				<option value="53">53</option>
				<option value="54">54</option>
				<option value="55">55</option>
				<option value="56">56</option>
				<option value="57">57</option>
				<option value="58">58</option>
				<option value="59">59</option>
			</select>

			<button type="submit" name="clearBtn" class="btn btn-sm btn-primary btn-icon-split mb-2">
				<span class="icon text-white-50">
                	<i class="fas fa-arrow-up"></i>
				</span>
				<span class="text">submit</span>
			</button>
		</form>
 		<a href="addaccount.php" class="btn btn-sm btn-primary btn-icon-split float-right">
            <span class="icon text-white-50">
                <i class="fas fa-arrow-down"></i>
            </span>
            <span class="text">Add Client</span>
        </a>
        <br>
        <br>
		<table>
		    <thead>
		        <tr>
		            <td>#</td>
		            <td>Name</td>
		            <td>MeterNo</td>
		            <td>Units</td>
		            <td>Amount</td>
		            <td>Payment</td>
		            <td>Balance</td>
		            <td>GPS_Cordinates</td>
		            <td>Status</td>
		            <td>Action</td>
		        </tr>
		    </thead>
		    <tbody>
		        <?php foreach ($accounts as $account): ?>
		        	<?php $record_id++ ?>
		        	<?php $amount=($account['meter_reading']*4.95) ?>
		        	<?php $balance=($account['payment']-$amount) ?>
		        <tr>
		            <td><?=$record_id?></td>
		            <td><?=users_id($account['user_id'])?></td>
		            <td><?=$account['meter_no']?></td>
		            <td><?=$account['meter_reading']?></td>
		            <td><?=$amount?></td>
		            <td><?=$account['payment']?></td>
		            <td><?=$balance?></td>
		            <td><?=$account['gps_cordinates']?></td>
		            <td><?=$account['status']==1?"Active":"Paused";?></td>
		            <td class="">
		            	<a href="account.php?id=<?=$account['user_id']?>" class="btn btn-sm btn-success btn-icon-split">
                            <span class="icon text-white-50">
                                <i class="fas fa-eye"></i>
                            </span>
                            <span class="text">View</span>
                        </a>
                       	<a href="edit_account.php?id=<?=$account['user_id']?>" class="btn btn-sm btn-info btn-icon-split">
                            <span class="icon text-white-50">
                                <i class="fas fa-pen"></i>
                            </span>
                            <span class="text">Edit</span>
                        </a>
                       	<a href="delete_account.php?id=<?=$account['user_id']?>" class="btn btn-sm btn-danger btn-icon-split">
                            <span class="icon text-white-50">
                                <i class="fas fa-trash"></i>
                            </span>
                            <span class="text">Delete</span>
                        </a>
		            </td>
		        </tr>
		        <?php endforeach; ?>
		    </tbody>
		</table>
		<div class="pagination">
			<?php if ($page > 1): ?>
			<a href="accounts.php?page=<?=$page-1?>"><i class="fas fa-angle-double-left fa-sm"></i></a>
			<?php endif; ?>
			<?php if ($page*$records_per_page < $num_accounts): ?>
			<a href="accounts.php?page=<?=$page+1?>"><i class="fas fa-angle-double-right fa-sm"></i></a>
			<?php endif; ?>
		</div>
		</div>	
</div>
<?=$template->footer_template('Dashboard')?>
