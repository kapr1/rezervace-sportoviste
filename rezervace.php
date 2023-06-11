<?php
require("db_conect.php");
require("navigace.phtml");


if (isset($_POST["submit"])) {
    $jmeno = $_POST["jmeno"];
    $prijmeni = $_POST["prijmeni"];
    $telefon = $_POST["telefon"];
    $poznamky = $_POST["poznamky"];
    $datum = $_POST["datum"];
    $casOd = $_POST["casOd"];
    $casDo = $_POST["casDo"];

    // Získání ID přihlášeného uživatele
    if (isset($_SESSION["login"])) {
        $uzivatelskeJmeno = $_SESSION["login"];

        // Dotaz do databáze pro získání ID uživatele
        $sql = "SELECT ID_uzivatele FROM prihlaseni WHERE login = ?";
        $stmt = mysqli_prepare($dbSpojeni, $sql);
        mysqli_stmt_bind_param($stmt, "s", $uzivatelskeJmeno);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $idUzivatele);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        // Získání ID sportoviště
        if (isset($_GET['id'])) {
            $idSportoviste = $_GET['id'];

            // Kontrola dostupnosti vybraného času
            $sql = "SELECT * FROM rezervace WHERE datum = ? AND ((casOd >= ? AND casOd < ?) OR (casDo > ? AND casDo <= ?)) AND ID_s = ?";
            $stmt = mysqli_prepare($dbSpojeni, $sql);
            mysqli_stmt_bind_param($stmt, "ssssss", $datum, $casOd, $casDo, $casOd, $casDo, $idSportoviste);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {

                $errorMessage2 = "Zadaný čas není k dispozici, prosím vyberte si jiný čas.";
            } else {
            
                $sql = "INSERT INTO rezervace (jmeno, prijmeni, telefon, poznamky, datum, casOd, casDo, ID_uzivatele, ID_s) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($dbSpojeni, $sql);
                mysqli_stmt_bind_param($stmt, "sssssssss", $jmeno, $prijmeni, $telefon, $poznamky, $datum, $casOd, $casDo, $idUzivatele, $idSportoviste);
                mysqli_stmt_execute($stmt);
                header("Location: celkem.php");
                exit();
            }
        } else {
            echo "<div class='error'>Chyba: Nebylo možné získat ID sportoviště.</div>";
        }
    }
}
require("rezervace.phtml");
?>
