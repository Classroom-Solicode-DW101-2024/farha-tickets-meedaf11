<?php
    require 'config.php';

    if (!isset($_SESSION['isLogin']) || $_SESSION['isLogin'] !== true) {
        header('Location: login.php');
        exit();
    }

    if (isset($_GET['logout'])) {
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit();
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="profile.css">
    <title>Document</title>
</head>

<body id="body1">


    <aside>

        <h2 class="profileTitle">My Profile</h2>
        <hr>
        <a href="?section=general">General</a>
        <a href="?section=myTickets">My Tickets</a>
        <a href="?section=myFacture">Facture</a>
        <a href="index.php">Go Home</a>
        <a href="profile.php?logout=true">Logout</a>

    </aside>

    <div class="MainProfileSection">

    <?php
       
        if (isset($_GET['section'])) {
            if ($_GET['section'] === 'myTickets') {
                
                include('myTickets.php');
            }elseif ($_GET['section'] === 'general') {
                
                include('general.php');
            }elseif($_GET['section'] === 'myFacture'){
                include('Facture.php');
            }
           
        } else {
           
            include('general.php');
        }
    ?>
        

    </div>



</body>

</html>