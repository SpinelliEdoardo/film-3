<?php
header('Access-Control-Allow-Origin: *');


function recommend_movies($user_id) {
    $mysqli = new mysqli("mysql","root","root","db_film");
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }

    $movieMatrix = collaborativeFiltering_build_matrix();
    //return $movieMatrix;


    $most_similar_user = get_most_similar_user($user_id, $movieMatrix);
    //return $most_similar_user;

    //RISOLVERE SITUAZIONE CON USER 12 E 3 PERCHE HANNO COSINE 1 E AVENDO VISTO GLI STESSI FILM HANNO 0 NEL PAYLOAD


    $user_movies = $movieMatrix[$user_id];
    $similar_user_movies = $movieMatrix[$most_similar_user['user_id']];


    $recommended_movies = array();

    foreach ($similar_user_movies as $movie_id => $rating) {
        if ($rating > 0 && $user_movies[$movie_id] == 0) {
            $movieQuery = "SELECT * FROM movie WHERE id = $movie_id";

            $movieResult = $mysqli->query($movieQuery);
            $movie = query_join($movieResult);

            $recommended_movies[] = $movie;
        }
    }

    

    return $recommended_movies;
}



function collaborativeFiltering_build_matrix() {
    $mysqli = new mysqli("mysql","root","root","db_film");
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }


    /*GET MOVIES*/
    $moviesQuery = 'SELECT * FROM movie';
    $moviesResult = $mysqli -> query($moviesQuery);

    while ($moviesRow = $moviesResult -> fetch_assoc()) {
        $movies[] = $moviesRow;
    }
    $filmNumber = count($movies);
    //return $movies;
        
    $users = get_users();

    $userFilms = array();

    foreach ($users as $user) {
        $user_id = $user['id'];

        $watchFilms = get_user_ratings($user_id);

        $userFilms[$user_id] = array_fill(1, $filmNumber, 0);

        foreach ($watchFilms as $row) {
            $userFilms[$user_id][$row['movie_id']] = $row['rating'];
        }
    }

    return $userFilms;
}


function get_most_similar_user($user_id, $movieMatrix) {
    $users_distances = array(); 
    $user_array = $movieMatrix[$user_id];
   
    foreach ($movieMatrix as $index => $user) {
        if ($index != $user_id) {
            $distance = cosine_similarity($user_array, $user);
            
            $users_distances[] = array($index, $distance);
        }
    }
    
    $max_distance = -1;
    $most_similar_user_id = -1;

    foreach ($users_distances as $key => $row) {
        $user_distance = $row[1];
        //echo $row[1];
        /*echo '<pre>';
        echo print_r($users_distance);
        echo '</pre>';*/

        if ($user_distance > $max_distance) {
            $max_distance = $user_distance;
            $most_similar_user_id = $row[0];
        }
    }

    $most_similar_user = $most_similar_user_id;

    return [
        "user_id" => $most_similar_user,
        "similarity" => $max_distance
    ];
}


function cosine_similarity($a, $b) {
    $dist = 0;

    $modulo_a = 0;
    $modulo_b = 0;
    $denom = 0;
    $numeratore = 0;
    
    foreach ($a as $key => $value) {
        $modulo_a = $modulo_a + pow($value, 2);
        $modulo_b = $modulo_b + pow($b[$key], 2);
    }
    $modulo_a = sqrt($modulo_a);
    $modulo_b = sqrt($modulo_b);

    $denom = $modulo_a * $modulo_b;

    if ($denom == 0) {
        return null;
    } else {
        foreach ($a as $key => $value) {
            $numeratore = $numeratore + ($value * $b[$key]);
        }
    
        $dist = $numeratore / $denom;
        //echo $dist;
    
        return $dist;
    }
}
?>