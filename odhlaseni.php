<?php

if (!isset($_SESSION["login"])) {
    header("Location: index.php");
    exit(); 
}


if (isset($_POST["odhlasit"])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

if (isset($_SESSION["login"])) {
    $uzivatelskeJmeno = $_SESSION["login"];
    echo "<span class='logged-in'>Přihlášený uživatel: $uzivatelskeJmeno</span>";
  }
?>

    <form method="post">
        <input class="odhlasit" type="submit" name="odhlasit" value="Odhlásit se">
    </form>

    