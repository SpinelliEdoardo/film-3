<?php
header('Access-Control-Allow-Origin: *');


function recommend_movies($user_id) {
    $mysqli = new mysqli("mysql","root","root","db_film");
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }


    $recommend_movies = array();


    /*-------------COLLABORATIVE FILTERING RECOMMENDATION--------------*/
    $movieMatrix = collaborativeFiltering_build_matrix();
    //return $movieMatrix;


    $most_similar_user = get_most_similar_user($user_id, $movieMatrix);
    //return $most_similar_user;

    //RISOLVERE SITUAZIONE CON USER 12 E 3 PERCHE HANNO COSINE 1 E AVENDO VISTO GLI STESSI FILM HANNO 0 NEL PAYLOAD


    $user_movies = $movieMatrix[$user_id];
    $similar_user_movies = $movieMatrix[$most_similar_user['user_id']];


    $collabFilteringRecommendations = array();

    foreach ($similar_user_movies as $movie_id => $rating) {
        if ($rating > 0 && $user_movies[$movie_id] == 0) {
            $movieQuery = "SELECT * FROM movie WHERE id = $movie_id";

            $movieResult = $mysqli->query($movieQuery);
            $movie = query_join($movieResult);

            $collabFilteringRecommendations[] = $movie;
        }
    }
    //return $collabFilteringRecommendations;


    $recommend_movies = $collabFilteringRecommendations;
    //return $recommend_movies;



    /*-------------CONTENT BASED RECOMMENDATION--------------*/
    $watchedMovies = contentBased_build_matrix($user_id);
    //return $watchedMovies;

    $genre_map = get_database_genres($watchedMovies);
    //return $genre_map;

    $userProfile = build_user_profile($watchedMovies, $genre_map, $user_id);
    //return $userProfile;

    $candidate_movies_matrix = build_candidateMovies_matrix($watchedMovies, $genre_map);
    //return $candidate_movies_matrix;

    //GESTIRE SITUAZIONE DEI FILM CON CONTENT SIMILAITY 0 O VICINA A 0 (FORSE MENO DI 0.5)

    
    $most_similar_items = get_most_similar_items($userProfile, $candidate_movies_matrix);
    //return $most_similar_items;

    
    $contentBasedRecommendations = array();

    foreach ($most_similar_items as $movie_id => $content_similarity) {
        if ($content_similarity <= 0.5) {
            continue;
        }
        
        $movieId = $movie_id;
        $isAlreadyRecommended = false;

        foreach ($collabFilteringRecommendations as $collabRecommendationMovie) {
            $collabMovieId = $collabRecommendationMovie[0]['id'];
            //echo $collabMovieId;

            if ($movieId == $collabMovieId) {
                $isAlreadyRecommended = true;
            }
        }
        
        if (!$isAlreadyRecommended) {
            $recommendationsQuery = "SELECT * FROM movie WHERE id = $movie_id";
            $recommendationsResult = $mysqli->query($recommendationsQuery);
            
            $contentBasedRecommendations[] = query_join($recommendationsResult);
        }
    }
    //return $contentBasedRecommendations;

    
    $recommend_movies = array_merge($recommend_movies, $contentBasedRecommendations);
    return $recommend_movies;

    
}




/*-------------------COLLABORATIVE FILTERING FUNCTIONS-------------------*/
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




/*-------------------CONTENT BASED FILTERING FUNCTIONS-------------------*/
function build_user_profile($userMovieMatrix, $genre_map, $user_id) {
    $mysqli = new mysqli("mysql","root","root","db_film");
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }


    //GET THE GENRES OF THE MOVIES WATCHED BY THE USER
    $watchedMovies_genres = get_watchedMovies_genres($user_id);


    $genreMatrix = [];

    //BUILDING MATRIX WITH 0 AND 1 FOR  GENRES OF THE MOVIES WATCHED
    foreach ($watchedMovies_genres as $row => $movie) {
        $userMovieId = $movie['id'];
        $userMovieGenreName = $movie['name'];

        if (!isset($genreMatrix[$userMovieId])) {
            $genreMatrix[$userMovieId] = array_fill_keys(array_values($genre_map), 0);
        }
    
        foreach ($genre_map as $genre_id => $genre_name) {
            if ($genre_name === $userMovieGenreName) {
                $genreMatrix[$userMovieId][$genre_name] = 1;
            }
        }
    }
    //return $genreMatrix;


    //BUILDING TABLE RATING FOR GENRES MULTIPLYING THE RATING OF THE MOVIE AND THE GENRE VALUE 1
    foreach ($userMovieMatrix as $movie_id => $rating) {
        if (isset($genreMatrix[$movie_id])) {
            foreach ($genreMatrix[$movie_id] as $genre => $value) {
                if (!isset($weightedGenreMatrix[$movie_id])) {
                    $weightedGenreMatrix[$movie_id] = [];
                }
                $weightedGenreMatrix[$movie_id][$genre] = $rating * $value;
            }
        }
    }
    //return $weightedGenreMatrix;


    //SUM OF THE VALUES OF THE USER PROFILE
    $userProfileSum = array_fill_keys(array_values($genre_map), 0);

    foreach ($weightedGenreMatrix as $movie) {
        foreach ($movie as $genre => $value) {
            $userProfileSum[$genre] += $value;
        }
    }

    $userProfile = normalization($userProfileSum);

    
    $mysqli->close();


    return $userProfile;
}


