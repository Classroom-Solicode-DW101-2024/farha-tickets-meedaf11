<?php

    require 'config.php';

    if (isset($_SESSION['isLogin']) && $_SESSION['isLogin'] === true){
        header('Location:index.php');
    }

    $errorMessage = '';

    if(isset($_POST['registerBtn'])){

        if(!empty($_POST['fName']) && !empty($_POST['lName']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['Cpassword'])){

            $id = "US" . getUserNumber();

            $firstName = $_POST['fName'];
            $lastName = $_POST['lName'];
            $email = $_POST['email'];

            if($_POST['password'] === $_POST['Cpassword']){

                $password = $_POST['password'];

                userRegister($id, $firstName, $lastName, $email, $password);

                $_SESSION['user_id'] = $id;
                $_SESSION['first_Name'] = $firstName;
                $_SESSION['last_Name'] = $lastName;
                $_SESSION['email'] = $email;
                $_SESSION['isLogin'] = true;

                header('Location:index.php');
                exit();



            }else{
                $errorMessage = "Passwords do not match";
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

            <input type="text" placeholder="First Name" name="fName" required >
            <input type="text" placeholder="Last Name" name="lName"required >
            <input type="email" name="email" placeholder="Email" required >
            <input type="password" name="password" placeholder="Password" required >
            <input type="password" name="Cpassword" placeholder="Confirm Password" required >
            <?php if (!empty($errorMessage)): ?>
                <span id="errMessage"><?= $errorMessage ?></span>
            <?php endif; ?>
            <button id="registerBtn" name="registerBtn">Register</button>
            
            <span><a href="login.php">Already Have An Account Login</a></span>
    
        </form>

    </div>
    
    

</body>
</html>