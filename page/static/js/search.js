/*BACK BUTTON*/
var backButton = document.querySelector('.back-button');

backButton.addEventListener('click', function() {
    window.history.back();
});



/*SEARCHING*/
var inputElement = document.querySelector(".search-bar");
var filmList = document.querySelector(".film-container");

var moviesList = [];


fetchMovies();
//var start = true


async function fetchMovies() {
    var movies = await doFetch("/api/api.php/movies");
    //console.log(movies);
    moviesList = movies;
    if (movies) {
        generateFilmCards(movies); 
    }
}


async function doFetch(path) {
    try {
        const response = await fetch(path);
        return await thenCallback(response);
    } catch (error) {
        catchCallback(error);
    }
}

async function thenCallback(response) {
    if (response.status === 200) {
        const data = await response.json();
        return data.payload;
    } else {
        throw new Error(`HTTP error! Status: ${response.status}`);
    }
}

function catchCallback(error) {
    console.log(error)
    //error = ""
    //document.querySelector(".sentenceBox").innerHTML = error
}



//RICERCA RISPETTO ALL'INPUT UTENTE
inputElement.addEventListener("input", handleInput);
function handleInput(event) {
    clearFilmCards();

    var inputValue = event.target.value.toLowerCase();
    //console.log("Input Value: " + inputValue);

    var filteredFilms = moviesList.filter(film => film.title.toLowerCase().includes(inputValue));

    if (filteredFilms.length === 0) {
        failedSearch();
    } else {
        generateFilmCards(filteredFilms);
    }
    //searching();
}



//CARD FUNCTIONS
function clearFilmCards() {
    var filmListElement = document.querySelector(".film-container");

    filmListElement.innerHTML = '';
}

function failedSearch() {
    var noFilm = document.createElement('div');
    noFilm.classList.add('failed-search', 'flex');

    filmList.appendChild(noFilm);


    var noFilmImage = document.createElement('img');
    noFilmImage.src = "/page/static/images/failedSearch.png";
    noFilmImage.alt = "No film found";
    noFilmImage.classList.add('failed-search-image');

    noFilm.appendChild(noFilmImage);


    var noFilmText = document.createElement('h3');
    noFilmText.textContent = "we are sorry, we can not find the movie :(";
    noFilmText.classList.add('failed-search-text');

    noFilm.appendChild(noFilmText);
}



function generateFilmCards(films) {
    var filmListElement = document.querySelector(".film-container");

    //RANDOM GENERATION
    var max = films.length;
    var randomMovieIDs = [];

    for(var y = 0; y < max; y++){
        var temp = Math.floor(Math.random()*max);
        if(randomMovieIDs.indexOf(temp) == -1){
            randomMovieIDs.push(temp);
        }
        else
        y--;
    }
    
    if (films.length > 4) { 
        for (let i = 0; i < 4; i++) {
            var randomMovieID = randomMovieIDs[i];
            //console.log(randomMovieID)
    
            const filmCard = films[randomMovieID];
            var filmElement = createFilmCard(filmCard);
    
            filmListElement.appendChild(filmElement);
        }
    } else {
        for (let i = 0; i < films.length; i++) {
            const filmCard = films[i];
            var filmElement = createFilmCard(filmCard);
    
            filmListElement.appendChild(filmElement);
        }
    }
    
    /*films.forEach(film => {
        var filmCard = createFilmCard(film);

        filmListElement.appendChild(filmCard);
    });*/
}


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








/*function finalCallback(data) {
    //console.log(data.payload)
    //console.log(data.value)

    return (data.payload);
    //generateFilmCards(data.payload);
}

//FILM LIST AT LOADED
/*searching();


function searching() {
    if (inputElement.value === "") {
        path = "/api/api.php/movies";
    } else {
        path = "/api/api.php/movies?title=" + inputElement.value;
    }
    doFetch(path);
}*/