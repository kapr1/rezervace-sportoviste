<?php
require("db_conect.php");

$errorMessage = "";

if (isset($_POST["login"])) {
    // check if username already exists
    $existingUserSql = "SELECT * FROM prihlaseni WHERE login = ?";
    $existingUserStmt = mysqli_prepare($dbSpojeni, $existingUserSql);
    mysqli_stmt_bind_param($existingUserStmt, "s", $_POST["login"]);
    mysqli_stmt_execute($existingUserStmt);
    $existingUserResult = mysqli_stmt_get_result($existingUserStmt);
    
    if (mysqli_num_rows($existingUserResult) > 0) {
        $errorMessage = "Uživatelské jméno již existuje.";
    } else {
        // check if passwords match
        if ($_POST["heslo"] == $_POST["znovu_heslo"]) {
            $login = $_POST["login"];
            $password = sha1($_POST["heslo"]);

            $insertUserSql = "INSERT INTO prihlaseni (login, heslo) VALUES (?, ?)";
            $insertUserStmt = mysqli_prepare($dbSpojeni, $insertUserSql);
            mysqli_stmt_bind_param($insertUserStmt, "ss", $_POST["login"], $password);
            mysqli_stmt_execute($insertUserStmt);

            // set session variables for logged in user
            session_start();
            $_SESSION["ID_uzivatele"] = mysqli_insert_id($dbSpojeni);
            $_SESSION["login"] = $login;
            $_SESSION["id_uzivatele"] = $idUzivatele;

            // redirect to success page
            header("Location: sportoviste.php");
            exit;
        } else {
            $errorMessage = "Hesla se neshodují.";
        }
    }
}

$titulek = "Registrace";
require("top_html.phtml");
require("registrace.phtml");
?>