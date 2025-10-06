<?php
    session_start();
    require_once "../includes/product-class.php";

    // Check if user is logged in
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        header("Location: ./login-user.php");
        exit();
    }

    // Get all products from database
    $product = new Product();
    $product_info = $product->haalProductOpMetId($_GET['id']);



    // FIXME: dit is de code van insert product verander dit naar een edit product
    // Variables for form and messages
    $code = '';
    $omschrijving = '';
    $foto = '';
    $prijs = '';
    $errors = [];
    $success = false;

    // Create product object
    $product = new Product();

    try {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get and clean form data
            $code = trim($_POST['code'] ?? '');
            $omschrijving = trim($_POST['omschrijving'] ?? '');
            $prijs = $_POST['prijs'] ?? '';
            
            // Handle file upload
            $foto = ''; // Default empty
            $uploadOk = 1;
            
            // Check if a file was uploaded
            if (isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == 0) {
                $target_dir = "../uploads/";
                
                // Create uploads directory if it doesn't exist
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                
                // Check if image file is actual image
                $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                if($check !== false) {
                    $uploadOk = 1;
                } else {
                    $errors[] = "Bestand is geen geldige afbeelding";
                    $uploadOk = 0;
                }
                
                // Check if file already exists
                if (file_exists($target_file)) {
                    $errors[] = "Dit bestand bestaat al";
                    $uploadOk = 0;
                }
                
                // Check file size (500KB max)
                if ($_FILES["fileToUpload"]["size"] > 50000000) {
                    $errors[] = "Bestand is te groot (max 500MB)";
                    $uploadOk = 0;
                }
                
                // Allow certain file formats
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                    $errors[] = "Alleen JPG, JPEG, PNG & GIF bestanden toegestaan";
                    $uploadOk = 0;
                }
                
                // Try to upload file if everything is ok
                if ($uploadOk == 1) {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                        $foto = "uploads/" . basename($_FILES["fileToUpload"]["name"]);
                    } else {
                        $errors[] = "Fout bij uploaden bestand";
                    }
                }
            }

            // Validate required fields
            if (empty($code)) {
                $errors[] = "Product code is verplicht";
            }
            if (empty($omschrijving)) {
                $errors[] = "Omschrijving is verplicht";
            }
            if (empty($prijs)) {
                $errors[] = "Prijs is verplicht";
            }

            // Validate price format
            if (!empty($prijs) && (!is_numeric($prijs) || $prijs < 0)) {
                $errors[] = "Prijs moet een geldig getal zijn groter dan 0";
            }

            // Check if code already exists
            if (!empty($code) && $product->codeBestaatAl($code)) {
                $errors[] = "Deze product code bestaat al";
            }

            // If no errors, try to add the product
            if (empty($errors)) {
                if ($product->voegProductToe($code, $omschrijving, $foto, $prijs)) {
                    $success = true;
                    // Clear form data on success
                    $code = '';
                    $omschrijving = '';
                    $foto = '';
                    $prijs = '';
                } else {
                    $errors[] = "Fout bij toevoegen van product. Probeer opnieuw.";
                }
            }
        }
    } catch (Exception $e) {
        $errors[] = "Er is een fout opgetreden: " . $e->getMessage();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../css/stylesheet.css">
</head>
<body>
    <!-- Rain -->
    <div class="rain"></div>
    <div class="wisps"></div>
    
    <div class='user_container single_product_container'>
        <div class='login-icon'>📋</div>
        <h1>Product Overzicht</h1>
        <p class='user_h2 subtitle-text'>
            Welkom, <?php echo htmlspecialchars($_SESSION['username']); ?>!
        </p>
        
        <?php if(empty($product_info)): ?>
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <h3>Geen product gevonden</h3>
            </div>
            
            <div class='action-buttons'>
                <a href="../product/view-product.php" class="user_button">← Terug naar Producten Bekijken</a>
                <a href="../user/dashboard-user.php" class="secondary-button">← Terug naar dashboard</a>
            </div>
            
        <?php else: ?>
            
            <div class="table-container">
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
                                <span class="product-code"><?php echo htmlspecialchars($product_info['code']); ?></span>
                            </td>
                            <td data-label="Omschrijving" class="description-cell">
                                <?php echo htmlspecialchars(($product_info['omschrijving'])); ?>
                            </td>
                            <td data-label="Foto" class="image-cell">
                                <?php if (!empty($product_info['foto'])): ?>
                                    <img src="../<?php echo htmlspecialchars($product_info['foto']); ?>" 
                                         alt="Product foto" 
                                         class="product-image"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                    <span class="no-image" style="display: none;">Geen foto</span>
                                <?php else: ?>
                                    <span class="no-image">Geen foto</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Prijs" class="price-cell">
                                <span class="price-tag">€<?php echo number_format($product_info['prijsPerStuk'], 2, ',', '.'); ?></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class='divider'></div>
            
            <!-- FIXME EDIT SECTION -->
            
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

            <div class='divider'></div>

            <div class='action-buttons'>
                <a href="../product/view-product.php" class="user_button">← Terug naar Producten Bekijken</a>
                <a href="../user/dashboard-user.php" class="secondary-button">← Terug naar dashboard</a>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="../javascript/script.js"></script>
</body>
</html>