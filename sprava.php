<style>
    /* CSS styles for the table */
    table {
        border-collapse: collapse;
        width: 85%;
        margin-left: 230px;
        margin-top: 100px;
    }

    th, td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    label {
        display: block;
        margin-bottom: 5px;
    }

    input[type="text"] {
        width: 200px;
        margin-bottom: 10px;
    }

    .mazani_upravy {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        cursor: pointer;
        margin-left: 5px;
    }

    .mazani_upravy:hover {
        background-color: #45a049;
    }

    h2 {
        margin-top: 100px;
        margin-left: 230px;
    }
    .sport{
        margin-left: 230px;
    }
    .nic{
        margin-left: 230px;
    }
</style>

<?php
require("db_conect.php");
require("navigace.phtml");

// Zpracování akce pro úpravu uživatele
if (isset($_POST["edit"])) {
    $uzivatelskeJmeno = $_POST["uzivatelske_jmeno"];
    $noveJmeno = $_POST["nove_jmeno"];
    $noveHeslo = sha1($_POST["nove_heslo"]);

    // Provádění dotazu na aktualizaci jména a hesla
    $updateSql = "UPDATE prihlaseni SET ";
    if (!empty($noveJmeno)) {
        $updateSql .= "login = '$noveJmeno'";
    }
    if (!empty($noveHeslo)) {
        if (!empty($noveJmeno)) {
            $updateSql .= ", ";
        }
        $updateSql .= "heslo = '$noveHeslo'";
    }
    $updateSql .= " WHERE login = '$uzivatelskeJmeno'";

    if (mysqli_query($dbSpojeni, $updateSql)) {
        header("Location: ".$_SERVER['PHP_SELF']); // Přesměrování na stejnou stránku
        exit();
    } else {
        echo "Chyba při aktualizaci dat uživatele: " . mysqli_error($dbSpojeni);
    }
}

// Zpracování akce pro smazání uživatele
if (isset($_POST["delete_user"])) {
    $uzivatelskeJmeno = $_POST["uzivatelske_jmeno"];

    // Provádění dotazu na smazání uživatele
    $deleteSql = "DELETE FROM prihlaseni WHERE login = '$uzivatelskeJmeno'";
    if (mysqli_query($dbSpojeni, $deleteSql)) {
        header("Location: ".$_SERVER['PHP_SELF']); // Přesměrování na stejnou stránku
        exit();
    } else {
        echo "Chyba při mazání uživatele: " . mysqli_error($dbSpojeni);
    }
}

// Získání uživatelů z databáze
$sql = "SELECT login, heslo FROM prihlaseni";
$result = mysqli_query($dbSpojeni, $sql);
?>
    <h2>Uživatelé</h2>
<?php
if (mysqli_num_rows($result) > 0) {
    echo "<table>";
    echo "<tr><th>Login</th><th>Akce</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>".$row["login"]."</td>";
        echo "<td>";
        echo "<form method='post'>";
        echo "<input type='hidden' name='uzivatelske_jmeno' value='".$row["login"]."'>";
        echo "<input type='text' name='nove_jmeno' placeholder='Nové jméno'>";
        echo "<input type='text' name='nove_heslo' placeholder='Nové heslo'>";
        echo "<input class='mazani_upravy' type='submit' name='edit' value='Upravit'>";
        echo "<input class='mazani_upravy' type='submit' name='delete_user' value='Smazat'>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='nic'>Žádní uživatelé k zobrazení.</p>";
}

// Zpracování akce pro smazání rezervace
if (isset($_POST["delete_reservation"])) {
    $jmeno = $_POST["jmeno"];

    // Získání ID rezervace
    $selectSql = "SELECT ID_R FROM rezervace WHERE jmeno = '$jmeno' LIMIT 1";
    $result = mysqli_query($dbSpojeni, $selectSql);
    $row = mysqli_fetch_assoc($result);
    $id = $row["ID_R"];

    if ($id) {
        // Provádění dotazu na smazání záznamu rezervace s určeným ID
        $deleteSql = "DELETE FROM rezervace WHERE ID_R  = $id";
        if (mysqli_query($dbSpojeni, $deleteSql)) {
            header("Location: ".$_SERVER['PHP_SELF']); // Přesměrování na stejnou stránku
            exit();
        } else {
            echo "Chyba při mazání záznamu: " . mysqli_error($dbSpojeni);
        }
    } 
}


