<?php
    // Start de sessie om gebruikersgegevens te kunnen gebruiken
    session_start();
    require_once "../includes/product-class.php";

    // Controleer of de gebruiker is ingelogd, anders doorsturen naar login pagina
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        header("Location: ./login-user.php");
        exit();
    }

    // Controleer of er een product ID is meegegeven via de URL
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: ./view-product.php");
        exit();
    }

    // Haal het product op uit de database met het meegegeven ID
    $product = new Product();
    $product_info = $product->haalProductOpMetId($_GET['id']);

    // Als het product niet bestaat, doorsturen naar overzicht
    if (empty($product_info)) {
        header("Location: ./view-product.php");
        exit();
    }

    // Variabelen voor formulier en berichten
    $code = $product_info['code'];
    $omschrijving = $product_info['omschrijving'];
    $foto = $product_info['foto'];
    $prijs = $product_info['prijsPerStuk'];
    $errors = [];
    $success = false;

    try {
        // Controleer of het formulier is verzonden
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Haal de formuliergegevens op en verwijder spaties aan begin/eind
            $code = trim($_POST['code'] ?? '');
            $omschrijving = trim($_POST['omschrijving'] ?? '');
            $prijs = $_POST['prijs'] ?? '';
            
            // Behoud de oude foto als er geen nieuwe wordt geüpload
            $nieuwe_foto = $foto;
            $uploadCheck = 1;
            
            // Controleer of er een nieuw bestand is geüpload
            if (isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == 0) {
                $target_dir = "../uploads/";
                
                // Maak de uploads map aan als deze nog niet bestaat
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                
                // Controleer of het bestand een echte afbeelding is
                $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                if($check !== false) {
                    $uploadCheck = 1;
                } else {
                    $errors[] = "Bestand is geen geldige afbeelding";
                    $uploadCheck = 0;
                }
                
                // Controleer bestandsgrootte (500MB max)
                if ($_FILES["fileToUpload"]["size"] > 50000000) {
                    $errors[] = "Bestand is te groot (max 500MB)";
                    $uploadCheck = 0;
                }
                
                // Sta alleen bepaalde bestandsformaten toe
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                    $errors[] = "Alleen JPG, JPEG, PNG & GIF bestanden toegestaan";
                    $uploadCheck = 0;
                }
                
                // Probeer het bestand te uploaden als alles oké is
                if ($uploadCheck == 1) {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                        // Verwijder de oude foto als er een nieuwe is geüpload
                        if (!empty($foto) && file_exists("../" . $foto)) {
                            // Voor het verwijderen van fotos dit uncommenten:
                            // unlink("../" . $foto);
                        }
                        $nieuwe_foto = "uploads/" . basename($_FILES["fileToUpload"]["name"]);
                    } else {
                        $errors[] = "Fout bij uploaden bestand";
                    }
                }
            }

            // Valideer verplichte velden
            if (empty($code)) {
                $errors[] = "Product code is verplicht";
            }
            if (empty($omschrijving)) {
                $errors[] = "Omschrijving is verplicht";
            }
            if (empty($prijs)) {
                $errors[] = "Prijs is verplicht";
            }

            // Controleer of de prijs een geldig nummer is
            if (!empty($prijs) && (!is_numeric($prijs) || $prijs < 0)) {
                $errors[] = "Prijs moet een geldig getal zijn groter dan 0";
            }

            // Controleer of de code al bestaat bij een ander product
            if (!empty($code) && $code != $product_info['code']) {
                if ($product->codeBestaatAl($code)) {
                    $errors[] = "Deze product code bestaat al";
                }
            }

            // Als er geen fouten zijn, probeer het product bij te werken
            if (empty($errors)) {
                if ($product->bewerkProduct($_GET['id'], $code, $omschrijving, $nieuwe_foto, $prijs)) {
                    $success = true;
                    // Update de lokale variabelen met de nieuwe waarden
                    $foto = $nieuwe_foto;
                } else {
                    $errors[] = "Fout bij bijwerken van product. Probeer opnieuw.";
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
    <title>Product Bewerken</title>
    <link rel="stylesheet" href="../css/stylesheet.css">
</head>
<body>
    <!-- Rain effecten -->
    <div class="rain"></div>
    <div class="wisps"></div>
    
    <div class='user_container single_product_container'>
        
        <?php if ($success): ?>
            <!-- Succesbericht na het bijwerken van product -->
            <div class='login-icon'>✅</div>
            <h1>Product Bijgewerkt!</h1>
            
            <p class='user_h2 success-login-message'>
                ✓ Het product is succesvol bijgewerkt
            </p>
            
            <div class='progress-bar'>
                <div class='progress-fill'></div>
            </div>
            
            <div class='action-buttons'>
                <a href='./view-product.php' class='user_button'>Naar product overzicht</a>
                <a href='./edit-product.php?id=<?php echo $_GET['id']; ?>' class='secondary-button'>Product opnieuw bewerken</a>
            </div>
            
            <script>
                // Automatisch doorsturen naar overzicht na 3 seconden
                setTimeout(function() {
                    window.location.href = './view-product.php';
                }, 3000);
            </script>
            
        <?php else: ?>
            <!-- Bewerkformulier -->
            <div class='login-icon'>✏️</div>
            <h1>Product Bewerken</h1>
            <p class='user_h2 subtitle-text'>
                Welkom, <?php echo htmlspecialchars($_SESSION['username']); ?>! Pas de productgegevens aan.
            </p>

            <?php if (!empty($errors)): ?>
                <!-- Toon eventuele foutmeldingen -->
                <div class="error-message">
                    <strong>⚠️ Product bijwerken mislukt</strong><br>
                    <?php foreach ($errors as $error): ?>
                        • <?php echo htmlspecialchars($error); ?><br>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Toon huidige productgegevens in een tabel -->
            <div class="table-container">
                <h3>Huidige Productgegevens</h3>
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

            <div class='divider'></div>

            <!-- Bewerkformulier met vooraf ingevulde waarden -->
            <form method="POST" enctype="multipart/form-data" class='user_form'>
                <div class="form-group">
                    <label>Product Code</label>
                    <input type="text" 
                        name="code" 
                        placeholder="Product Code (bijv: PROD001)" 
                        value="<?php echo htmlspecialchars($code); ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>Omschrijving</label>
                    <textarea name="omschrijving" 
                            class="product-textarea"
                            placeholder="Product Omschrijving"
                            required><?php echo htmlspecialchars($omschrijving); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="file-label">
                        📷 Nieuwe Product Foto (optioneel)
                    </label>
                    <input type="file" 
                        name="fileToUpload" 
                        class="file-input"
                        accept="image/*">
                    <small class="file-hint">
                        Laat leeg om de huidige foto te behouden. Alleen JPG, JPEG, PNG & GIF. Max 500MB.
                    </small>
                </div>

                <div class="form-group">
                    <label>Prijs per Stuk (€)</label>
                    <input type="number" 
                        name="prijs" 
                        step="0.01" 
                        min="0" 
                        placeholder="Prijs per stuk (€)"
                        value="<?php echo htmlspecialchars($prijs); ?>"
                        required>
                </div>

                <input class='user_button' type="submit" value="Product Bijwerken">
            </form>

            <div class='divider'></div>

            <div class='action-buttons'>
                <a href="./view-product.php" class="secondary-button">📋 Terug naar product overzicht</a>
                <a href="../user/dashboard-user.php" class="secondary-button">← Terug naar dashboard</a>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="../javascript/script.js"></script>
</body>
</html>