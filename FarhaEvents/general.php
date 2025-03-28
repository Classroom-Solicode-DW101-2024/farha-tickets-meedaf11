<?php

require_once 'config.php';
$userID = $_SESSION['user_id'];
$f_Name = $_SESSION['first_Name'];
$l_Name = $_SESSION['last_Name'];
$email = $_SESSION['email'];

if (isset($_POST['update'])) {
    $new_f_Name = !empty($_POST['first_name']) ? $_POST['first_name'] : $f_Name;
    $new_l_Name = !empty($_POST['last_name']) ? $_POST['last_name'] : $l_Name;
    $new_email = !empty($_POST['email']) ? $_POST['email'] : $email;
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_f_Name === $f_Name && $new_l_Name === $l_Name && $new_email === $email && empty($password) && empty($confirm_password)) {
        echo "<script>alert('Please Change At least 1 Information Or change Password To Update Your Profile');</script>";
    } else {

        if (!empty($password) && !empty($confirm_password)) {
            if ($password === $confirm_password) {
                updateUserInfo($new_f_Name, $new_l_Name, $new_email, $password, $userID);
                $_SESSION['first_Name'] = $new_f_Name;
                $_SESSION['last_Name'] = $new_l_Name;
                $_SESSION['email'] = $new_email;
                echo "<script>alert('User information updated successfully.');</script>";
            } else {
                echo "<script>alert('Passwords do not match.');</script>";
            }
        } else {
            updateUserInfo($new_f_Name, $new_l_Name, $new_email, null, $userID);
            $_SESSION['first_Name'] = $new_f_Name;
            $_SESSION['last_Name'] = $new_l_Name;
            $_SESSION['email'] = $new_email;
            echo "<script>alert('User information updated successfully.');</script>";
        }
    }

    }

    

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="general.css">
</head>
<body>
    <div class="profileContainer">
        <h2>Update User Information</h2>
        
        <form method="post">
          
            <input type="text" name="first_name" placeholder="First Name" value="<?= htmlspecialchars($f_Name) ?>" required>
            <input type="text" name="last_name" placeholder="Last Name" value="<?= htmlspecialchars($l_Name) ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email) ?>" required>
            <input type="password" name="password" placeholder="New Password" >
            <input type="password" name="confirm_password" placeholder="Confirm New Password" >
            <button type="submit" name="update">Update Information</button>
        </form>
    </div>
</body>
</html>