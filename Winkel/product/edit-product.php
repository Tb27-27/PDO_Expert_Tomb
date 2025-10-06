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
    
    <form action="" method="POST">

    </form>


    <script src="../javascript/script.js"></script>
</body>
</html>