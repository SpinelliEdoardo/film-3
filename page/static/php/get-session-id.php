<?php
/*header('Access-Control-Allow-Origin: *');

session_start();

header('Content-Type: application/json');

$response = array(
    "loggedIn" => false
);

if (isset($_SESSION['user'])) {
    $response["loggedIn"] = true;
    $response["user"] = array(
        "profileImageUrl" => $_SESSION['user']['profileImageUrl']
    );
}

error_log("Auth status response: " . json_encode($response));
echo json_encode($response);*/

session_start();

var_dump($_SESSION);

if (isset($_SESSION['user_id'])) {
    //$sessionID = session_id();
    //echo json_encode(array("sessionID" => $sessionID));

    echo json_encode(array("sessionID" => $sessionID));
} else {
    echo json_encode(array("error" => "L'utente non è autenticato"));
}
?>