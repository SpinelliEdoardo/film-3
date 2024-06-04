<?php
header('Access-Control-Allow-Origin: *');

require_once("db_query.php");
require_once("recommendation-prova.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_SERVER['PATH_INFO'] == '/movies') {
        //localhost:8000/api.php/movies
        //qui estraggo i file

        if (isset($_GET['title'])) {
            $user_input = $_GET['title'];
            $filter = 'title';
        } else if (isset($_GET['synopsis'])) {
            $user_input = $_GET['synopsis'];
            $filter = 'synopsis';
        } else if (isset($_GET['duration'])) {
            $user_input = $_GET['duration'];
            $filter = 'duration';
        } else if (isset($_GET['released_year'])) {
            $user_input = $_GET['released_year'];
            $filter = 'released_year';
        } else {
            $user_input = NULL;
            $filter = NULL;
        }
        //IL FILTRO SUL POSTER È INUTILE FARLO

        $movies = get_movies($user_input, $filter);


        http_response_code(200);
        header("Content-Type: application/json");
        echo json_encode([
            "status" => 200,
            "message" => "OK",
            "payload" => $movies
        ]);
    } else if ($_SERVER['PATH_INFO'] == '/actors') {
        //localhost:8000/api.php/actors
        //qui estraggo i file

        if (isset($_GET['last_name'])) {
            $user_input = $_GET['last_name'];
            $filter = 'last_name';
        } else if (isset($_GET['name'])) {
            $user_input = $_GET['name'];
            $filter = 'name';
        } else {
            $user_input = NULL;
            $filter = NULL;
        }
        //MANCA FILTRO DATA NASCITA
        //MANCA FILTRO CON PIU CAMPI

        $actors = get_actors($user_input, $filter);

        
        http_response_code(200);
        header("Content-Type: application/json");
        echo json_encode([
            "status" => 200,
            "message" => "OK",
            "payload" => $actors
        ]);
    } else if ($_SERVER['PATH_INFO'] == '/directors') {
        //localhost:8000/api.php/directors
        //qui estraggo i file

        if (isset($_GET['last_name'])) {
            $user_input = $_GET['last_name'];
            $filter = 'last_name';
        } else if (isset($_GET['name'])) {
            $user_input = $_GET['name'];
            $filter = 'name';
        } else {
            $user_input = NULL;
            $filter = NULL;
        }
        //MANCA FILTRO DATA NASCITA
        //MANCA FILTRO CON PIU CAMPI

        $directors = get_directors($user_input, $filter);


        http_response_code(200);
        header("Content-Type: application/json");
        echo json_encode([
            "status" => 200,
            "message" => "OK",
            "payload" => $directors
        ]);
    } else if ($_SERVER['PATH_INFO'] == '/genres') {
        //localhost:8000/api.php/genres
        //qui estraggo i file

        if (isset($_GET['name'])) {
            $user_input = $_GET['name'];
            $filter = 'name';
        } else {
            $user_input = NULL;
            $filter = NULL;
        }

        $genres = get_genres($user_input, $filter);
        

        http_response_code(200);
        header("Content-Type: application/json");
        echo json_encode([
            "status" => 200,
            "message" => "OK",
            "payload" => $genres
        ]);
    } else if ($_SERVER['PATH_INFO'] == '/users') {
        $users = get_users();

        http_response_code(200);
        header("Content-Type: application/json");
        echo json_encode([
            "status" => 200,
            "message" => "OK",
            "payload" => $users
        ]);
    } else if ($_SERVER['PATH_INFO'] == '/recommend') {
        $recommendations = recommend_movies($_GET['id']);

        http_response_code(200);
        header("Content-Type: application/json");
        echo json_encode([
            "status" => 200,
            "message" => "OK",
            "payload" => $recommendations
        ]);
    } else if ($_SERVER['PATH_INFO'] == '/watchlist') {
        $viewed_movies = get_viewedMovies($_GET['id']);

        http_response_code(200);
        header("Content-Type: application/json");
        echo json_encode([
            "status" => 200,
            "message" => "OK",
            "payload" => $viewed_movies
        ]);
    } else {
        http_response_code(404);
        header("Content-Type: application/json");
        echo json_encode([
            "status" => 404,
            "message" => "Not found",
            "payload" => []
        ]);
    }
} else {
    http_response_code(405);
    header("Content-Type: application/json");
    echo json_encode([
        "status" => 405,
        "message" => "Method not allowed",
        "payload" => []
    ]);
}

exit;


/*$viewed_movies = get_users();

        http_response_code(200);
        header("Content-Type: application/json");
        echo json_encode([
            "status" => 200,
            "message" => "OK",
            "payload" => $viewed_movies
        ]);*/
?>