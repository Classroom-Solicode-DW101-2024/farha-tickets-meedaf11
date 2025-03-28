<?php


    
        if(isset($_POST['goToProfile'])){
    
            if (!isset($_SESSION['isLogin']) || $_SESSION['isLogin'] !== true) {
                header('Location: login.php');
                exit();
            } else {
                header('Location: profile.php');
                exit();
            }
    
        }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="header.css">
    <title>Document</title>
</head>

<body>


<header>
        <img id="logoImg" src="images/farha_ic.png" alt="farha logo">
        <nav>
            <ul>
                
                <a href="index.php"><li>Home</li></a>
                <?php foreach($eventsTypes as $type): ?>
                    <a href="index.php?category=<?=$type['eventType']?>"><li><?=$type['eventType']?></li></a>
                    
                <?php endforeach;?>    
            </ul>
        </nav>
        <form class="btnForm" method="post">
            <button id="goToProfile" name="goToProfile">Profile</button>
        </form>
    </header>


</body>

</html>