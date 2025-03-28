<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $billetId = $_POST['billetId'] ?? '';
    $eventName = $_POST['eventName'] ?? '';
    $eventDate = $_POST['eventDate'] ?? '';
    $eventTime = $_POST['eventTime'] ?? '';
    $tarifType = $_POST['tarifType'] ?? '';
    $tarifPrice = $_POST['tarifPrice'] ?? '';
    $PlaceNumber = $_POST['placeNum'] ?? '';
    $SalleNum = $_POST['SalleNum'] ?? '';

} else {
    echo "Invalid request!";
    exit;
}


?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=League+Spartan:wght@100..900&family=Lora:ital,wght@0,400..700;1,400..700&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Red+Hat+Text:ital,wght@0,300..700;1,300..700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    <title>Ticket Soirée Marocaine</title>
    <link rel="stylesheet" href="ticket.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

</head>

<body>
    <div class="ticket" id="ticket">
        <div class="ticket-left">
            <div class="left_section">
                <div class="ticket-number">#<?=$billetId?></div>
                <div class="ticket-num-title">Numéro de ticket</div>
            </div>
        </div>

        <div class="ticket-right">
            <div class="event-title"><?=$eventName?></div>
            <div class="event-date"><?php echo date('l, d F Y', strtotime($eventDate)) . ' at ' . $eventTime; ?></div>

            <div class="organization">
                <div class="dot"></div>
                ASSOCIATION FARHA
            </div>

            <div class="ticket-details">
                <p style="margin-bottom: 5px;"><strong>Tarif :</strong></p>
                <p style="margin-bottom: 15px;">MAD <?=$tarifPrice?></p>

                <p style="margin-bottom: 5px;"><strong>Type :</strong></p>
                <p style="margin-bottom: 15px;">Tarif <?=$tarifType?></p>

                <p style="margin-bottom: 10px;"><strong>Addresse :</strong></p>
                <p>Centre Culturel Farha, Tanger</p>
            </div>

            <div class="divider"></div>
            <div class="circle-top"></div>
            <div class="circle-bottom"></div>

            <div class="ticket-barcode">
                <div class="barcode"></div>
                <div class="seat-place">
                    <div>PLACE</div>
                    <div class="place-number"><?=$PlaceNumber?></div>
                </div>
                <div class="seat-place">
                    <div>SALLE</div>
                    <div class="seat-number"><?=$SalleNum?></div>
                </div>
            </div>

            <div class="dots">
                <div class="dot-pattern"></div>
            </div>
        </div>
    </div>

    <div class="button-container">
        <button onclick="downloadTicket()">Download Ticket</button>
    </div>

</body>


<script>
    function downloadTicket() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'mm', 'a4'); // الوضع الأفقي

    const ticketElement = document.getElementById("ticket"); // تأكد من صحة الـ ID

    if (!ticketElement) {
        alert("Erreur: Ticket introuvable !");
        return;
    }

    html2canvas(ticketElement, { scale: 2 }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        
        const pageWidth = 297; 
        const pageHeight = 210; 

        const imgWidth = 180;
        const imgHeight = (canvas.height * imgWidth) / canvas.width; 

        const x = (pageWidth - imgWidth) / 2;
        const y = (pageHeight - imgHeight) / 2;

        doc.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);
        doc.save(`ticket_${Date.now()}.pdf`); 
    }).catch(error => {
        console.error("Erreur lors de la génération du ticket:", error);
        alert("Une erreur s'est produite lors de la génération du ticket.");
    });
}

</script>

</html>