<?php
    session_start();

    //var_dump($_SESSION);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search a movie</title>

    <link rel="stylesheet" href="static/css/search-style.css">
    <link rel="stylesheet" href="static/css/general.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
</head>
<body>
    <div class="main-container centerAlignment">
        <div class="header flex">
            <a href="#" class="back-button">
                <i class="material-icons back-icon">arrow_back_ios</i>
            </a>
            <h2 class="header-title">Search</h2>
            <a href="#" class="back-button">
                <img src="static/images/movieAdding.jpg" alt="" class="request-movie">
        </div>
         
        <div class="search-bar-div pos-relative">
            <input type="text" name="search-bar" class="search-bar" placeholder="Search a movie">
            <a href="#">
                <i class="material-icons search-icon pos-absolute">search</i>
            </a>
        </div>
        <div class="film-container flex">
            
        </div>
    </div>

    <div class="bottom-menu flex">
        <a href="home.php" class="flex link button-menu-link">
            <i class="material-icons">home</i>
            <h3 class="button-menu-title">Home</h3>
        </a>
        <a href="search.php" class="flex link button-menu-link">
            <i class="material-icons active">search</i>
            <h3 class="button-menu-title active">Search</h3>
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

    <script src="static/js/search.js"></script>
    <script src="static/js/general.js"></script>
</body>
</html>