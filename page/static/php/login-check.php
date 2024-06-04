<?php 
session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = null;
    $fieldError = null;

    $emailOrUsername = $_POST['email-username'];
    $password = $_POST['password'];


    //CONNESSIONE AL DATABASE
    $mysqli = new mysqli("mysql", "root", "root", "db_film");
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }


    //SANIFICAZIONE DATI PER SQUL INJECTION
    $emailOrUsername = $mysqli->real_escape_string($emailOrUsername);
    $password = $mysqli->real_escape_string($password);


    if (empty($emailOrUsername) || empty($password)) {
        $error = "Tutti i campi sono obbligatori";
        $fieldError = "login";
        $success = false;
    } else {
        $mailCheck = "SELECT * FROM users WHERE email='$emailOrUsername' OR username='$emailOrUsername'";
        $mailCheckResult = $mysqli->query($mailCheck);

        if ($mailCheckResult) {
            if ($mailCheckResult->num_rows > 0) {
                $userFinded = $mailCheckResult->fetch_assoc();

                if (password_verify($password, $userFinded['password'])) {
                    $_SESSION['user_id'] = $userFinded['id'];
                    $_SESSION['username'] = $userFinded['username'];
                    $_SESSION['email'] = $userFinded['email'];
                    $_SESSION['profile_photo'] = $userFinded['profile_photo'];

                    $success = true;
                } else {
                    $error = "Password errata";
                    $fieldError = "password";
                    $success = false;    
                }
            } else {
                $error = "Email o username non valido";
                $fieldError = "email-username";
                $success = false;
            }
        } else {
            $error = "Errore nel recupero dei dati: " . $mysqli->error;
            $fieldError = "login";
            $success = false;
        }
    }


    $mysqli->close();


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