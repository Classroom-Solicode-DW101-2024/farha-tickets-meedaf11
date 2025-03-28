<?php
require_once 'config.php';
$eventsTypes = getAllTypes();

if (!isset($_SESSION['user_id'])) {
    echo "Please log in first.";
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "SELECT idReservation FROM reservation WHERE idUser = :user_id ORDER BY idReservation DESC";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$reservations = $stmt->fetchAll();

if (empty($reservations)) {
    echo "No reservations for this user.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Tickets</title>
    <link rel="stylesheet" href="myTickets.css">
    
</head>
<body id="ticketBody">

<h2>Your Reservation Tickets</h2>

<?php
foreach ($reservations as $reservation) {
    $reservation_id = $reservation['idReservation'];

    $query_normal = "SELECT * FROM billet WHERE idReservation = :reservation_id AND typeBillet = 'normal' ORDER BY billetId DESC";
    $query_reduit = "SELECT * FROM billet WHERE idReservation = :reservation_id AND typeBillet = 'reduit' ORDER BY billetId DESC";
    
    $stmt_normal = $pdo->prepare($query_normal);
    $stmt_normal->bindParam(':reservation_id', $reservation_id);
    $stmt_normal->execute();
    $normal_tickets = $stmt_normal->fetchAll();

    $stmt_reduit = $pdo->prepare($query_reduit);
    $stmt_reduit->bindParam(':reservation_id', $reservation_id);
    $stmt_reduit->execute();
    $reduit_tickets = $stmt_reduit->fetchAll();

    if (empty($normal_tickets) && empty($reduit_tickets)) {
        continue;
    }
?>

<div class="ticketsContainer">
<div class="reservation-header">
        <h3>Reservation No: <?php echo $reservation_id; ?></h3>
        <button onclick="openFacture(<?=$reservation_id?>)" class="facture-btn">Open Facture</button>
    </div>

    <h4>Normal Tickets</h4>
    <table>
        <thead>
            <tr>
                <th>Ticket Number</th>
                <th>Type</th>
                <th>Event Name</th>
                <th>View Ticket</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($normal_tickets as $ticket) { 
            $eventDetails = getEventDetails($ticket['idReservation']);
            $eventTitle = $eventDetails['eventTitle'];
            $eventDate = $eventDetails['dateEvent'];
            $eventTime = $eventDetails['timeEvent'];
            $tarifType = $ticket['typeBillet'];
            $PlaceNumber = $ticket['placeNum'];
            $numSalle = getEventNumSalle($ticket['idReservation']);

            $eventTariffs = getEventTariffs($ticket['idReservation']);
            $normalTariff = $eventTariffs['TariffNormal'];
            ?>
                <tr>
                    <td><?php echo $ticket['billetId']; ?></td>
                    <td><?php echo $ticket['typeBillet']; ?></td>
                    <td><?php echo $eventTitle; ?></td>
                    <td class="text-center">
                        <form action="ticket.php" method="POST">
                            <input type="hidden" name="billetId" value="<?php echo $ticket['billetId']; ?>">
                            <input type="hidden" name="eventName" value="<?php echo htmlspecialchars($eventTitle, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="eventDate" value="<?php echo htmlspecialchars($eventDate, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="eventTime" value="<?php echo htmlspecialchars($eventTime, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="tarifType" value="<?php echo htmlspecialchars($tarifType, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="tarifPrice" value="<?php echo htmlspecialchars($normalTariff, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="placeNum" value="<?php echo htmlspecialchars($PlaceNumber, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="SalleNum" value="<?php echo htmlspecialchars($numSalle, ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit" class="button">View Ticket</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <h4>Discounted Tickets</h4>
    <table>
        <thead>
            <tr>
                <th>Ticket Number</th>
                <th>Type</th>
                <th>Event Name</th>
                <th>View Ticket</th>
            </tr>
        </thead>
        <tbody>
            
        <?php foreach ($reduit_tickets as $ticket) { 
            $eventDetails = getEventDetails($ticket['idReservation']);
            $eventTitle = $eventDetails['eventTitle'];
            $eventDate = $eventDetails['dateEvent'];
            $eventTime = $eventDetails['timeEvent'];
            $tarifType = $ticket['typeBillet'];
            $PlaceNumber = $ticket['placeNum'];
            $numSalle = getEventNumSalle($ticket['idReservation']);

            $eventTariffs = getEventTariffs($ticket['idReservation']);
            $reducedTariff = $eventTariffs['TariffReduit'];
        ?>
            <tr>
                <td><?php echo $ticket['billetId']; ?></td>
                <td><?php echo $ticket['typeBillet']; ?></td>
                <td><?php echo $eventTitle; ?></td>
                <td class="text-center">
                    <form action="ticket.php" method="POST">
                        <input type="hidden" name="billetId" value="<?php echo $ticket['billetId']; ?>">
                        <input type="hidden" name="eventName" value="<?php echo htmlspecialchars($eventTitle, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="eventDate" value="<?php echo htmlspecialchars($eventDate, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="eventTime" value="<?php echo htmlspecialchars($eventTime, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="tarifType" value="<?php echo htmlspecialchars($tarifType, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="tarifPrice" value="<?php echo htmlspecialchars($reducedTariff, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="placeNum" value="<?php echo htmlspecialchars($PlaceNumber, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="SalleNum" value="<?php echo htmlspecialchars($numSalle, ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="button">View Ticket</button>
                    </form>
                </td>
            </tr>
        <?php } ?>

        </tbody>
    </table>
</div>

<?php
}
?>

<script>

    function openFacture(idReservation) {
        window.location.href = "profile.php?section=myFacture&reservation_id=" + idReservation;
    }

</script>

</body>
</html>
