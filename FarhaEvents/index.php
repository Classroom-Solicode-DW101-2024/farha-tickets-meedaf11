<?php

    require 'config.php';
    $eventsTypes = getAllTypes();

    if(isset($_POST['goToProfile'])){

        if (!isset($_SESSION['isLogin']) || $_SESSION['isLogin'] !== true) {
            header('Location: login.php');
            exit();
        } else {
            header('Location: profile.php');
            exit();
        }

    }

    $sql = "
        SELECT edition.*, evenement.eventTitle, evenement.eventType 
        FROM edition 
        JOIN evenement ON edition.eventId = evenement.eventId 
        WHERE 1=1
    ";
    $params = [];

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $sql .= " AND evenement.eventTitle LIKE :search";
        $params[':search'] = '%' . $_GET['search'] . '%';
    }

    // Filtre par catégorie
    if (isset($_GET['category']) && !empty($_GET['category'])) {
        $sql .= " AND evenement.eventType = :category";
        $params[':category'] = $_GET['category'];
    }

    if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
        $sql .= " AND edition.dateEvent >= :start_date";
        $params[':start_date'] = $_GET['start_date'];
    }

    if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
        $sql .= " AND edition.dateEvent <= :end_date";
        $params[':end_date'] = $_GET['end_date'];
    }

    $sql .= " ORDER BY edition.dateEvent ASC";

    $query = $pdo->prepare($sql);
    $query->execute($params);
    $editions = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>Document</title>
</head>
<body>
    

    <?php include 'header.php'; ?>

    <section class="hero">
        <div class="hero-content">
            <h1>Discover Amazing Events</h1>
            <p>Find and book tickets for the best events in your area</p>
            
            <!-- Formulaire de recherche avancée -->
            <form class="advanced-search" method="GET" action="">
                <!-- Barre de recherche par titre -->
                <div class="search-row">
                    <input type="text" name="search" placeholder="Search by event title..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    <button type="submit" class="search-btn">Search</button>
                </div>
                
                <!-- Filtres additionnels -->
                <div class="filters-container">
                    <div class="filter-group">
                        <label for="category">Category:</label>
                        <select name="category" id="category">
                            <option value="">All Categories</option>
                            <?php foreach($eventsTypes as $type): ?>
                                <option value="<?= $type['eventType'] ?>" <?= (isset($_GET['category']) && $_GET['category'] == $type['eventType']) ? 'selected' : '' ?>>
                                    <?= $type['eventType'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="start_date">From:</label>
                        <input type="date" id="start_date" name="start_date" value="<?= isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : '' ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label for="end_date">To:</label>
                        <input type="date" id="end_date" name="end_date" value="<?= isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : '' ?>">
                    </div>
                    
                    <button type="submit" class="filter-btn">Apply Filters</button>
                    <a href="index.php" class="reset-btn">Reset</a>
                </div>
            </form>
        </div>
        
        <div class="featured-events">
            <h2>Events (<?= count($editions) ?>)</h2>
            <?php if (empty($editions)): ?>
                <div class="no-results">
                    <p>No events found matching your criteria. Try different search terms or filters.</p>
                </div>
            <?php else: ?>
                <div class="events-grid">
                    <?php foreach($editions as $edition): ?>
                        <div class="event-card">
                            <div class="event-image">
                                <img src="<?=$edition['image']?>" alt="<?= htmlspecialchars($edition['eventTitle']) ?>">
                            </div>
                            <div class="event-details">
                                <h3><?= htmlspecialchars($edition['eventTitle']) ?></h3>
                                <div class="event-meta">
                                    <p class="event-date"><?= date('F j, Y', strtotime($edition['dateEvent'])) ?> at <?= htmlspecialchars($edition['timeEvent']) ?></p>
                                    <p class="event-salle">Salle <?= htmlspecialchars($edition['NumSalle']) ?></p>
                                    <p class="event-category"><?= htmlspecialchars($edition['eventType']) ?></p>
                                </div>

                                <?php 
                                    $availableSeats = checkAvailableSeats($edition['editionId']);
                                    $btnMessage = '';
                                    
                                    if ($availableSeats === 'No places available') {
                                        $btnMessage = 'Tickets Expired';
                                    } else {
                                        
                                        $btnMessage = 'Buy Ticket';
                                    }
                                ?>

                                <a href="event_details.php?id=<?= $edition['editionId'] ?>" class="view-event"><?= $btnMessage?></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

</body>
</html>