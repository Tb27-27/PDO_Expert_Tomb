<?php
require "../includes/product-class.php";

$product = new Product();
$producten = $product->haalAlleProductenOp();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Producten</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f4f4f4; }
        .knop { padding: 5px 10px; margin: 2px; text-decoration: none; color: white; border-radius: 3px; }
        .bewerken { background-color: #007cba; }
        .verwijderen { background-color: #dc3545; }
        .nieuw { background-color: #28a745; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Producten</h1>
    
    <a href="insert-product.php" class="knop nieuw">+ Nieuw Product</a>
    
    <?php if(empty($producten)): ?>
        <p>Geen producten gevonden.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Code</th>
                <th>Omschrijving</th>
                <th>Prijs</th>
                <th>Acties</th>
            </tr>
            <?php foreach($producten as $p): ?>
            <tr>
                <td><?php echo $p['code']; ?></td>
                <td><?php echo $p['omschrijving']; ?></td>
                <td>€<?php echo number_format($p['prijsPerStuk'], 2, ',', '.'); ?></td>
                <td>
                    <a href="edit-product.php?id=<?php echo $p['id']; ?>" class="knop bewerken">Bewerken</a>
                    <a href="delete-product.php?id=<?php echo $p['id']; ?>" class="knop verwijderen" 
                       onclick="return confirm('Zeker weten?')">Verwijderen</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>