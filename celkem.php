<style>
 
.rezervace-title {
  font-size: 24px;
  margin-top: 100px;
  margin-left: 150px;
}

.rezervace-table {
  width: 90%;
  border-collapse: collapse;
  margin-top: 10px;
  margin-left: 150px;
}

.rezervace-table th,
.rezervace-table td {
  border: 1px solid #ccc;
  padding: 8px;
}

.rezervace-table th {
  background-color: #f2f2f2;
}

.no-rezervace {
  margin-top: 10px;
  font-style: italic;
}

.smazat-button {
  background-color: #ff0000;
  color: #fff;
  border: none;
  padding: 5px 10px;
  cursor: pointer;
}

.smazat-button:hover {
  background-color: #cc0000;
}

</style>
<?php
require("db_conect.php");
require("navigace.phtml");  

function smazatRezervaci($idRezervace, $dbSpojeni)
{
    // Připravený dotaz pro smazání rezervace s daným ID
    $sql = "DELETE FROM rezervace WHERE ID_R = ?";
    $stmt = mysqli_prepare($dbSpojeni, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idRezervace);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    // Přesměrování na stránku s rezervacemi nebo jinou požadovanou stránku
    header("Location: celkem.php");
    exit();
}

if (isset($_SESSION["login"])) {
    $uzivatelskeJmeno = $_SESSION["login"];

    // Získání rezervací daného uživatele z databáze s názvem sportoviště, městem, typem sportoviště, jménem a příjmením
    $sql = "SELECT rezervace.*, sportoviste.nazev AS nazev_sportoviste, sportoviste.mesto, sportoviste.typ_sportoviste, rezervace.jmeno, rezervace.prijmeni FROM rezervace INNER JOIN sportoviste ON rezervace.ID_s = sportoviste.ID_s INNER JOIN prihlaseni ON rezervace.ID_uzivatele = prihlaseni.ID_uzivatele WHERE prihlaseni.login = ?";
    $stmt = mysqli_prepare($dbSpojeni, $sql);
    mysqli_stmt_bind_param($stmt, "s", $uzivatelskeJmeno);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "<h2 class='rezervace-title'>Rezervace uživatele: " . $uzivatelskeJmeno . "</h2>";
        echo "<table class='rezervace-table'>";
        echo "<tr><th>Jméno</th><th>Příjmení</th><th>Sportoviště</th><th>Město</th><th>Typ sportoviště</th><th>Datum</th><th>Čas od</th><th>Čas do</th><th>Akce</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row["jmeno"] . "</td>";
            echo "<td>" . $row["prijmeni"] . "</td>";
            echo "<td>" . $row["nazev_sportoviste"] . "</td>";
            echo "<td>" . $row["mesto"] . "</td>";
            echo "<td>" . $row["typ_sportoviste"] . "</td>";
            echo "<td>" . $row["datum"] . "</td>";
            echo "<td>" . $row["casOd"] . "</td>";
            echo "<td>" . $row["casDo"] . "</td>";
            echo "<td><form action='' method='post'><input type='hidden' name='idRezervace' value='" . $row["ID_R"] . "'><input type='submit' name='smazat' value='Smazat' class='smazat-button'></form></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='no-rezervace'>Žádné rezervace pro uživatele: " . $uzivatelskeJmeno . "</p>";
    }

    mysqli_stmt_close($stmt);

    // Zpracování požadavku na smazání rezervace
    if (isset($_POST["smazat"])) {
        $idRezervace = $_POST["idRezervace"];
        smazatRezervaci($idRezervace, $dbSpojeni);
    }
} else {
    echo "Uživatel není přihlášen.";
}

mysqli_close($dbSpojeni);
?>
