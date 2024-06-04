<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = null;
    $fieldError = null;

    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    $profilePic = $_POST['profile_picture'];


    //CONNESSIONE AL DATABASE
    $mysqli = new mysqli("mysql", "root", "root", "db_film");
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }


    // Validazione dei campi
    /*if (empty($name) || empty($surname) || empty($email) || empty($username) || empty($password) || empty($confirm_password)) {
        echo "<script> alert('Tutti i campi sono obbligatori'); </script>";
    }*/

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'email inserita non è valida";
        $fieldError = "email";
    }

    if (strlen($username) < 5) { 
        $error = "Il nome utente deve essere di almeno 5 caratteri";
        $fieldError = "username";
    }


    if (strlen($password) < 8) {
        $error = "La password deve essere di almeno 8 caratteri";
        $fieldError = "password";
    } else if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = "La password deve contenere almeno una lettera maiuscola, una lettera minuscola e un numero";
        $fieldError = "password";
    }
    if ($password !== $confirm_password) {
        $error = "Le password non corrispondono";
        $fieldError = "password";
    }


    // Verifica se l'email è già registrata
    $emailCheckQuery = "SELECT * FROM users WHERE email='$email'";
    $result = $mysqli->query($emailCheckQuery);

    if ($result->num_rows > 0) {
        $error = "L'email inserita è già associata ad un account";
        $fieldError = "email";
    }


    // Verifica se il nome utente è già registrato
    $usernameCheckQuery = "SELECT * FROM users WHERE username='$username'";
    $result = $mysqli->query($usernameCheckQuery);

    if ($result->num_rows > 0) {
        $error = "Il nome utente è già in uso";
        $fieldError = "username";
    }


    //REGISTRAZIONE UTENTE
    if ($error === null) {
        $encryptedPassword = password_hash($password, PASSWORD_DEFAULT);

        $insertUserData = "INSERT INTO users (name, last_name, email, password, username, profile_photo, registration_date) VALUES ('$name', '$surname', '$email', '$encryptedPassword', '$username', '$profilePic', NOW())";

        if ($mysqli->query($insertUserData) === TRUE) {
            $success = true;
            /*echo "<script> setTimeout(function(){ window.location.href = '../../login.html'; }, 3000); </script>";*/
        } else {
            $success = false;
        
            $error = "Errore nella registrazione dell'utente:";
            $fieldError = "registration";
        }
    } else {
        $success = false;
    }

    //INVIO DATI
    header("Content-Type: application/json");
    echo json_encode([
        "status" => 200,
        "fieldError" => $fieldError,
        "error" => $error,
        "success" => $success,
    ]);
    return; 
}
?>