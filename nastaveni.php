<?php

require("db_conect.php");
require("navigace.phtml");

// Konstanty pro nahrávání obrázků
$uploadDirectory = "obrazky/";
$maxFileSize = 5 * 1024 * 1024; // 5 MB

// Zpracování změny hesla
if (isset($_POST["changePassword"])) {
    $noveHeslo = sha1($_POST["changePassword"]);
    $login = $_SESSION["login"];
    
    // Provádění dotazu na aktualizaci hesla
    $updateSql = "UPDATE prihlaseni SET heslo = '$noveHeslo' WHERE login = '$login'";

    if (mysqli_query($dbSpojeni, $updateSql)) {
        header("Location: ".$_SERVER['PHP_SELF']); // Přesměrování na stejnou stránku
        exit();
    } else {
        echo "Chyba při aktualizaci hesla: " . mysqli_error($dbSpojeni);
    }
}

// Zpracování nahrání obrázku
if (isset($_POST["uploadImage"])) {
    $image = $_FILES["image"];
    $imageSize = $image["size"];
    $imageType = $image["type"];
    $imageName = $image["name"];
    $imageTempPath = $image["tmp_name"];

    // Kontrola velikosti souboru
    if ($imageSize > $maxFileSize) {
        $errorMessage = "Velikost souboru překračuje limit 5 MB.";
    }

    // Kontrola typu souboru
    $allowedFormats = ["image/png", "image/jpeg", "image/jpg"];
    if (!in_array($imageType, $allowedFormats)) {
        $errorMessage = "Povolené formáty obrázků jsou pouze PNG a JPEG.";
    }

    // Přesunutí nahrávaného obrázku do složky pro nahrávání
    if (!isset($errorMessage)) {
        // Vytvoření složky pro nahrávání, pokud ještě neexistuje
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        // Vygenerování unikátního názvu souboru pro nahrávaný obrázek
        $fileName = uniqid() . "_" . $imageName;

        // Přesunutí nahrávaného obrázku do složky pro nahrávání
        $filePath = $uploadDirectory . $fileName;
        move_uploaded_file($imageTempPath, $filePath);

        // Uložení cesty k obrázku do session nebo databáze pro uživatele
        $_SESSION["imagePath"] = $filePath;

        header("Location: nastaveni.php");
        exit();

    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Uživatelský profil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h1, h2 {
            color: #333;
            margin-left: 230px;
            margin-top: 100px;
        }

        form {
            margin-bottom: 20px;
            margin-left: 230px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="password"],
        input[type="file"] {
            margin-bottom: 10px;
        }

        .error {
            color: red;
        }

        .profile-image {
            max-width: 300px;
            margin-left: 230px;
        }
    </style>
</head>
<body>
    <h1>Uživatelský profil: <?php echo $_SESSION["login"]; ?></h1>

    <!-- Změna hesla -->
    <h2>Změna hesla</h2>
    <form method="post">
        <label for="password">Nové heslo:</label>
        <input type="password" id="password" name="changePassword" required>
        <br>
        <input type="submit">
    </form>

        <!-- Nahrání obrázku -->
        <h2>Nahrát obrázek</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="image">Nahrát obrázek:</label>
        <input type="file" id="image" name="image" accept="image/png, image/jpeg">
        <br>
        <input type="submit" name="uploadImage" value="Nahrát obrázek">
    </form>

    <?php
    // Zobrazení nahrávaného obrázku, pokud je k dispozici
    if (isset($_SESSION["imagePath"])) {
        $imagePath = $_SESSION["imagePath"];
        echo "<img class='profile-image' src='$imagePath' alt='Nahráný obrázek'>";
    }

    if (isset($errorMessage)) {
        echo "<div class='error'>$errorMessage</div>";
    }
    ?>
</body>
</html>
