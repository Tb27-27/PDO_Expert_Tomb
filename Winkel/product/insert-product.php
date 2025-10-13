<?php
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

// Variabelen voor formulier en berichten
$code = '';
$omschrijving = '';
$foto = '';
$prijs = '';
$errors = [];
$success = false;

// Maak een product object aan
$product = new Product();

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Controleer eerst of het CSRF token geldig is
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $errors[] = "Ongeldige beveiligingstoken. Vernieuw de pagina en probeer opnieuw.";
        } else {
            // Haal de formuliergegevens op en verwijder spaties aan begin/eind
            $code = trim($_POST['code'] ?? '');
            $omschrijving = trim($_POST['omschrijving'] ?? '');
            $prijs = $_POST['prijs'] ?? '';
            
            // Variabelen voor het uploaden van bestanden
            $foto = '';
            $uploadCheck = 1;
            
            // Controleer of er een bestand is geüpload
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
                
                // Controleer of het bestand al bestaat
                if (file_exists($target_file)) {
                    $errors[] = "Dit bestand bestaat al";
                    $uploadCheck = 0;
                }
                
                // Controleer bestandsgrootte (maximaal 50MB)
                if ($_FILES["fileToUpload"]["size"] > 50000000) {
                    $errors[] = "Bestand is te groot (max 500MB)";
                    $uploadCheck = 0;
                }
                
                // Sta alleen bepaalde bestandsformaten toe
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                    $errors[] = "Alleen JPG, JPEG, PNG & GIF bestanden toegestaan";
                    $uploadCheck = 0;
                }
                
                // Probeer het bestand te uploaden als alles in orde is
                if ($uploadCheck == 1) {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                        $foto = "uploads/" . basename($_FILES["fileToUpload"]["name"]);
                    } else {
                        $errors[] = "Fout bij uploaden bestand";
                    }
                }
            }

            // Controleer of alle verplichte velden zijn ingevuld
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

            // Controleer of de product code al bestaat in de database
            if (!empty($code) && $product->codeBestaatAl($code)) {
                $errors[] = "Deze product code bestaat al";
            }

            // Als er geen fouten zijn, probeer het product toe te voegen
            if (empty($errors)) {
                if ($product->voegProductToe($code, $omschrijving, $foto, $prijs)) {
                    $success = true;
                    // Maak de formuliervelden leeg na succesvolle toevoeging
                    $code = '';
                    $omschrijving = '';
                    $foto = '';
                    $prijs = '';
                } else {
                    $errors[] = "Fout bij toevoegen van product. Probeer opnieuw.";
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
    <title>Product Toevoegen</title>
    <link rel="stylesheet" href="../css/stylesheet.css">
</head>
<body>
    <!-- Rain -->
    <div class="rain"></div>
    <div class="wisps"></div>

    <div class='user_container insert-product-container'>        
        <?php if ($success): ?>
            <div class='login-icon'>✅</div>
            <h1>Product Toegevoegd!</h1>
            
            <p class='user_h2 success-login-message'>
                ✓ Het product is succesvol opgeslagen
            </p>
            
            <div class='progress-bar'>
                <div class='progress-fill'></div>
            </div>
            
            <div class='action-buttons'>
                <a href='./view-product.php' class='user_button'>Naar product overzicht</a>
                <a href='./insert-product.php' class='secondary-button'>Nog een product toevoegen</a>
            </div>
            
            <script>
                setTimeout(function() {
                    window.location.href = './view-product.php';
                }, 3000);
            </script>
            
        <?php else: ?>
            <div class='login-icon'>📦</div>
            <h1>Nieuw Product</h1>
            <p class='user_h2 subtitle-text'>
                Vul de gegevens in om een product toe te voegen
            </p>

            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <strong>⚠️ Product toevoegen mislukt</strong><br>
                    <?php foreach ($errors as $error): ?>
                        • <?php echo htmlspecialchars($error); ?><br>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class='user_form'>
                <!-- Verborgen veld met CSRF token voor beveiliging tegen CSRF aanvallen -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                
                <div class="form-group">
                    <input type="text" 
                           name="code" 
                           placeholder="Product Code (bijv: PROD001)" 
                           value="<?php echo htmlspecialchars($code); ?>"
                           required>
                </div>

                <div class="form-group">
                    <textarea name="omschrijving" 
                              class="product-textarea"
                              placeholder="Product Omschrijving"
                              required><?php echo htmlspecialchars($omschrijving); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="file-label">
                        📷 Product Foto
                    </label>
                    <input type="file" 
                           name="fileToUpload" 
                           class="file-input"
                           accept="image/*">
                    <small class="file-hint">
                        Alleen JPG, JPEG, PNG & GIF bestanden. Max 500MB.
                    </small>
                </div>

                <div class="form-group">
                    <input type="number" 
                           name="prijs" 
                           step="0.01" 
                           min="0" 
                           placeholder="Prijs per stuk (€)"
                           value="<?php echo htmlspecialchars($prijs); ?>"
                           required>
                </div>

                <input class='user_button' type="submit" value="Product Toevoegen">
            </form>

            <div class='divider'></div>

            <div class='action-buttons'>
                <a href="./view-product.php" class="secondary-button">📋 Naar product overzicht</a>
                <a href="../user/dashboard-user.php" class="secondary-button">← Terug naar dashboard</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="../javascript/script.js"></script>
</body>
</html>