$sql = "SELECT jmeno, prijmeni, telefon, poznamky, datum, casOd, casDo, nazev, mesto, typ_sportoviste 
        FROM rezervace
        JOIN sportoviste ON rezervace.id_s = sportoviste.id_s";
$result = mysqli_query($dbSpojeni, $sql);
?>
    <h2>Seznam rezervací</h2>
<?php
if (mysqli_num_rows($result) > 0) {
    echo "<table>";
    echo "<tr><th>Jméno</th><th>Příjmení</th><th>Telefon</th><th>Poznámky</th><th>Datum</th><th>Čas od</th><th>Čas do</th><th>nazev</th><th>mesto</th><th>typ_sportoviste</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>".$row["jmeno"]."</td>";
        echo "<td>".$row["prijmeni"]."</td>";
        echo "<td>".$row["telefon"]."</td>";
        echo "<td>".$row["poznamky"]."</td>";
        echo "<td>".$row["datum"]."</td>";
        echo "<td>".$row["casOd"]."</td>";
        echo "<td>".$row["casDo"]."</td>";
        echo "<td>".$row["nazev"]."</td>";
        echo "<td>".$row["mesto"]."</td>";
        echo "<td>".$row["typ_sportoviste"]."</td>";
        echo "<td>";
        echo "<form method='post'>";
        echo "<input type='hidden' name='jmeno' value='".$row["jmeno"]."'>";
        echo "<input class='mazani_upravy' type='submit' name='delete_reservation' value='Smazat'>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='nic'>Žádné rezervace k zobrazení.</p>";
}

// Zpracování akce pro přidání sportoviště
if (isset($_POST["add"])) {
    $nazev = $_POST["nazev"];
    $mesto = $_POST["mesto"];
    $typSportoviste = $_POST["typ_sportoviste"];

    // Provádění dotazu na přidání sportoviště
    $insertSql = "INSERT INTO sportoviste (nazev, mesto, typ_sportoviste) VALUES ('$nazev', '$mesto', '$typSportoviste')";
    if (mysqli_query($dbSpojeni, $insertSql)) {
        header("Location: ".$_SERVER['PHP_SELF']); // Přesměrování na stejnou stránku
        exit();
    } else {
        echo "Chyba při přidávání sportoviště: " . mysqli_error($dbSpojeni);
    }
}

// Zpracování akce pro smazání sportoviště
if (isset($_POST["delete"])) {
    $nazev = $_POST["nazev"];

    // Provádění dotazu na smazání sportoviště
    $deleteSql = "DELETE FROM sportoviste WHERE nazev = '$nazev'";
    if (mysqli_query($dbSpojeni, $deleteSql)) {
        header("Location: ".$_SERVER['PHP_SELF']); // Přesměrování na stejnou stránku
        exit();
    } else {
        echo "Chyba při mazání sportoviště: " . mysqli_error($dbSpojeni);
    }
}

// Získání sportovišť z databáze
$sportovisteSql = "SELECT nazev, mesto, typ_sportoviste FROM sportoviste";
$sportovisteResult = mysqli_query($dbSpojeni, $sportovisteSql);
?>
    <h2>Seznam sportovišť</h2>
<?php
if (mysqli_num_rows($sportovisteResult) > 0) {
    echo "<table>";
    echo "<tr><th>Název</th><th>Město</th><th>Typ sportoviště</th><th>Akce</th></tr>";
    while ($sportovisteRow = mysqli_fetch_assoc($sportovisteResult)) {
        echo "<tr>";
        echo "<td>".$sportovisteRow["nazev"]."</td>";
        echo "<td>".$sportovisteRow["mesto"]."</td>";
        echo "<td>".$sportovisteRow["typ_sportoviste"]."</td>";
        echo "<td>";
        echo "<form method='post'>";
        echo "<input type='hidden' name='nazev' value='".$sportovisteRow["nazev"]."'>";
        echo "<input class='mazani_upravy' type='submit' name='delete' value='Smazat'>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='nic'>Žádná sportoviště k zobrazení.</p>";
}

mysqli_close($dbSpojeni);
?>

<h2>Přidání nového sportoviště</h2>
<form class="sport" method="post">
    <label for="nazev">Název:</label>
    <input type="text" name="nazev" id="nazev" required>
    <label for="mesto">Město:</label>
    <input type="text" name="mesto" id="mesto" required>
    <label for="typ_sportoviste">Typ sportoviště:</label>
    <input type="text" name="typ_sportoviste" id="typ_sportoviste" required>
    <input type="submit" name="add" value="Přidat sportoviště">
</form>