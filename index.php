<?php
include "./autoload.php";
include 'config/database.php';
include_once "app/model/User.php";
include_once "./app/Constants.php";

error_reporting(E_ALL);
session_start();
if(isset($_SESSION['userId'])){
    header("Location: modules/0/index.php");
}

$pageTitle = "Register";
$requireLogin = false;
$accessDenied = false;
$notActive = false;
  
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo env('APP_NAME'). " | ". $pageTitle; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="title" content="Chat-App">
<meta name="author" content="Themesberg">

<!-- Fontawesome -->
<link type="text/css" href="node_modules/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
<!-- Custom CSS -->
<link type="text/css" href="assets/css/custom.css" rel="stylesheet">

</head>

<body>
    <main>
        <!-- Section -->
        <section class="min-vh-100 d-flex bg-primary align-items-center">
            <div class="container">
            <?php
                if($_POST && isset($_POST['email']) && isset($_POST['password'])){
                    $database = new Database();
                    $db = $database->getConnection();
                
                    $userModel = new User($db);
                
                    $userModel->email = $_POST['email'];
                    if($userModel->emailAlreadyExists()){
                        echo ' <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                                    Email already exits!!
                                    </div>';
                    } elseif (isset($_POST['username'])) {
                        if (isset($_POST['confirm-password']) && $_POST['password'] === $_POST['confirm-password']) {
                            $userModel->setUsername($_POST['username']);
                            $userModel->setEmail($_POST['email']);
                            $userModel->setPassword($_POST['password']);
                            if($userModel->registerNewUser()){
                                echo ' <div class="alert alert-success alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <h4><i class="fa fa fa-check"></i> Alert!</h4>
                                        Successfully Added.
                                        </div>';
                                $_POST=array();
                                header("Location: modules/0/index.php?action=login_success");
                                // $URL = $home_url."modules/0/index.php?action=login_success";
                                // echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
                                // echo '<META HTTP-EQUIV="refresh" content="0;URL=' . $URL . '">';
                            } else {
                                echo '<div class="alert alert-danger alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                            <h4><i class="fa fa fa-ban"></i> Alert!</h4>
                                            Error while inserting.....
                                        </div>';
                        
                            }
                        } else {
                            echo '<div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <h4><i class="fa fa fa-ban"></i> Password Mismatch</h4>
                                        The two password do not match.
                                    </div>';
                        }
                    } else {
                        echo '<div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="fa fa fa-ban"></i> Empty Input Field</h4>
                                    Please fill in the required field.
                                </div>';
                    }
                }

            ?>
                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-6 justify-content-center">
                        <div class="card bg-primary shadow-soft border-light p-4">
                            <div class="card-header text-center pb-0">
                                <h2 class="mb-0 h5">Create Account</h2>                               
                            </div>
                            <div class="card-body">
                                <form id="register" action="<?php  htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
                                    <div class="form-group">
                                        <label for="exampleInputIcon4">Username</label>
                                        <div class="input-group mb-4">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><span class="fas fa-user"></span></span>
                                            </div>
                                            <input class="form-control" id="username" placeholder="username" 
                                                type="text" name="username"
                                                data-parsley-pattern="/^[a-zA-Z\s]+$/" 
                                                aria-label="username" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputIcon4">Email</label>
                                        <div class="input-group mb-4">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><span class="fas fa-envelope"></span></span>
                                            </div>
                                            <input class="form-control" id="email" placeholder="example@company.com" 
                                                type="text" name="email"
                                                data-parsley-type="email" 
                                                aria-label="email adress" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <div class="input-group mb-4">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><span class="fas fa-unlock-alt"></span></span>
                                                </div>
                                                <input class="form-control" id="password" placeholder="Password" 
                                                    type="password" name="password" data-parsley-pattern="^[a-zA-Z\s]+$"
                                                    data-parsley-minlength="6" data-parsley-maxlength="12"
                                                    aria-label="password" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="confirm-password">Confirm Password</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><span class="fas fa-unlock-alt"></span></span>
                                                </div>
                                                <input class="form-control" id="confirm-password" placeholder="Confirm password" 
                                                    type="password" name="confirm-password" data-parsley-pattern="^[a-zA-Z\s]+$"
                                                    data-parsley-minlength="6" data-parsley-maxlength="12"
                                                    aria-label="confirm password" required>
                                            </div>
                                        </div>
                                        <div class="form-check mb-4">
                                            <input class="form-check-input" type="checkbox" value="" id="defaultCheck6">
                                            <label class="form-check-label" for="defaultCheck6">
                                                I agree to the <a href="#">terms and conditions</a>
                                            </label>
                                        </div>
                                    </div>
                                    <button type="submit" name="submit" class="btn btn-block btn-primary">Sign in</button>
                                </form>
                                <div class="mt-3 mb-4 text-center">
                                    <span class="font-weight-normal">or</span>
                                </div>
                                <div class="btn-wrapper my-4 text-center">
                                    <button class="btn btn-primary btn-icon-only text-facebook mr-2" type="button" aria-label="facebook button" title="facebook button">
                                        <span aria-hidden="true" class="fab fa-facebook-f"></span>
                                    </button>
                                    <button class="btn btn-primary btn-icon-only text-twitter mr-2" type="button" aria-label="twitter button" title="twitter button">
                                        <span aria-hidden="true" class="fab fa-twitter"></span>
                                    </button>
                                    <button class="btn btn-primary btn-icon-only text-facebook" type="button" aria-label="github button" title="github button">
                                        <span aria-hidden="true" class="fab fa-github"></span>
                                    </button>
                                </div>
                                <div class="d-block d-sm-flex justify-content-center align-items-center mt-4">
                                    <span class="font-weight-normal">
                                        Already have an account?
                                        <a href="#" class="font-weight-bold">Login here</a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

<!-- Core -->
<script src="node_modules/jquery/dist/jquery.min.js"></script>
<script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>
<script>
$(document).ready(function(){
    $('#register').parsley();
});
</script>