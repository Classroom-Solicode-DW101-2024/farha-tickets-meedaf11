<?php

    require 'config.php';

    if (isset($_SESSION['isLogin']) && $_SESSION['isLogin'] === true){
        header('Location:index.php');
    }
    
    $errorMessage = '';

    if(isset($_POST['loginBtn'])){


        if(!empty($_POST['email']) && !empty($_POST['password'])){

            $email = $_POST['email'];
            $password = $_POST['password'];
            $isLogin = userLogin($email,$password);

            if(!empty($isLogin)){

                $_SESSION['user_id'] = $isLogin['idUser'];
                $_SESSION['first_Name'] = $isLogin['nomUser'];
                $_SESSION['last_Name'] = $isLogin['prenomUser'];
                $_SESSION['email'] = $isLogin['mailUser'];
                $_SESSION['isLogin'] = true;

                header('Location:index.php');
                exit();

            }else{
                $errorMessage = "This User Not Available";
            }

        }else{
            $errorMessage = "Please Fill All Feilds";
        }


    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="register.css">
    <title>Document</title>
</head>
<body>

    <div class="formContainer">

        <h2>Welcome To Farha Events</h2>

        <form method="post">

            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <?php if (!empty($errorMessage)): ?>
                <span id="errMessage"><?= $errorMessage ?></span>
            <?php endif; ?>
            <button id="loginBtn" name="loginBtn">Login</button>
            <span><a href="register.php">Don't Have an Account register</a></span>
    
        </form>

    </div>
    
    

</body>
</html>