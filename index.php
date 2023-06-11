<?php
session_start();
require("db_conect.php");
require("funkce.php");
require("prihlaseni.phtml");

if (isset($_POST["login"])) {
    if (prihlasUzivatele($dbSpojeni, $_POST)) {
        
        // Check if the user is an admin
        $sql = "SELECT * FROM prihlaseni WHERE login = ? AND admin = 1";
        $stmt = mysqli_prepare($dbSpojeni, $sql);
        mysqli_stmt_bind_param($stmt, "s", $_POST["login"]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $_SESSION["admin"] = true;
            header("Location: sprava.php");
            exit(); // Terminate the current script
        } else {
            $_SESSION["admin"] = false;
            header("Location: sportoviste.php");
            exit(); // Terminate the current script
        }
    }
}
?>
