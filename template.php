<?php
include 'connection.php';

$database = new DB();
$db = $database->connect();

ob_start();
session_start();

/**
 * 
 */
class Template {

	public $conn;
	private $role = '';
	private $brand = '';
	public $title = '';
    private $sidenav = '';
	private $username = '';
	private $directory = '';
	public $home_directory = '';
	public $dashboard_directory = '../';
	private $admin = 'admin';
	private $client = 'client';
    private $headerElements = [
        0 => array(
            'view' => 'dashboard',
        ),
        1 => array(
            'view' => 'home',
        ) 
    ];
	private $sidenav_values = [
        0 => array(
            'id' => 'Two',
            'role' => 'client',
            'title' => 'Subclients',
            'heading' => 'Users',
            'name' => 'subclients',
            'value' => 'customers.php',
            'icon' => 'cog'
        ),
        1 => array(
            'id' => 'Two',
            'role' => 'admin',
            'title' => 'Users',
            'heading' => 'Users',
            'name' => 'users',
            'value' => 'users.php',
            'icon' => 'cog'
        ),
        2 => array(
            'id' => 'Utilities',
            'role' => 'admin',
            'title' => 'Clients',
            'heading' => 'Accounts',
            'name' => 'clients',
            'value' => 'accounts.php',
            'icon' => 'wrench'
        )
    ];


	function __construct($db) {
		$this->conn = $db;
	}

	public function session_check($role) {
	    if (!($_SESSION['role']==$role)) {
    		header("location: ../login.php");
		}
	}

    private function head_template($title, $dash) {
		$directory = ($dash=='Dashboard'?$this->dashboard_directory:$this->home_directory);
		echo <<<EOT
		<!DOCTYPE html>
		<html lang="en">
		<head>
		    <meta charset="utf-8">
		    <meta http-equiv="X-UA-Compatible" content="IE=edge">
		    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		    <meta name="description" content="">
		    <meta name="author" content="">
		    <title>$title</title>
		    <link href="{$directory}vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
		    <link
		        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
		        rel="stylesheet">
		    <link href="{$directory}css/sb-admin-2.css" rel="stylesheet">
		    <link href="{$directory}css/style.css" rel="stylesheet">
		    <link href="{$directory}css/register.css" rel="stylesheet">
		    <link href="{$directory}css/login.css" rel="stylesheet">
		    <style>
		    .home-bg-img {
		        background: url(img/smart3.jpg) no-repeat center !important;
		        min-height: 77vh;
		        -webkit-background-size: cover !important;
		        -moz-background-size: cover !important;
		        -o-background-size: cover !important;
		        background-size: cover !important;
		    }
		    </style>
		</head>
		<body id="page-top">
		    <div id="wrapper">
EOT;
    }

    private function sidenav_template($role) {
    	// $sidenav = $this->sidenav;
	    foreach($this->sidenav_values as $key => $value) {

	        if ($value['role']==$role) {
	            $this->sidenav = $this->sidenav.'<li class="nav-item"><a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse'.$value['id'].'"aria-expanded="true" aria-controls="collapse'.$value['id'].'"><i class="fas fa-fw fa-'.$value['icon'].'"></i><span>'.$value['title'].'</span></a><div id="collapse'.$value['id'].'" class="collapse" aria-labelledby="heading'.$value['id'].'" data-parent="#accordionSidebar"><div class="bg-white py-2 collapse-inner rounded"><h6 class="collapse-header">'.$value['heading'].':</h6><a class="collapse-item" href="'.$value['value'].'">'.$value['name'].'</a></div></div></li>';
	        }
	    }

		echo <<<EOT
			<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
			    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
			        <div class="sidebar-brand-icon rotate-n-15">
			            <i class="fas fa-water"></i>
			        </div>
			        <div class="sidebar-brand-text mx-3">Smart<span>Tap</span></div>
			    </a>
			    <hr class="sidebar-divider my-0">
			    <li class="nav-item active">
			        <a class="nav-link" href="index.php">
			            <i class="fas fa-fw fa-tachometer-alt"></i>
			            <span>Dashboard</span></a>
			    </li>
			    <hr class="sidebar-divider">
			    <div class="sidebar-heading">
			        Interface
			    </div>
			    {$this->sidenav}
			    <hr class="sidebar-divider d-none d-md-block">
			    <div class="text-center d-none d-md-inline">
			        <button class="rounded-circle border-0" id="sidebarToggle"></button>
			    </div>
			</ul>
EOT;
    }

