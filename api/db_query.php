<?php
header('Access-Control-Allow-Origin: *');

function get_movies($user_input, $filter) {  
    $movies = array();

    $mysqli = new mysqli("mysql","root","root","db_film");
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }

    if ($user_input !== NULL) {
        $moviesQuery = 'SELECT * FROM movie WHERE '. $filter .' LIKE "%'.$user_input.'%"';

        /*if ($filter === 'title') {  
            $moviesQuery = 'SELECT * FROM movie WHERE title LIKE "%'.$user_input.'%"';
        } else if ($filter === 'duration') {   VA FATTO CON UN INTERVALLO DI TEMPO
            $moviesQuery = 'SELECT * FROM movie WHERE duration LIKE "%'.$user_input.'%"';
        } else if ($filter === 'released_year') {
            $moviesQuery = 'SELECT * FROM movie WHERE released_year LIKE "%'.$user_input.'%"';
        }*/
        //IL FILTRO SUL POSTER È INUTILE FARLO
    } else if ($user_input === NULL) {
        $moviesQuery = 'SELECT * FROM movie';
    }

    $moviesResult = $mysqli -> query($moviesQuery);
    $movies = query_join($moviesResult);

    $mysqli -> close();

    return $movies;
}


function get_actors($user_input, $filter) { 
    $actors = array();

    $mysqli = new mysqli("mysql","root","root","db_film");
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }

    if ($user_input !== NULL) {
        $actorsQuery = 'SELECT * FROM actor WHERE '. $filter .' LIKE "%'.$user_input.'%"';

        /*if ($filter === 'last_name') {
            $actorsQuery = 'SELECT * FROM actor WHERE last_name LIKE "%'.$user_input.'%"';
        } else if ($filter === 'name') {  
            $actorsQuery = 'SELECT * FROM actor WHERE name LIKE "%'.$user_input.'%"';
        }*/
        //MANCA FILTRO DATA NASCITA
        //MANCA FILTRO CON PIU CAMPI
    } else if ($user_input === NULL) {
        $actorsQuery = 'SELECT * FROM actor';
    }

    $actorsResult = $mysqli -> query($actorsQuery);

    while ($actorsRow = $actorsResult -> fetch_assoc()) {
        $actors[] = $actorsRow;

        $last_actor = $actors[count($actors) - 1];
        $actorID = $last_actor['id'];


        //MOVIES
        $moviesQuery = 'SELECT movie.* FROM movie_actor 
        INNER JOIN movie ON movie.id = movie_actor.movie_id
        WHERE movie_actor.actor_id = '. $actorID;

        $moviesResult = $mysqli -> query($moviesQuery);
        if (!$moviesResult) {
            die("Error retrieving actors for movie $actorID: " . $mysqli -> connect_error);
        }

        while ($moviesRow = $moviesResult -> fetch_assoc()) {
            $actors[count($actors) - 1]['movies'][] = $moviesRow;
        }
    }


    $mysqli -> close();

    return $actors;
}


function get_directors($user_input, $filter) {  
    $directors = array();

    $mysqli = new mysqli("mysql","root","root","db_film");
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }

    
    if ($user_input !== NULL) {
        $directorsQuery = 'SELECT * FROM director WHERE '. $filter .' LIKE "%'.$user_input.'%"';

        /*if ($filter === 'last_name') {
            $directorsQuery = 'SELECT * FROM director WHERE last_name LIKE "%'.$user_input.'%"';
        } else if ($filter === 'name') {  
            $directorsQuery = 'SELECT * FROM director WHERE name LIKE "%'.$user_input.'%"';
        }*/
        //MANCA FILTRO DATA NASCITA
        //MANCA FILTRO CON PIU CAMPI
        
    } else if ($user_input === NULL) {
        $directorsQuery = 'SELECT * FROM director';
    }
    
    $directorsResult = $mysqli -> query($directorsQuery);

    while ($directorsRow = $directorsResult -> fetch_assoc()) {
        $directors[] = $directorsRow;

        $last_director = $directors[count($directors) - 1];
        $directorID = $last_director['id'];


        //MOVIES
        $moviesQuery = 'SELECT movie.* FROM movie_director 
        INNER JOIN movie ON movie.id = movie_director.movie_id
        WHERE movie_director.director_id = '. $directorID;

        $moviesResult = $mysqli -> query($moviesQuery);
        if (!$moviesResult) {
            die("Error retrieving directors for movie $directorID: " . $mysqli -> connect_error);
        }

        while ($moviesRow = $moviesResult -> fetch_assoc()) {
            $directors[count($directors) - 1]['movies'][] = $moviesRow;
        }
    }


    $mysqli -> close();

    return $directors;
}


