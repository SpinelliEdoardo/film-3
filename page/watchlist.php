<?php
    session_start();

    //var_dump($_SESSION);
    //$_SESSION['user_id'] = 11;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watch List</title>

    <link rel="stylesheet" href="static/css/watchlist-style.css">
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
            <h2 class="header-title centerAlignment">Watch List</h2>
        </div>
    
        <div class="watch-film-container flex">
            <!--<a href="#" class="film-card flex link">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQfj-Xxr1DlcuFjU4Nj0ZHm2rmEn0e7BBU0xQZzQedaWODnFw7Q" class="film-card-image">
                
                <div class="film-card-information flex">
                    <h3 class="film-card-title">Fight Club</h3>

                    <div class="film-card-details flex">
                        <div class="film-card-rating flex">
                            <i class="material-icons information-icon rating-style">star</i>
                            <h4 class="information-text rating-style">9.5</h4>
                        </div>
                        <div class="film-card-rating flex">
                            <i class="material-icons information-icon">confirmation_number</i>
                            <h4 class="information-text">Action</h4>
                        </div>
                        <div class="film-card-rating flex">
                            <i class="material-icons information-icon">calendar_today</i>
                            <h4 class="information-text">2005</h4>
                        </div>
                        <div class="film-card-rating flex">
                            <i class="material-icons information-icon">schedule</i>
                            <h4 class="information-text">135min</h4>
                        </div>
                    </div>
                </div>

                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQfj-Xxr1DlcuFjU4Nj0ZHm2rmEn0e7BBU0xQZzQedaWODnFw7Q" alt="" style="border-radius: 50px; width: 80px; height: 80px; margin: auto;">
            </a>-->
        </div>
    </div>
    
    <div class="bottom-menu flex">
        <a href="home.php" class="flex link button-menu-link">
            <i class="material-icons">home</i>
            <h3 class="button-menu-title">Home</h3>
        </a>
        <a href="search.php" class="flex link button-menu-link">
            <i class="material-icons">search</i>
            <h3 class="button-menu-title">Search</h3>
        </a>
        <a href="watchlist.php" class="flex link button-menu-link">
            <i class="material-icons active">bookmark</i>
            <h3 class="button-menu-title active">Watchlist</h3>
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

    <script src="static/js/watchlist.js" data-userid="<?php if (isset($_SESSION['user_id'])): echo $_SESSION['user_id']; else: echo "null"; endif;?>"></script>
    <script src="static/js/general.js"></script>
</body>
</html>