    private function header_template($dash, $username, $role) {
	    $directory = ($dash=='Dashboard'?$this->dashboard_directory:$this->home_directory);
	    if ($dash=='Dashboard') {        
	        $this->sidenav_template($role);
	        $header = '<li class="nav-item dropdown no-arrow"><a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><div class="clear-fix"><span class="mr-2 d-none d-lg-inline text-gray-800"><b>'.$username.'</b></span><br><span class="mr-2 d-none d-lg-inline text-gray-600 text-capitalize small"><b>'.$role.'</b></span><span class="mr-2 d-none d-lg-inline text-gray-600 text-capitalize small">'.$role.'</span></div><img class="img-profile rounded-circle" src="'.$directory.'/img/undraw_profile.svg"></a><div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown"><a class="dropdown-item" href="#"><i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>Profile</a><div class="dropdown-divider"></div><a class="dropdown-item" href="'.$directory.'logout.php" ><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Logout</a></div></li>';
	    }else {
	        $brand = '<a class="nav-brand d-flex align-items-center justify-content-center" href="index.php"><div class="nav-brand-icon rotate-n-15"><i class="fas fa-water"></i></div><div class="nav-brand-text mx-3"><h5><b>Smart<span>Tap</span></b></5></div></a>';
	        $header = '<li class="nav-item"><a class="nav-link text-gray-800" href="index.php">Home</a></li><li class="nav-item"><a class="nav-link text-gray-800" href="login.php">Login</a></li>';
	    }

		echo <<<EOT
			<div id="content-wrapper" class="d-flex flex-column">
			    <div id="content">
			        <nav class="navbar navbar-expand navbar-light bg-white topbar static-top shadow">
			            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
			            <i class="fa fa-bars"></i>
			            </button>
			            {$this->brand}
			            <ul class="navbar-nav ml-auto">
			            $header
			            </ul>
			        </nav>
EOT;
    }

    public function dashboard_header_template($title, $name, $role) {
		$this->head_template($title, 'Dashboard');
		$this->header_template('Dashboard', $name, $role);
    }

    public function home_header_template($title) {
		$this->head_template($title, 'home');
		$this->header_template('home', '', '');
    }

    public function footer_template($dash) {
    	$directory = ($dash=='Dashboard'?$this->dashboard_directory:$this->home_directory);
		echo <<<EOT
		            </div>
		            <footer class="sticky-footer bg-white">
		                <div class="container my-auto">
		                    <div class="copyright text-center my-auto">
		                        <span>Copyright &copy; Smart Tap 2021</span>
		                    </div>
		                </div>
		            </footer>
		        </div>
		    </div>
		    <a class="scroll-to-top rounded" href="#page-top">
		        <i class="fas fa-angle-up"></i>
		    </a>

		    <script src="{$directory}vendor/jquery/jquery.min.js"></script>
		    <script src="{$directory}vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

		    <script src="{$directory}vendor/jquery-easing/jquery.easing.min.js"></script>

		    <script src="{$directory}js/sb-admin-2.min.js"></script>
		    <script src="{$directory}js/scripts.js"></script>
		    <script src="{$directory}js/script.js"></script>

		    <script src="{$directory}vendor/chart.js/Chart.min.js"></script>

		    <script src="{$directory}js/demo/chart-area-demo.js"></script>
		    <script src="{$directory}js/demo/chart-pie-demo.js"></script>
		</body>

		</html>
EOT;
    }
}
?>