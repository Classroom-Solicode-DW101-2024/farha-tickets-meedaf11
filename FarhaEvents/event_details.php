<?php

require 'config.php';
$eventsTypes = getAllTypes();
include 'header.php';

$editionId = isset($_GET['id']) ? $_GET['id'] : null;

if($editionId){

    $edition = getEditionDetails($editionId);

    if (!$edition) {
        echo "Event not found.";
        exit;
    }

    $availableSeats = checkAvailableSeats($editionId);

    if ($availableSeats === 'No places available') {
        $btnMessage = 'Tickets Expired';
        $btnDisabled = 'disabled';  
    } else {
        $btnMessage = 'Buy Ticket';
        $btnDisabled = '';  
    }


}else {
    echo "Invalid event ID.";
    exit;

}

if(isset($_POST['buyTicket'])){

    if (!isset($_SESSION['user_id'])) {
        echo "<script>
        alert('You must be logged in to buy tickets.');
        window.location.href = 'login.php';
      </script>";
    exit;

    }


    $userId = $_SESSION['user_id'];  
    $qteBilletsNormal = isset($_POST['qteBilletsNormal']) ? (int) $_POST['qteBilletsNormal'] : 0;
    $qteBilletsReduit = isset($_POST['qteBilletsReduit']) ? (int) $_POST['qteBilletsReduit'] : 0;

    if(($qteBilletsNormal + $qteBilletsReduit) > 0){

        if( ($qteBilletsNormal + $qteBilletsReduit) <= $availableSeats){

            $reservationId = insertReservation($userId, $qteBilletsNormal, $qteBilletsReduit, $editionId);
            
            if ($qteBilletsNormal > 0) {
                insertTicket('Normal', $qteBilletsNormal, $reservationId);
            }

            
            if ($qteBilletsReduit > 0) {
                insertTicket('Reduit', $qteBilletsReduit, $reservationId);
            }
            header("Location: Facture.php?reservation_id=$reservationId");
            exit;
            

        }else{

            echo "<script>
        alert('The number of tickets you have selected is greater than the number of seats available.');
      </script>";

        }


    }else{
        echo "<script>
        alert('You must be to select at least one ticket');
      </script>";
    }



}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details</title>
    <link rel="stylesheet" href="event_details.css"> <!-- Include your stylesheet -->
</head>
<body>

<div class="event-details-container">
    <h1><?= htmlspecialchars($edition['eventTitle']) ?></h1>
    <div class="event-image">
        <img src="<?= htmlspecialchars($edition['image']) ?>" alt="<?= htmlspecialchars($edition['eventTitle']) ?>" />
    </div>
    <div class="event-description">
        <p><?= htmlspecialchars($edition['eventDescription']) ?></p>
        <p><strong>Date:</strong> <?= date('F j, Y', strtotime($edition['dateEvent'])) ?> at <?= htmlspecialchars($edition['timeEvent']) ?></p>
        <p><strong>Location:</strong> Salle <?= htmlspecialchars($edition['NumSalle']) ?></p>
        <p><strong>Normal Price: </strong><?= htmlspecialchars($edition['TariffNormal']) ?>DH / <strong>Reduced Price: </strong><?= htmlspecialchars($edition['TariffReduit'])?> DH</p>
    </div>

    <!-- Form to select number of tickets -->
    <?php if ($availableSeats !== 'No places available'): ?>
        <p class='availablePlacesP'><?= $availableSeats  ?> Available Places </p>
        <form class="detailsForm" method="POST">
            <input type="hidden" name="eventTitle" value="<?= htmlspecialchars($edition['eventTitle']) ?>">
            <input type="hidden" name="eventDate" value="<?= htmlspecialchars($edition['dateEvent']) ?>">
            <input type="hidden" name="eventTime" value="<?= htmlspecialchars($edition['timeEvent']) ?>">
            <input type="hidden" name="eventLocation" value="<?= htmlspecialchars($edition['NumSalle']) ?>">
            <input type="hidden" name="normalPrice" value="<?= htmlspecialchars($edition['TariffNormal']) ?>">
            <input type="hidden" name="reducedPrice" value="<?= htmlspecialchars($edition['TariffReduit']) ?>">
            <input type="hidden" name="qteBilletsNormal" value="<?= htmlspecialchars($qteBilletsNormal) ?>">
            <input type="hidden" name="qteBilletsReduit" value="<?= htmlspecialchars($qteBilletsReduit) ?>">

            <label for="qteBilletsNormal">Number of Normal Tickets:</label>
            <input type="number" id="qteBilletsNormal" name="qteBilletsNormal" placeholder="For Adults"><br><br>

            <label for="qteBilletsReduit">Number of Reduced Tickets:</label>
            <input type="number" id="qteBilletsReduit" name="qteBilletsReduit" placeholder="For Students or Minor"><br><br>

            <button type="submit" name="buyTicket" <?= $btnDisabled ?>><?= $btnMessage ?></button> <!-- Use the button text and disabled attribute -->
        </form>
    <?php else: ?>
        <p><?= $btnMessage ?></p> 
    <?php endif; ?>
</div>

</body>
</html>