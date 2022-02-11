<?php
include '../template.php';

$template = new Template($db);
$template->session_check('client');

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;
$record_id = 0;

$amount = 0;
$balance = 0;

$stmt = $db->prepare('SELECT * FROM customers WHERE user_id=:user_id LIMIT :current_page, :record_per_page');
$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->bindValue(':current_page', ($page-1)*$records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':record_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$num_clients = $db->query('SELECT COUNT(*) FROM customers')->fetchColumn();
?>

<?=$template->dashboard_header_template('Subclients', $_SESSION['name'], $_SESSION['role'])?>
<div class="container">
	<div class="content read">
		<h2>Sub Clients</h2>
		<a href="addcustomer.php" class="btn btn-sm btn-primary btn-icon-split float-right">
            <span class="icon text-white-50">
                <i class="fas fa-arrow-down"></i>
            </span>
            <span class="text">Add Subclient</span>
        </a>
        <br>
        <br>
		<table>
		    <thead>
		        <tr>
		            <td>#</td>
		            <td>Name</td>
		            <td>CardNumber</td>
		            <td>Units</td>
		            <td>Amount</td>
		            <td>Payment</td>
		            <td>Balance</td>
		            <td>Status</td>
		            <td>Action</td>
		        </tr>
		    </thead>
		    <tbody>
		        <?php foreach ($customers as $customer): ?>
		        	<?php $record_id++ ?>
					<?php $amount = ($customer['meter_reading']*15) ?>
					<?php $balance = ($customer['payment']-$amount) ?>
		        <tr>
		            <td><?=$record_id?></td>
		            <td><?=$customer['name']?></td>
		            <td><?=$customer['card_no']?></td>
		            <td><?=$customer['meter_reading']?></td>
		            <td><?=$amount?></td>
		            <td><?=$customer['payment']?></td>
		            <td><?=$balance?></td>
		            <td><?=$customer['status']==1?"Active":"Paused"?></td>
		            <td class="">
                       	<a href="customer.php?id=<?=$customer['id']?>" class="btn btn-sm btn-success btn-icon-split">
                            <span class="icon text-white-50">
                                <i class="fas fa-eye"></i>
                            </span>
                            <span class="text">View</span>
                        </a>
                       	<a href="edit_customer.php?id=<?=$customer['id']?>" class="btn btn-sm btn-info btn-icon-split">
                            <span class="icon text-white-50">
                                <i class="fas fa-pen"></i>
                            </span>
                            <span class="text">Edit</span>
                        </a>
                       	<a href="delete_customer.php?id=<?=$customer['id']?>" class="btn btn-sm btn-danger btn-icon-split">
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
			<a href="read.php?page=<?=$page-1?>"><i class="fas fa-angle-double-left fa-sm"></i></a>
			<?php endif; ?>
			<?php if ($page*$records_per_page < $num_clients): ?>
			<a href="read.php?page=<?=$page+1?>"><i class="fas fa-angle-double-right fa-sm"></i></a>
			<?php endif; ?>
		</div>
		</div>	
</div>
<?=$template->footer_template('Dashboard')?>
