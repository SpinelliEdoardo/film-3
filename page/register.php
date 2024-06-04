<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
  <title>Registration</title>

  <link rel="stylesheet" href="static/css/register.css">
  <link rel="stylesheet" href="static/css/general.css">
  
  <link rel="stylesheet" href="static/libraries/swiper/package/css/swiper.min.css">
  <link rel="stylesheet" href="static/libraries/swiper/css/styles.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
</head>
<body>
    <div class="main-container centerAlignment pos-relative">
        <img src="static/images/popcornLoader.gif" alt="Loader" class="page-loader">
    
        <div class="header flex">
            <a href="login.html" class="back-login-button">
                <i class="material-icons back-login-button">login</i>
            </a>
            <h1 class="header-title">REGISTRATION</h1>
            <a href="home.php" class="home-button">
                <i class="material-icons back-icon">home</i>
            </a>
        </div>

        <form class="register-form flex" id="register-form" action="static/php/register-functions.php" method="post">
            <div class="dataField-container flex">
                <div class="data-field flex" id="email-field">
                    <input type="text" placeholder="E-mail" id="email" name="email" class="data-field-input" autocomplete="off">
                    <p class="data-field-error" id="email-error"></p>
                </div>
                <div class="anagrafical-data flex">
                    <div class="data-field flex" id="name-field">
                        <input type="text" placeholder="Name" id="name" name="name" class="data-field-input" autocomplete="off">
                        <p class="data-field-error" id="name-error"></p>
                    </div>
                    <div class="data-field flex" id="surname-field">
                        <input type="text" placeholder="Surname" id="surname" name="surname" class="data-field-input" autocomplete="off">
                        <p class="data-field-error" id="surname-error"></p>
                    </div>
                </div>
                <div class="data-field flex" id="username-field">
                    <input type="text" placeholder="Username" id="username" name="username" class="data-field-input" autocomplete="off">
                    <p class="data-field-error" id="username-error"></p>
                </div>

                <div class="password-container flex">
                    <div class="password-field flex">
                        <div class="data-field flex pos-relative" id="password-field">
                            <input type="password" id="password-input" placeholder="Password" name="password" class="data-field-input" autocomplete="off">
                            <a href="#" class="password-visibility-button link pos-absolute" id="pw-visib">
                                <i class="material-icons" id="pw-visib-icon">visibility</i>
                            </a>
                        </div>
                        <p class="data-field-error" id="password-error"></p>
                    </div>

                    <div class="confirm-password-container flex">
                        <div class="data-field flex pos-relative" id="confirm-password-field">
                            <input type="password" id="confirm-password-input" placeholder="Confirm Password" name="confirm-password" class="data-field-input" autocomplete="off">
                            <a href="#" class="password-visibility-button link pos-absolute" id="confirm-pw-visib">
                                <i class="material-icons" id="confirm-password-visib-icon">visibility</i>
                            </a>
                        </div>
                        <p class="data-field-error" id="password-confirm-error"></p>
                    </div>
                </div>
            </div>

            <div class="profile-foto-picker">
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        <?php
                        $directory = 'static/images/fotoProfilo/';
                        
                        $handler = opendir($directory);
                        
                        if ($handler) {
                            while (($file = readdir($handler)) !== false) {
                                if ($file === '.' || $file === '..') {
                                    continue;
                                }
                        
                                echo '<div class="swiper-slide">
                                        <a href="#" class="profileImage-link">
                                            <img src="static/images/fotoProfilo/'. $file .'" id="'. $file .'" alt="Movie Title" class="profilePicker-image">
                                        </a>
                                    </div>';
                            }
                        
                            closedir($handler);
                        }
                        ?>
                    </div>
                    <div class="swiper-button-next navigation-arrow"></div>
                    <div class="swiper-button-prev navigation-arrow"></div>
                </div>
                <input type="hidden" name="profile_picture" id="profile_picture">
            </div>
            <button id="submit" class="confirm-button">Confirm Registration</button>
            <p class="data-field-error" id="registration-error"></p>
        </form>       
    </div>

    <script src="static/js/register.js"></script>
    <script src="static/js/general.js"></script>

    <script src="static/libraries/swiper/package/js/swiper.min.js"></script>
    <script>
        var swiper = new Swiper('.swiper-container', {
        slidesPerView: 4,
        spaceBetween: 15,
        slidesPerGroup: 2,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        });
    </script>
</body>
</html>