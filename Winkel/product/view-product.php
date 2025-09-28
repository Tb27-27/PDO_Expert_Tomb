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
$producten = $product->haalAlleProductenOp();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Overzicht</title>
    <link rel="stylesheet" href="../css/stylesheet.css">
</head>
<body>
    <!-- Rain -->
    <div class="rain"></div>

    <div class='user_container' style="max-width: 800px;">
        <h1>Product Overzicht</h1>
        <h2>Welkom, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>

        <!-- Navigation buttons -->
        <div class="dashboard-nav">
            <a href="./insert-product.php" class="login_button">+ Nieuw Product</a>
            <a href="../user/dashboard-user.php" class="user_button">Dashboard</a>
        </div>

        <?php if(empty($producten)): ?>
            <div class="info-box">
                <h3>Geen producten gevonden</h3>
                <p>Er zijn nog geen producten toegevoegd aan de database.</p>
                <p>Klik op "Nieuw Product" om je eerste product toe te voegen.</p>
            </div>
        <?php else: ?>
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
                        <td><?php echo htmlspecialchars($p['code']); ?></td>
                        <td><?php echo htmlspecialchars(substr($p['omschrijving'], 0, 50)) . (strlen($p['omschrijving']) > 50 ? '...' : ''); ?></td>
                        <td>
                            <?php if (!empty($p['foto'])): ?>
                                <img src="../<?php echo htmlspecialchars($p['foto']); ?>" 
                                     alt="Product foto" 
                                     class="product-image"
                                     onerror="this.style.display='none'">
                            <?php else: ?>
                                <span style="color: rgba(255, 255, 255, 0.5);">Geen foto</span>
                            <?php endif; ?>
                        </td>
                        <td>€<?php echo number_format($p['prijsPerStuk'], 2, ',', '.'); ?></td>
                        <td>
                            <a href="edit-product.php?id=<?php echo $p['id']; ?>" 
                               class="action-button action-edit">Bewerken</a>
                            <a href="delete-product.php?id=<?php echo $p['id']; ?>" 
                               class="action-button action-delete" 
                               onclick="return confirm('Weet je zeker dat je dit product wilt verwijderen?')">Verwijderen</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="info-box">
                <p><strong>Totaal aantal producten:</strong> <?php echo count($producten); ?></p>
            </div>
        <?php endif; ?>

        <div style="margin-top: 30px;">
            <a href="../frontpage.php" class="back-link">Terug naar homepage</a>
        </div>
    </div>

    <script src="../javascript/script.js"></script>
</body>
</html>