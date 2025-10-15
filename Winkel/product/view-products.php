<?php
    session_start();
    require_once "../includes/product-class.php";

    // Check if user is logged in
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        header("Location: ../user/login-user.php");
        exit();
    }

    // Get all products from database
    $product = new Product();
    $producten = $product->haalAlleProductenOp();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Overzicht</title>
    <link rel="stylesheet" href="../css/stylesheet.css">
</head>
<body>
    <!-- Rain -->
    <div class="rain"></div>
    <div class="wisps"></div>
    
    <div class='user_container'>

        <?php
            // Controleer of er een succesbericht in de sessie staat
            if (isset($_SESSION['success_message']) && !empty($_SESSION['success_message'])) {
                $success_msg = $_SESSION['success_message'];
                // Verwijder het bericht uit de sessie zodat het maar 1x wordt getoond
                unset($_SESSION['success_message']);
            ?>
                <!-- Succesbericht met animatie -->
                <div class="success-notification" id="successNotification">
                    <div class="success-icon">✅</div>
                    <div class="success-content">
                        <strong>Gelukt!</strong>
                        <p><?php echo htmlspecialchars($success_msg); ?></p>
                    </div>
                    <button class="close-notification" onclick="closeNotification()">✕</button>
                </div>

                <script>
                    // Sluit het succesbericht na 5 seconden automatisch
                    setTimeout(function() {
                        closeNotification();
                    }, 5000);

                    // Functie om het bericht te sluiten met smooth animatie
                    function closeNotification() {
                        const notification = document.getElementById('successNotification');
                        if (notification) {
                            // Voeg slideOut class toe voor de animatie
                            notification.classList.add('notification-closing');
                            // Verwijder het element na de animatie
                            setTimeout(() => notification.remove(), 400);
                        }
                    }
                </script>
            <?php
            }
        ?>

        <div class='login-icon'>📋</div>
        <h1>Product Overzicht</h1>
        <p class='user_h2 subtitle-text'>
            Welkom, <?php echo htmlspecialchars($_SESSION['username']); ?>!
        </p>
        
        <?php if(empty($producten)): ?>
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <h3>Geen producten gevonden</h3>
                <p>Er zijn nog geen producten toegevoegd aan de database.</p>
                <p>Klik op "Nieuw Product" om je eerste product toe te voegen.</p>
            </div>
            
            <div class='action-buttons'>
                <a href="./insert-product.php" class="user_button">+ Nieuw Product Toevoegen</a>
                <a href="../user/dashboard-user.php" class="secondary-button">← Terug naar dashboard</a>
            </div>
            
        <?php else: ?>
            <div class="product-count">
                <strong>Totaal:</strong> <?php echo count($producten); ?> producten
            </div>
            
            <div class="table-container">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Omschrijving</th>
                            <th>Foto</th>
                            <th>Prijs</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($producten as $p): ?>
                        <tr>
                            <td data-label="Code">
                                <span class="product-code"><?php echo htmlspecialchars($p['code']); ?></span>
                            </td>
                            <td data-label="Omschrijving" class="description-cell">
                                <?php echo htmlspecialchars(substr($p['omschrijving'], 0, 60)) . (strlen($p['omschrijving']) > 60 ? '...' : ''); ?>
                            </td>
                            <td data-label="Foto" class="image-cell">
                                <?php if (!empty($p['foto'])): ?>
                                    <img src="../<?php echo htmlspecialchars($p['foto']); ?>" 
                                         alt="Product foto" 
                                         class="product-image"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                    <span class="no-image" style="display: none;">Geen foto</span>
                                <?php else: ?>
                                    <span class="no-image">Geen foto</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Prijs" class="price-cell">
                                <span class="price-tag">€<?php echo number_format($p['prijsPerStuk'], 2, ',', '.'); ?></span>
                            </td>
                            <td data-label="Acties" class="actions-cell">
                                <!-- FIXME: Opdracht 5 Edit product -->
                                <a href="edit-product.php?id=<?php echo $p['id']; ?>" 
                                   class="action-button edit-button">✏️ Bewerken</a>
                                <!-- FIXME: Opdracht 6 Delete product ? -->
                                <a href="delete-product.php?id=<?php echo $p['id']; ?>" 
                                   class="action-button delete-button" 
                                   onclick="return confirm('Weet je zeker dat je dit product wilt verwijderen?')">🗑️ Verwijderen</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class='divider'></div>
            
            <div class='action-buttons'>
                <a href="./insert-product.php" class="user_button">+ Nieuw Product</a>
                <a href="../user/dashboard-user.php" class="secondary-button">← Terug naar dashboard</a>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="../javascript/script.js"></script>
</body>
</html>