function get_genres($user_input, $filter) {  
    $genres = array();

    $mysqli = new mysqli("mysql","root","root","db_film");
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }

    if ($user_input !== NULL) {
        $genresQuery = 'SELECT * FROM genre WHERE '. $filter .' LIKE "%'.$user_input.'%"';

        //NON SERVE IL FILTRO SULLO SLUG
    } else if ($user_input === NULL) {
        $genresQuery = 'SELECT * FROM genre';
    }

    $geresResult = $mysqli -> query($genresQuery);

    while ($genresRow = $geresResult -> fetch_assoc()) {
        $genres[] = $genresRow;

        $last_genre = $genres[count($genres) - 1];
        $genreID = $last_genre['id'];


        //MOVIES
        $moviesQuery = 'SELECT movie.* FROM movie_genre 
        INNER JOIN movie ON movie.id = movie_genre.movie_id
        WHERE movie_genre.genre_id = '. $genreID;

        $moviesResult = $mysqli -> query($moviesQuery);
        if (!$moviesResult) {
            die("Error retrieving genres for movie $genreID: " . $mysqli -> connect_error);
        }

        while ($moviesRow = $moviesResult -> fetch_assoc()) {
            $genres[count($genres) - 1]['movies'][] = $moviesRow;
        }
    }


    $mysqli -> close();

    return $genres;
}


function query_join($moviesResult) {
    $mysqli = new mysqli("mysql","root","root","db_film");
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }

    while ($moviesRow = $moviesResult -> fetch_assoc()) {
        $movies[] = $moviesRow;

        $last_movie = $movies[count($movies) - 1];
        $movieID = $last_movie['id'];


        //ACTORS
        $actorsQuery = 'SELECT actor.* FROM movie_actor 
        INNER JOIN actor ON actor.id = movie_actor.actor_id 
        WHERE movie_actor.movie_id = '.$movieID;

        $actorsResult = $mysqli -> query($actorsQuery);
        if (!$actorsResult) {
            die("Error retrieving actors for movie $movieID: " . $mysqli -> connect_error);
        } 

        while ($actorsRow = $actorsResult -> fetch_assoc()) {
            $movies[count($movies) - 1]['actors'][] = $actorsRow;
        }


        //DIRECTORS
        $directorsQuery = 'SELECT director.* FROM movie_director 
        INNER JOIN director ON director.id = movie_director.director_id 
        WHERE movie_director.movie_id = '.$movieID;

        $directorsResult = $mysqli -> query($directorsQuery);
        if (!$directorsResult) {
            die("Error retrieving directors for movie $movieID: " . $mysqli -> connect_error);
        }

        while ($directorsRow = $directorsResult -> fetch_assoc()) {
            $movies[count($movies) - 1]['directors'][] = $directorsRow;
        }


        //GENRES
        $genresQuery = 'SELECT genre.* FROM movie_genre 
        INNER JOIN genre ON genre.id = movie_genre.genre_id 
        WHERE movie_genre.movie_id = '.$movieID;

        $genresResult = $mysqli -> query($genresQuery);
        if (!$genresResult) {
            die("Error retrieving genres for movie $movieID: " . $mysqli -> connect_error);
        }

        while ($genresRow = $genresResult -> fetch_assoc()) {
            $movies[count($movies) - 1]['genres'][] = $genresRow;
        }
    }

    return $movies;
}


function get_viewedMovies($user_id) {
    $viewed_movies = array();

    $mysqli = new mysqli("mysql","root","root","db_film");
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }

    $moviesQuery = 'SELECT * FROM movie WHERE id IN (SELECT movie_id FROM movie_user WHERE user_id = '.$user_id.')';
    $moviesResult = $mysqli -> query($moviesQuery);

    $viewed_movies = query_join($moviesResult);


    $mysqli -> close();

    return $viewed_movies;
}


function get_users() {
    $users = array();

    $mysqli = new mysqli("mysql","root","root","db_film");
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }


    $usersQuery = 'SELECT * FROM users';
    $usersResult = $mysqli -> query($usersQuery);

    while ($usersRow = $usersResult -> fetch_assoc()) {
        $users[] = $usersRow;
    }
    //return $users;


    $mysqli -> close();

    return $users;
}


function get_user_ratings($user_id) {
    $watchFilms = array();

    $mysqli = new mysqli("mysql","root","root","db_film");
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }

    $watchFilmsQuery = 'SELECT * FROM movie_user WHERE user_id = ' . $user_id;
    $watchFilmsResult = $mysqli -> query($watchFilmsQuery);

    while ($watchFilmsRow = $watchFilmsResult -> fetch_assoc()) {
        $watchFilms[] = $watchFilmsRow;
    }
    //return $watchFilms;


    $mysqli -> close();

    return $watchFilms;
}
// fetch_assoc() restituisce un array associativo | chiave - valore
// fecth_array() restituisce un array di array | 0 - n, gli indici dipendono dalla query
?>