function get_most_similar_items($userProfile, $candidate_movies_matrix) {
    $recommendations = array();


    //BUILDING TABLE RATING FOR GENRES MULTIPLYING THE RATING OF THE MOVIE AND THE GENRE VALUE 1
    foreach ($candidate_movies_matrix as $filmId => $filmGenres) {
        $weighted_recommendMovies_matrix[$filmId] = [];
    
        foreach ($userProfile as $genre => $userScore) {
            $weighted_recommendMovies_matrix[$filmId][$genre] = $userScore * $filmGenres[$genre];
        }
    }

    foreach ($weighted_recommendMovies_matrix as $filmId => $filmGenres) {
        $recommendations[$filmId] = array_sum($filmGenres);
    }

    arsort($recommendations);


    return $recommendations;
}


function normalization($userProfilenotNormalized) {
    /*La normalizzazione min-max è appropriata quando si desidera che i valori siano compresi in un intervallo specifico e 
    si vogliono mantenere le proporzioni relative tra i valori. Questo approccio può essere utile nel nostro contesto per assicurarsi 
    che le preferenze dell'utente siano rappresentate in modo uniforme e confrontabili con altre caratteristiche dei film.*/
    $min = min($userProfilenotNormalized);
    $max = max($userProfilenotNormalized);
    
    $normalized = [];//16
    
    foreach ($userProfilenotNormalized as $key => $value) {
        if ($max - $min != 0) { 
            $numeratore = $value - $min;
            $denominatore = $max - $min;

            $normalized[$key] = $numeratore / $denominatore;
        } else {
            $normalized[$key] = 0; 
        }
    }
    
    return $normalized;
}



//BUILD UTIL MATRIX FUNCTIONS
function contentBased_build_matrix($user_id) {
    $mysqli = new mysqli("mysql","root","root","db_film");
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }
    

    $userFilms = array();

    $watchFilms = get_user_ratings($user_id);

    foreach ($watchFilms as $row) {
        $userFilms[$row['movie_id']] = $row['rating'];
    }

    $mysqli->close();

    return $userFilms;
}

function build_candidateMovies_matrix($watchedMovies, $genre_map) {
    $mysqli = new mysqli("mysql","root","root","db_film");
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }

    $databaseMoviesQuery = 'SELECT m.id, m.title FROM movie m';
    $databaseMoviesResult = $mysqli -> query($databaseMoviesQuery);

    while ($databaseMoviesRow = $databaseMoviesResult -> fetch_assoc()) {
        $databaseMovies[] = $databaseMoviesRow;
    }

    $unseenMovies = array();

    // Ottieni i film non visti dall'utente
    foreach ($databaseMovies as $movie) {
        $movieId = $movie['id'];
        if (!isset($watchedMovies[$movieId])) {
            $unseenMovies[] = $movie;
        }
    }
    

    $unseenMoviesGenres = array(); 
    
    foreach ($unseenMovies as $unseenMovie) {
        $movieId = $unseenMovie['id'];
        
        $unseenMoviesGenresQuery = 'SELECT m.id, g.name FROM movie m
            INNER JOIN movie_genre mg ON m.id = mg.movie_id
            INNER JOIN genre g ON mg.genre_id = g.id
            WHERE mg.movie_id ='. $movieId;
        $unseenMoviesGenresResult = $mysqli -> query($unseenMoviesGenresQuery);
        
        while ($unseenMoviesGenresRow = $unseenMoviesGenresResult -> fetch_assoc()) {
            $unseenMoviesGenres[] = $unseenMoviesGenresRow;
        }
    }
    //return $unseenMoviesGenres;


    $candidate_movies_matrix = [];

    //BUILDING MATRIX WITH 0 AND 1 FOR  GENRES OF THE MOVIES WATCHED
    foreach ($unseenMoviesGenres as $row => $movie) {
        $userMovieId = $movie['id'];
        $userMovieGenreName = $movie['name'];

        if (!isset($candidate_movies_matrix[$userMovieId])) {
            $candidate_movies_matrix[$userMovieId] = array_fill_keys(array_values($genre_map), 0);
        }
    
        foreach ($genre_map as $genre_id => $genre_name) {
            if ($genre_name === $userMovieGenreName) {
                $candidate_movies_matrix[$userMovieId][$genre_name] = 1;
            }
        }
    }

    $mysqli->close();

    return $candidate_movies_matrix;
}



//GET DATABASE DATA FUNCTIONS
function get_watchedMovies_genres($user_id) {
    $mysqli = new mysqli("mysql","root","root","db_film");
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }


    $movieGenresQuery = 'SELECT m.id, g.name FROM movie_user mu
        INNER JOIN movie m ON mu.movie_id = m.id
        INNER JOIN movie_genre mg ON m.id = mg.movie_id
        INNER JOIN genre g ON mg.genre_id = g.id
        WHERE mu.user_id ='. $user_id;
    $movieGenresResult = $mysqli -> query($movieGenresQuery);

    while ($movieGenresRow = $movieGenresResult -> fetch_assoc()) {
        $movieGenres[] = $movieGenresRow;
    }

    $mysqli->close();

    return $movieGenres;
}


function get_database_genres($movieMatrix) {
    $mysqli = new mysqli("mysql","root","root","db_film");
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }


    $genresQuery = 'SELECT * FROM genre';
    $genresResult = $mysqli -> query($genresQuery);

    while ($genresRow = $genresResult -> fetch_assoc()) {
        $genres[] = $genresRow;
    }

    $genre_map = array();
    foreach ($genres as $genre) {
        $genre_map[$genre['id']] = $genre['name'];
    }

    $mysqli->close();

    return $genre_map;
}

?>