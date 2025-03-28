<?php
require_once 'config.php'; 

if (!isset($_GET['reservation_id'])) {
    echo "Please Select a Facture From My Tickets Page.";
    exit;
}

$reservationId = $_GET['reservation_id'];
$reservation = getReservationDetails($reservationId);


if (!$reservation) {
    echo "Reservation not found.";
    exit;
}

// print_r($reservation);

$invoice_number = $reservation['idReservation'];
$client_name = $_SESSION['first_Name'] . ' ' . $_SESSION['last_Name']; 
$client_email = $_SESSION['email'];
$event_name = $reservation['eventTitle'];;
$event_date = date('d/m/Y', strtotime($reservation['dateEvent'])) . ' à ' . $reservation['timeEvent'];
$association_name = "ASSOCIATION FARHA";
$location = "Centre Culturel Farha, Tanger";

$totalNormal = $reservation['TariffNormal'] * $reservation['qteBilletsNormal'];
$totalReduced = $reservation['TariffReduit'] * $reservation['qteBilletsReduit'];
$total = $totalNormal + $totalReduced . " MAD";
$totalQte = $reservation['qteBilletsNormal'] + $reservation['qteBilletsReduit'];

if (isset($_POST['toMyTickets'])) {
    header('Location: profile.php?section=myTickets');
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #<?php echo $invoice_number; ?></title>
    <link rel="stylesheet" href="Facture.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
       
    </style>
</head>
<body id="body12">
    
    <div id="invoice">
        <div class="header">
            <div class="company-info">
                <div class="company-name"><?php echo $association_name; ?></div>
                <div class="company-address"><?php echo $location; ?></div>
            </div>
            <div class="client-info">
                <div>Client :</div>
                <div class="client-name"><?php echo $client_name; ?></div>
                <div>Adresse email :</div>
                <div><?php echo $client_email; ?></div>
            </div>
        </div>
        
        <div class="event-info">
            <div class="event-name"><?php echo $event_name; ?></div>
            <div><?php echo $event_date; ?></div>
        </div>
        
        <div class="invoice-title">FACTURE #<?php echo $invoice_number; ?></div>
        
        <table>
            <thead>
                <tr>
                    <th>Tarif</th>
                    <th>Prix</th>
                    <th>Qté</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Normal </td>
                    <td><?= $reservation['TariffNormal']; ?> MAD</td>
                    <td><?= $reservation['qteBilletsNormal']; ?></td>
                    <td class="text-right"><?= $totalNormal; ?>MAD</td>
                </tr>
                <tr>
                    <td>Reduit </td>
                    <td><?= $reservation['TariffReduit']; ?> MAD</td>
                    <td><?= $reservation['qteBilletsReduit']; ?></td>
                    <td class="text-right"><?= $totalReduced; ?>MAD</td>
                </tr>
                <tr>
                    <td><strong>Price Total : </strong></td>
                    <td></td>
                    <td><?=  $totalQte?></td>
                    <td class="text-right"><strong><?= $total; ?></strong></td>
                </tr>
            </tbody>
        </table>
        
        <div class="thank-you">MERCI !</div>
    </div>


    <div class="button-container">
        <button onclick="downloadFacture()">Download Invoice</button>
        <form method="POST">
            <input type="hidden" name="reservation_id" value="<?php echo $reservationId; ?>">
            <button name='toMyTickets'>Show Tickets</button>
        </form>
    </div>
</body>

<script>
       function downloadFacture() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'mm', 'a4'); 

        const invoiceElement = document.getElementById("invoice"); 

        html2canvas(invoiceElement, { scale: 2 }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const imgWidth = 210; 
            const pageHeight = 297; 
            const imgHeight = (canvas.height * imgWidth) / canvas.width; 

            let position = 10;
            doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);

           
            doc.save("facture_<?php echo $invoice_number; ?>.pdf");
        });
    }


    </script>

</html>