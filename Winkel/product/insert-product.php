<?php
session_start();
require_once "../includes/product-class.php";

// Check if user is logged in - protect this page
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: ./login-user.php");
    exit();
}

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
                $errors[] = "File is not an image.";
                $uploadOk = 0;
            }
            
            // Check if file already exists
            if (file_exists($target_file)) {
                $errors[] = "Sorry, file already exists.";
                $uploadOk = 0;
            }
            
            // Check file size (500KB max)
            if ($_FILES["fileToUpload"]["size"] > 500000) {
                $errors[] = "Sorry, your file is too large.";
                $uploadOk = 0;
            }
            
            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                $errors[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }
            
            // Try to upload file if everything is ok
            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    $foto = "uploads/" . basename($_FILES["fileToUpload"]["name"]);
                } else {
                    $errors[] = "Sorry, there was an error uploading your file.";
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
    <title>Product Toevoegen</title>
    <link rel="stylesheet" href="../css/stylesheet.css">
</head>
<body>
    <!-- Rain -->
    <div class="rain"></div>

    <div class='user_container'>
        <h1>Nieuw Product Toevoegen</h1>
        <h2>Welkom, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>

        <?php if ($success): ?>
            <div class="success-message">
                <strong>Product succesvol toegevoegd!</strong><br>
                Het product is opgeslagen in de database.<br>
                Je wordt doorgestuurd naar het overzicht...
                <script>
                    setTimeout(function() {
                        window.location.href = './view-product.php';
                    }, 3000);
                </script>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <strong>Product toevoegen mislukt:</strong><br>
                <?php foreach ($errors as $error): ?>
                    • <?php echo htmlspecialchars($error); ?><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
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
                              placeholder="Product Omschrijving"
                              required
                              style="min-height: 80px; resize: vertical; font-family: inherit;"><?php echo htmlspecialchars($omschrijving); ?></textarea>
                </div>

                <div class="form-group">
                    <label style="color: white; font-size: 14px; margin-bottom: 5px; display: block;">
                        Product Foto (optioneel):
                    </label>
                    <input type="file" 
                           name="fileToUpload" 
                           accept="image/*"
                           style="background: rgba(255, 255, 255, 0.1); color: white; border: 1px solid rgba(255, 255, 255, 0.3);">
                    <small style="color: rgba(255, 255, 255, 0.7); font-size: 12px; display: block; margin-top: 5px;">
                        Alleen JPG, JPEG, PNG & GIF bestanden. Max 500KB.
                    </small>
                </div>

                <div class="form-group">
                    <input type="number" 
                           name="prijs" 
                           step="0.01" 
                           min="0" 
                           placeholder="Prijs per stuk (euro)"
                           value="<?php echo htmlspecialchars($prijs); ?>"
                           required>
                </div>

                <input class='user_button' type="submit" value="Product Toevoegen">
            </form>

            <div style="margin-top: 20px;">
                <a href="./view-product.php" class="back-link">Naar product overzicht</a>
                <br>
                <a href="../user/dashboard-user.php" class="back-link">Terug naar dashboard</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="../javascript/script.js"></script>
</body>
</html>