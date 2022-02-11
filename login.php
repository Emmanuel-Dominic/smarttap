<?php
include 'template.php';

$template = new Template($db);
$errorMsg = '';

if (isset($_SESSION["admin"])) {
    header("Location: admin/index.php");
}

if (isset($_SESSION["client"])) {
    header("Location: client/index.php");
}

if (isset($_REQUEST['login'])) {
    $email = $_REQUEST["email"];
    $password = $_REQUEST["password"];

    if (empty($email)) {
        $errorMsg = "Please enter email!";
    } elseif (empty($password)) {
        $errorMsg = "Please enter password!";
    } elseif ($email AND $password) {
        try {
            $select_stmt = $db->prepare("SELECT id, name, username, email, password, role FROM users WHERE email=:uemail AND password=:upassword");
            $select_stmt->bindParam(":uemail", $email);
            $select_stmt->bindParam(":upassword", $password);
            $select_stmt->execute();

            while ($row=$select_stmt->fetch(PDO::FETCH_ASSOC)) {
                $dbid = $row["id"];
                $dbemail = $row["email"];
                $dbpassword = $row["password"];
                $dbrole = $row["role"];
                $dbuname = $row["username"];
                $dbname = $row["name"];
            }

            if ($email!=null AND $password!=null) {
                if ($select_stmt->rowCount()>0) {
                    if ($email==$dbemail AND $password==$dbpassword) {
                        $_SESSION['user_id']=$dbid;
                        $_SESSION['name']=$dbname;
                        $_SESSION['username']=$dbuname;
                        $_SESSION['role']=$dbrole;
                        if ($dbrole=="admin") {
                            $loginMsg = "Admin...Successfully loggedIn";
                            header("location: admin/index.php");
                        }else if($dbrole=="client") {
                            $loginMsg = "Client...Successfully loggedIn";
                            header("location: client/index.php");
                        }else {
                            $errorMsg = "Wrong email or password entered!";
                        }
                    } else {
                        $errorMsg = "Wrong email or password entered!";
                    }
                } else {
                    $errorMsg = "Wrong email or password entered!";
                }
            } else {
                $errorMsg = "Wrong email or password entered!";
            }
        } catch(PDOException $e) {
            $e->getMessage();
        }
    } else {
        $errorMsg = "Wrong email or password entered!";
    }
}
?>


<?=$template->home_header_template('Login')?>
<div class="content-view">
    <div class="login-container">
        <div class="bb-login">
            <form action="login.php" method="post" class="bb-form validate-form">
                <span class="bb-form-title p-b-26"> Login </span>
                <span class="bb-form-title p-b-48">
                    <i class="mdi mdi-symfony"></i>
                </span>
                <?php if ($errorMsg): ?>
                    <p class="text-danger"><?=$errorMsg?></p>
                <?php endif; ?>
                <div class="wrap-input100 validate-input" data-validate="Valid email is: a@b.c">
                    <label for="meter">Email:</label>
                    <input class="input100" type="text" name="email" autocomplete="off">
                </div>
                <div class="wrap-input100 validate-input" data-validate="Enter password">
                    <label for="meter">Password:</label>
                    <input class="input100" type="password" name="password" autocomplete="off">
                </div>
                <div class="login-container-form-btn">
                    <div class="bb-login-form-btn">
                        <div class="bb-form-bgbtn"></div>
                        <button type="submit" name="login" class="bb-form-btn">Login</button>
                    </div>
                </div>
                <div class="text-center p-t-115"> <span class="txt1"> Don’t have an account? </span> <a class="txt2" href="register.php"> Sign Up </a> </div>
            </form>
        </div>
    </div>
</div>
<?=$template->footer_template('Login')?>
