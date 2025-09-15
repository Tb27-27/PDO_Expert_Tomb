
<?php
require "../includes/product-class.php";

$melding = "";
$product = new Product();

if ($_POST) {
    $code = $_POST['code'];
    $omschrijving = $_POST['omschrijving'];
    $foto = $_POST['foto'];
    $prijs = $_POST['prijs'];
    
    // Simpele validatie
    if (empty($code) || empty($omschrijving) || empty($prijs)) {
        $melding = "Vul alle velden in.";
    } elseif ($product->codeBestaatAl($code)) {
        $melding = "Deze code bestaat al.";
    } elseif ($product->voegProductToe($code, $omschrijving, $foto, $prijs)) {
        header("Location: products.php");
        exit;
    } else {
        $melding = "Fout bij toevoegen product.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Product Toevoegen</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-groep { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea { width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        textarea { height: 60px; resize: vertical; }
        .knop { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .knop:hover { background: #005a8a; }
        .terug { background: #6c757d; text-decoration: none; display: inline-block; margin-left: 10px; }
        .fout { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <h1>Nieuw Product Toevoegen</h1>
    
    <?php if ($melding): ?>
        <div class="fout"><?php echo $melding; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-groep">
            <label>Code:</label>
            <input type="text" name="code" value="<?php echo $_POST['code'] ?? ''; ?>" required>
        </div>
        
        <div class="form-groep">
            <label>Omschrijving:</label>
            <textarea name="omschrijving" required><?php echo $_POST['omschrijving'] ?? ''; ?></textarea>
        </div>
        
        <div class="form-groep">
            <label>Foto (optioneel):</label>
            <input type="text" name="foto" value="<?php echo $_POST['foto'] ?? ''; ?>" placeholder="bijv: images/product.jpg">
        </div>
        
        <div class="form-groep">
            <label>Prijs per stuk (€):</label>
            <input type="number" name="prijs" step="0.01" min="0" value="<?php echo $_POST['prijs'] ?? ''; ?>" required>
        </div>
        
        <button type="submit" class="knop">Product Toevoegen</button>
        <a href="products.php" class="knop terug">Terug naar overzicht</a>
    </form>
</body>
</html>