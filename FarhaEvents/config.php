<?php

    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }

    $dbHost = 'localhost';
    $dbName = 'farhaevents';
    $dbUsername = 'root';
    $dbPassword = '';

    try{

        $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName",$dbUsername,$dbPassword);
        $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }catch(PDOException $exception){
        
        die ("Échec de la connexion : " . $e->getMessage());
    }


    function getAllTypes(){

        global $pdo;
        $sql = "SELECT DISTINCT eventType FROM evenement";
        $stmt = $pdo -> prepare($sql);
        $stmt -> execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC); 
        return $results;
    
    }

    function userRegister($id, $firstName, $LastName, $Email, $Password){

        global $pdo;
        $registerSql= 'INSERT INTO utilisateur (idUser, nomUser, prenomUser, mailUser, motPasse) VALUES(:id,:fName,:lName,:email,:pass)';
        $registerStmt = $pdo->prepare($registerSql);
        $registerStmt->bindParam(":id" , $id);
        $registerStmt->bindParam(":fName" , $firstName);
        $registerStmt->bindParam(":lName" , $LastName);
        $registerStmt->bindParam(":email" , $Email);
        $registerStmt->bindParam(":pass" , $Password);
        $registerStmt->execute();

    }

    function userLogin($email,$Password){

        global $pdo;
        $loginSql = 'SELECT * from utilisateur WHERE mailUser = :email AND motPasse = :pass';
        $loginStmt = $pdo->prepare($loginSql);
        $loginStmt->bindParam(':email',$email);
        $loginStmt->bindParam(':pass',$Password);
        $loginStmt->execute();

        $result = $loginStmt->fetch(PDO::FETCH_ASSOC);

        return $result;

    }


    function getUserNumber(){

        global $pdo;
        $countSql = "SELECT COUNT(idUser) as 'numUser' FROM utilisateur;";
        $countStmt = $pdo->prepare($countSql);
        $countStmt -> execute();
        $res = $countStmt->fetch(PDO::FETCH_ASSOC);

    

        return $res['numUser']+1;

    }

    function updateUserInfo($firstName, $lastName, $email , $password, $id){

        global $pdo;
        $updateSql = "UPDATE utilisateur SET nomUser = :fName, prenomUser = :lName, mailUser = :mail, motPasse = :pass WHERE idUser = :userId";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->bindParam(':fName',$firstName);
        $updateStmt->bindParam(':lName',$lastName);
        $updateStmt->bindParam(':mail',$email);
        $updateStmt->bindParam(':pass',$password);
        $updateStmt->bindParam(':userId',$id);
        $updateStmt->execute();
    }

    function checkAvailableSeats($editionId) {
        
        global $pdo;

        $stmt = $pdo->prepare("SELECT SUM(qteBilletsNormal + qteBilletsReduit) AS totalQuantity FROM reservation WHERE editionId = :editionId");
        $stmt->bindParam(':editionId', $editionId);
        $stmt->execute();
        $reservation = $stmt->fetch();
    
        
        $totalQuantity = $reservation['totalQuantity'];
    
        
        $stmt = $pdo->prepare("SELECT NumSalle FROM edition WHERE editionId = :editionId");
        $stmt->bindParam(':editionId', $editionId);
        $stmt->execute();
        $edition = $stmt->fetch();
    
        
        $numSalle = $edition['NumSalle'];
    
        
        $stmt = $pdo->prepare("SELECT capSalle FROM salle WHERE NumSalle = :numSalle");
        $stmt->bindParam(':numSalle', $numSalle);
        $stmt->execute();
        $salle = $stmt->fetch();
    
        
        $capSalle = $salle['capSalle'];
    
        
        $remainingSeats = $capSalle - $totalQuantity;
    
        
        if ($remainingSeats <= 0) {
            return 'No places available';
        } else {
            return $remainingSeats;
        }
    }


    function getEditionDetails($editionId){

        global $pdo;
        $sql = "SELECT e.*, ev.* FROM edition e INNER JOIN evenement ev ON e.eventId = ev.eventId WHERE e.editionId = :editionId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':editionId', $editionId);
        $stmt->execute();
        $edition = $stmt->fetch(PDO::FETCH_ASSOC);

        return $edition;

    }


    function insertReservation($userId, $qteBilletsNormal, $qteBilletsReduit, $editionId) {
        global $pdo;
        $sql = "INSERT INTO reservation (qteBilletsNormal, qteBilletsReduit, editionId, idUser) VALUES (:qteBilletsNormal, :qteBilletsReduit, :editionId, :idUser)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':qteBilletsNormal', $qteBilletsNormal);
        $stmt->bindParam(':qteBilletsReduit', $qteBilletsReduit);
        $stmt->bindParam(':editionId', $editionId);
        $stmt->bindParam(':idUser', $userId);
        $stmt->execute();
        return $pdo->lastInsertId();
    }

    function getReservationDetails($reservationId) {
        global $pdo;
        $sql = "SELECT 
                    r.idReservation,
                    r.qteBilletsNormal, 
                    r.qteBilletsReduit, 
                    ev.eventTitle, 
                    ed.dateEvent, 
                    ed.timeEvent, 
                    ed.NumSalle, 
                    ev.TariffNormal, 
                    ev.TariffReduit
                FROM reservation r
                JOIN edition ed ON r.editionId = ed.editionId
                JOIN evenement ev ON ed.eventId = ev.eventId
                WHERE r.idReservation = :reservationId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':reservationId', $reservationId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function getLastPlaceNum($reservationId) {
        global $pdo;
        
        $stmt = $pdo->prepare("SELECT MAX(placeNum) AS lastPlaceNum FROM billet WHERE idReservation = :idReservation");
        $stmt->bindParam(':idReservation', $reservationId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['lastPlaceNum'] ? $result['lastPlaceNum'] : 0;
    }

    function insertTicket($typeBillet, $quantity, $reservationId) {
        global $pdo; 
        
        $lastPlaceNum = getLastPlaceNum($reservationId);
        
        for ($i = 0; $i < $quantity; $i++) {
            $newPlaceNum = $lastPlaceNum + 1; 
            
            $billetId = generateUniqueBilletId();
            
            $stmt = $pdo->prepare("INSERT INTO billet (billetId, typeBillet, placeNum, idReservation) 
                               VALUES (:billetId, :typeBillet, :placeNum, :idReservation)");
            $stmt->bindParam(':billetId', $billetId);
            $stmt->bindParam(':typeBillet', $typeBillet);
            $stmt->bindParam(':placeNum', $newPlaceNum);
            $stmt->bindParam(':idReservation', $reservationId);
            
            
            $stmt->execute();
            
            
            $lastPlaceNum = $newPlaceNum;
        }
    }

    function generateUniqueBilletId() {
        global $pdo;
        
        
        do {
            $billetId = mt_rand(1000000000, 9999999999); 
            
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM billet WHERE billetId = :billetId");
            $stmt->bindParam(':billetId', $billetId);
            $stmt->execute();
            
            $count = $stmt->fetchColumn();
        } while ($count > 0); 
        
        return $billetId;
    }
    
    function getEventDetails($reservationId) {
        global $pdo;
        $query = "
            SELECT e.eventTitle, ed.dateEvent, ed.timeEvent
            FROM billet b
            INNER JOIN reservation r ON b.idReservation = r.idReservation
            INNER JOIN edition ed ON r.editionId = ed.editionId
            INNER JOIN evenement e ON ed.eventId = e.eventId
            WHERE b.idReservation = :reservationId
            LIMIT 1
        ";
    
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':reservationId', $reservationId);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function getEventTariffs($reservationId) {
        global $pdo;
        $query = "
            SELECT e.TariffNormal, e.TariffReduit
            FROM billet b
            INNER JOIN reservation r ON b.idReservation = r.idReservation
            INNER JOIN edition ed ON r.editionId = ed.editionId
            INNER JOIN evenement e ON ed.eventId = e.eventId
            WHERE b.idReservation = :reservationId
            LIMIT 1
        ";
    
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':reservationId', $reservationId);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }
    
    function getEventNumSalle($reservationId) {
        global $pdo;
        $query = "
            SELECT ed.NumSalle
            FROM billet b
            INNER JOIN reservation r ON b.idReservation = r.idReservation
            INNER JOIN edition ed ON r.editionId = ed.editionId
            WHERE b.idReservation = :reservationId
            LIMIT 1
        ";
    
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':reservationId', $reservationId);
        $stmt->execute();
        
        return $stmt->fetchColumn(); 
    }
    
    


?>