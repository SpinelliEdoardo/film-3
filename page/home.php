<?php
    session_start();
    //var_dump($_SESSION);

    //PROVA CAMBIO ID PER CHECK RECOMMENDATION
    //$_SESSION['user_id'] = 11;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <link rel="stylesheet" href="static/css/home-style.css">
    <link rel="stylesheet" href="static/css/general.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
</head>
<body>
    <div class="main-container centerAlignment">
        <?php if (isset($_SESSION['username'])): ?>
            <h1 class="film-question">Welcome Back <?php echo $_SESSION['username']?></h1>
        <?php else: ?>
            <h1 class="film-question">What do you want to watch?</h1>
        <?php endif; ?>
        <div class="search-bar-div pos-relative">
            <input type="text" name="search-bar" class="search-bar" placeholder="Search a movie">
            <a href="#">
                <i class="material-icons search-icon pos-absolute">search</i>
            </a>
        </div>

        <div class="top-movies flex pos-relative">
            <div class="movie-big pos-relative" id=" movie-big-1">
                <a href="#" class="movie-big-link">
                    <img src="https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk" class="movie-big-image" alt="">
                </a>
                <h2 class="movie-big-num pos-absolute">1</h2>
            </div>
            <div class="movie-big pos-relative" id=" movie-big-2">
                <a href="#" class="movie-big-link">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQfj-Xxr1DlcuFjU4Nj0ZHm2rmEn0e7BBU0xQZzQedaWODnFw7Q" class="movie-big-image" alt="">
                </a>
                <h2 class="movie-big-num pos-absolute">2</h2>
            </div>
            <div class="movie-big pos-relative" id=" movie-big-3">
                <a href="#" class="movie-big-link">
                    <img src="https://www.corrierenerd.it/wp-content/uploads/2024/02/episode1.jpg" class="movie-big-image" alt="">
                </a>
                <h2 class="movie-big-num pos-absolute">3</h2>
            </div> 
            <div class="movie-big pos-relative" id=" movie-big-4">
                <a href="#" class="movie-big-link">
                    <img src="https://cultura.biografieonline.it/wp-content/uploads/2012/09/Fight-Club-locandina.jpg" class="movie-big-image" alt="">
                </a>
                <h2 class="movie-big-num pos-absolute">4</h2>
            </div>
            <div class="movie-big pos-relative" id=" movie-big-5">
                <a href="#" class="movie-big-link">
                    <img src="https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk" class="movie-big-image" alt="">
                </a>
                <h2 class="movie-big-num pos-absolute">5</h2>
            </div>
        </div>

        <div class="categories-container">
            <nav class="categories-nav">
                <ul class="categories-list flex">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="categories-li listItem"> 
                            <a href="#" class="categories-link link" data-category="forYou">For you</a>
                        </li>
                    <?php endif; ?>
                    <li class="categories-li listItem">
                        <a href="#" class="categories-link link category-selected" data-category="popular">Popular</a>
                    </li>
                    <li class="categories-li listItem">
                        <a href="#" class="categories-link link" data-category="upcoming">Upcoming</a>
                    </li>
                    <!--<li>
                        <select name="" data-category=""></select>Categories
                    </li>-->
                    <li class="categories-li listItem">
                        <a href="#" class="categories-link link" data-category="topRated">Top Rated</a>
                    </li>
                </ul>
            </nav>

            <div class="category-movies flex pos-relative">
                
            </div>
        </div>
    </div>

    <div class="bottom-menu flex">
        <a href="home.php" class="flex link button-menu-link">
            <i class="material-icons active">home</i>
            <h3 class="button-menu-title active">Home</h3>
        </a>
        <a href="search.php" class="flex link button-menu-link">
            <i class="material-icons">search</i>
            <h3 class="button-menu-title">Search</h3>
        </a>
        <a href="watchlist.php" class="flex link button-menu-link">
            <i class="material-icons">bookmark</i>
            <h3 class="button-menu-title">Watchlist</h3>
        </a>
        <?php if (isset($_SESSION['profile_photo'])): ?>
            <a href="account.php" class="flex link button-menu-link">
                <img src="static/images/fotoProfilo/<?php echo $_SESSION['profile_photo']; ?>" alt="Profile Image" class="profile-img">
            </a>
        <?php else: ?>
            <a href="login.html" class="flex link button-menu-link">
                <i class="material-icons login-icon">person</i>
                <h3 class="button-menu-title">Account</h3>
            </a>
        <?php endif; ?>
    </div>

    <script src="static/js/home.js" data-userid="<?php echo $_SESSION['user_id']; ?>"></script>
    <script src="static/js/general.js"></script>
</body>
</html>