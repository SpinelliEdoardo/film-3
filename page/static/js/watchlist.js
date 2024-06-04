/*BACK BUTTON*/
var backButton = document.querySelector('.back-button');

backButton.addEventListener('click', function() {
    window.history.back();
});



//WATCHLIST CREATE
async function loadWatchList(userID) {
    try {
        const response = await fetch('/api/api.php/watchlist?id=' + userID);
        const data = await response.json();

        watchMovies = data.payload;
        return watchMovies;
    }
    catch (error) {
        console.log(error);
    }
}



/*WATCH LIST FILLING*/
var watchlistContainer = document.querySelector('.watch-film-container');
var userid = document.querySelector('script[src$="watchlist.js"]').getAttribute('data-userid');

if (userid === "null") {
    failedSearch("not logged");
} else {
    fillWatchList();
}



async function fillWatchList() {
    var watchMovies

    watchMovies = await loadWatchList(userid);
    //console.log(watchMovies)
    
    watchMovies.forEach(function(film) {
        watchlistContainer.appendChild(createFilmCard(film));
    });
}


function failedSearch(status) {
    var noFilm = document.createElement('div');
    noFilm.classList.add('failed-search', 'flex');

    watchlistContainer.appendChild(noFilm);


    var noFilmImage = document.createElement('img');
    noFilmImage.src = "/page/static/images/failedSearch.png";
    noFilmImage.alt = "No film found";
    noFilmImage.classList.add('failed-search-image');

    noFilm.appendChild(noFilmImage);


    var noFilmText = document.createElement('h3');
    if (status === "not logged") {
        noFilmText.innerHTML = "You are not logged in!<br>Please log in to see your watchlist.";
    } else if (status === "watchlist empty") {
        noFilmText.textContent = "Watchlist is empty. Start adding films!";
    }
    noFilmText.classList.add('failed-search-text');

    noFilm.appendChild(noFilmText);


    var loginButton = document.createElement('a');
    loginButton.href = 'login.html';
    loginButton.textContent = "Login";
    loginButton.classList.add('login-button', 'link');

    noFilm.appendChild(loginButton);
}



//FILM CARD CREATE 
function createFilmCard(film) {
    var filmCard = document.createElement('a');
    filmCard.href = '#';
    filmCard.classList.add('film-card', 'flex', 'link');

    var filmImage = document.createElement('img');
    filmImage.src = film.poster;
    filmImage.alt = film.title;
    filmImage.classList.add('film-card-image');

    var filmInfo = document.createElement('div');
    filmInfo.classList.add('film-card-information', 'flex');

    var filmTitle = document.createElement('h3');
    filmTitle.textContent = film.title;
    filmTitle.classList.add('film-card-title');

    var filmDetails = document.createElement('div');
    filmDetails.classList.add('film-card-details', 'flex');

    filmDetails.appendChild(createFilmDetail('star', film.rating, ['rating-style']));
    filmDetails.appendChild(createFilmDetail('confirmation_number', film.genres));
    filmDetails.appendChild(createFilmDetail('calendar_today', film.released_year));
    filmDetails.appendChild(createFilmDetail('schedule', `${film.duration}min`));

    filmInfo.appendChild(filmTitle);
    filmInfo.appendChild(filmDetails);

    filmCard.appendChild(filmImage);
    filmCard.appendChild(filmInfo);

    return filmCard;
}
function createFilmDetail(iconName, text, additionalClasses = []) {
    if (additionalClasses.length > 0) {
        var detailDiv = document.createElement('div');
        detailDiv.classList.add('film-card-rating', 'flex');
    } else {
        var detailDiv = document.createElement('div');
        detailDiv.classList.add('film-card-info', 'flex');
    }

    var icon = document.createElement('i');
    icon.textContent = iconName;
    icon.classList.add('material-icons', 'information-icon');
    if (additionalClasses.length > 0) {
        icon.classList.add(...additionalClasses);
    }

    var iconText = document.createElement('h4');
    if (typeof text === 'object') {
        const detailDiv = text.map(detail => detail.name)    
        const detail = detailDiv.join(', ');
        
        iconText.textContent = detail;
    } else {
        iconText.textContent = text;
    }
    iconText.classList.add('information-text');

    if (additionalClasses.length > 0) {
        iconText.classList.add(...additionalClasses);
    }
    

    detailDiv.appendChild(icon);
    detailDiv.appendChild(iconText);

    return detailDiv;
}



















/*var films = [
    {
        title: "Fight Club",
        imageUrl: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQfj-Xxr1DlcuFjU4Nj0ZHm2rmEn0e7BBU0xQZzQedaWODnFw7Q",
        rating: "9.5",
        genre: "Action",
        year: "1999",
        duration: "139min"
    },
    {
        title: "Inception",
        imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk",
        rating: "8.8",
        genre: "Sci-Fi",
        year: "2010",
        duration: "148min"
    },
    {
        title: "The Matrix",
        imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk",
        rating: "8.7",
        genre: "Action",
        year: "1999",
        duration: "136min"
    },
    {
        title: "Pulp Fiction",
        imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk",
        rating: "8.9",
        genre: "Crime",
        year: "1994",
        duration: "154min"
    },
    {
        title: "The Dark Knight",
        imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk",
        rating: "9.0",
        genre: "Action",
        year: "2008",
        duration: "152min"
    }
];*/