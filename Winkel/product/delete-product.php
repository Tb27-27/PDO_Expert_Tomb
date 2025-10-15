<?php
    // Start de sessie om gebruikersgegevens te kunnen gebruiken
    session_start();
    require_once "../includes/product-class.php";

    // Maak een CSRF token aan als deze nog niet bestaat
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    // Controleer of de gebruiker is ingelogd, anders doorsturen naar login pagina
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        header("Location: ./login-user.php");
        exit();
    }

    // Controleer of er een product ID is meegegeven via de URL
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: ./view-products.php");
        exit();
    }

    // Haal het product op uit de database met het meegegeven ID
    $product = new Product();
    $product_info = $product->haalProductOpMetId($_GET['id']);

    // Als het product niet bestaat, doorsturen naar overzicht
    if (empty($product_info)) {
        header("Location: ./view-products.php");
        exit();
    }

    // Variabelen om methods te laten werken
    $id = $_GET['id'];
    $errors = [];
    $success = false;
        
    // Variabelen om product te laten zien
    $code = $product_info['code'];
    $omschrijving = $product_info['omschrijving'];
    $foto = $product_info['foto'];
    $prijs = $product_info['prijsPerStuk'];

    try {
        // Controleer of het formulier is verzonden
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Controleer of de beveiligingstoken geldig is
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                $errors[] = "Ongeldige beveiligingstoken. Vernieuw de pagina en probeer opnieuw.";
            } 
            else 
            {
                // Als er geen fouten zijn, probeer het product te verwijderen
                if (empty($errors)) {
                    if ($product->verwijderProduct($_GET['id'])) {
                        $success = true;
                    } else {
                        $errors[] = "Fout bij verwijderen van product. Probeer opnieuw.";
                    }
                }
            }
        }
    } catch (Exception $e) {
        $errors[] = "Er is een fout opgetreden: " . $e->getMessage();
    }
        
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Verwijderen</title>
    <link rel="stylesheet" href="../css/stylesheet.css">
</head>
<body>
    <!-- Rain effecten -->
    <div class="rain"></div>
    <div class="wisps"></div>
    
    <div class='user_container single_product_container'>
        
        <?php if ($success): ?>
            <!-- Succesbericht na het verwijderen van het product -->
            <div class='login-icon'>✅</div>
            <h1>Product Verwijderd!</h1>
            
            <p class='user_h2 success-login-message'>
                ✓ Het product is succesvol verwijderd
            </p>
            
            <div class='progress-bar'>
                <div class='progress-fill'></div>
            </div>
            
            <div class='action-buttons'>
                <a href='./view-products.php' class='user_button'>Naar product overzicht</a>
                <a href='./insert-product.php' class='secondary-button'>+ Nieuw Product</a>
            </div>
            
            <script>
                // Automatisch doorsturen naar overzicht na 3 seconden
                setTimeout(function() {
                    window.location.href = './view-products.php';
                }, 3000);
            </script>
            
        <?php else: ?>

            <!-- Verwijder formulier -->
            <div class='login-icon'>🗑️</div>
            <h1>Product Verwijderen</h1>
            <p class='user_h2 subtitle-text'>
                Welkom, <?php echo htmlspecialchars($_SESSION['username']); ?>! Weet je zeker dat je dit product wil verwijderen?
            </p>

            <?php if (!empty($errors)): ?>
                <!-- Toon eventuele foutmeldingen -->
                <div class="error-message">
                    <strong>⚠️ Product verwijderen mislukt</strong><br>
                    <?php foreach ($errors as $error): ?>
                        • <?php echo htmlspecialchars($error); ?><br>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Toon huidige productgegevens in een tabel -->
            <div class="table-container">
                <h3>Product dat verwijderd wordt</h3>
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Omschrijving</th>
                            <th>Foto</th>
                            <th>Prijs</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="Code">
                                <span class="product-code"><?php echo htmlspecialchars($code); ?></span>
                            </td>
                            <td data-label="Omschrijving" class="description-cell">
                                <?php echo htmlspecialchars($omschrijving); ?>
                            </td>
                            <td data-label="Foto" class="image-cell">
                                <?php if (!empty($foto)): ?>
                                    <img src="../<?php echo htmlspecialchars($foto); ?>" 
                                         alt="Product foto" 
                                         class="product-image"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                    <span class="no-image" style="display: none;">Geen foto</span>
                                <?php else: ?>
                                    <span class="no-image">Geen foto</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Prijs" class="price-cell">
                                <span class="price-tag">€<?php echo number_format($prijs, 2, ',', '.'); ?></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Formulier om het product te verwijderen -->
            <form method="POST" action="" class="delete-form">
                <!-- CSRF token voor beveiliging -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="warning-message">
                    <strong>⚠️ Let op!</strong><br>
                    Deze actie kan niet ongedaan worden gemaakt. Het product wordt permanent verwijderd uit de database.
                </div>

                <div class='action-buttons'>
                    <button type="submit" class="user_button delete-button">🗑️ Verwijder Product</button>
                    <a href="./view-products.php" class="secondary-button">← Annuleren</a>
                </div>
            </form>

            <div class='divider'></div>
            
            <div class='action-buttons'>
                <a href="../user/dashboard-user.php" class="secondary-button">← Terug naar dashboard</a>
            </div>

        <?php endif; ?>

    </div>

    <script src="../javascript/script.js"></script>
</body>
</html>