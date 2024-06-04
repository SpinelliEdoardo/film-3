<?php
header('Access-Control-Allow-Origin: *');

require_once("db_query.php");


function recommend_movies($user_id) {
    $mysqli = new mysqli("mysql","root","root","db_film");
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }

    $watchedMovies = contentBased_build_matrix($user_id);
    //return $watchedMovies;

    $genre_map = get_database_genres($watchedMovies);
    //return $genre_map;

    $userProfile = build_user_profile($watchedMovies, $genre_map, $user_id);
    //return $userProfile;

    $candidate_movies_matrix = build_candidateMovies_matrix($watchedMovies, $genre_map);
    //return $candidate_movies_matrix;


    $most_similar_items = get_most_similar_items($userProfile, $candidate_movies_matrix);
    //return $most_similar_items;

    foreach ($most_similar_items as $movie_id => $content_similarity) {
        $recommendationsQuery = "SELECT * FROM movie WHERE id = $movie_id";

        $recommendationsResult = $mysqli->query($recommendationsQuery);
        $recommendations[] = query_join($recommendationsResult);
    }
    return $recommendations;
}



//CONTENT BASED FILTERING FUNCTIONS
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
    /*"1": {
      "Adventure": 1,
      "Sci-Fi": 1,
      "Action": 0,
      "Fantastic Cinema": 0,
      "Family": 0,
      "Thriller": 0,
      "Mystery": 0
    },
    "3": {
      "Adventure": 0,
      "Sci-Fi": 1,
      "Action": 0,
      "Fantastic Cinema": 0,
      "Family": 1,
      "Thriller": 0,
      "Mystery": 0
    },
    "4": {
      "Adventure": 0,
      "Sci-Fi": 0,
      "Action": 0,
      "Fantastic Cinema": 0,
      "Family": 0,
      "Thriller": 1,
      "Mystery": 1
    }*/


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
    /*"1": {
      "Adventure": 9,
      "Sci-Fi": 9,
      "Action": 0,
      "Fantastic Cinema": 0,
      "Family": 0,
      "Thriller": 0,
      "Mystery": 0
    },
    "3": {
      "Adventure": 0,
      "Sci-Fi": 9,
      "Action": 0,
      "Fantastic Cinema": 0,
      "Family": 9,
      "Thriller": 0,
      "Mystery": 0
    },
    "4": {
      "Adventure": 0,
      "Sci-Fi": 0,
      "Action": 0,
      "Fantastic Cinema": 0,
      "Family": 0,
      "Thriller": 9,
      "Mystery": 9
    }*/


    //SUM OF THE VALUES OF THE USER PROFILE
    $userProfileSum = array_fill_keys(array_values($genre_map), 0);

    foreach ($weightedGenreMatrix as $movie) {
        foreach ($movie as $genre => $value) {
            $userProfileSum[$genre] += $value;
        }
    }
    //return $userProfileSum;
    /*"Adventure": 17.5,
    "Sci-Fi": 22.5,
    "Action": 22.5,
    "Fantastic Cinema": 7,
    "Family": 6.5,
    "Thriller": 8,
    "Mystery": 8*/

    $userProfile = normalization($userProfileSum);

    
    $mysqli->close();


    return $userProfile;
    /*"Adventure": 0.6875,
    "Sci-Fi": 1,
    "Action": 1,
    "Fantastic Cinema": 0.03125,
    "Family": 0,
    "Thriller": 0.09375,
    "Mystery": 0.09375*/
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
    //return $unseenMovies;
    /*{
      "id": "2",
      "title": "The Avengers"
    },
    {
      "id": "3",
      "title": "Star Wars: Episode I - The Phantom Menace"
    },
    {
      "id": "4",
      "title": "Fight Club"
    },
    {
      "id": "5",
      "title": "Black Panther: Wakanda Forever"
    },
    {
      "id": "6",
      "title": "Guardians of the Galaxy Vol. 3"
    }*/

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
    //return $movieGenres;
    /*{
      "id": "1",
      "name": "Adventure"
    },
    {
      "id": "1",
      "name": "Sci-Fi"
    },
    {
      "id": "3",
      "name": "Sci-Fi"
    },
    {
      "id": "3",
      "name": "Family"
    },
    {
      "id": "4",
      "name": "Thriller"
    },
    {
      "id": "4",
      "name": "Mystery"
    }*/

    //genre_map
    /*"1": "Adventure",
    "2": "Sci-Fi",
    "3": "Action",
    "4": "Fantastic Cinema",
    "5": "Family",
    "6": "Thriller",
    "7": "Mystery"*/

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