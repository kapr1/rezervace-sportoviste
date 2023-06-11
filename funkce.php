<?php

function prihlasUzivatele($dbSpojeni, $data) {
    $login = mysqli_real_escape_string($dbSpojeni, $data["login"]);
    $heslo = sha1(mysqli_real_escape_string($dbSpojeni, $data["heslo"]));
    
    $sql = "SELECT * FROM prihlaseni WHERE login = '$login' AND heslo = '$heslo'";
    $result = mysqli_query($dbSpojeni, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        $_SESSION["prihlaseny"] = true;
        $_SESSION["login"] = $login;
        return true;
    } else {
        return false;
    }
}