//CATEGORY SELECTION
var categoryChooses = document.querySelectorAll('.categories-link');

for (let i = 0; i < categoryChooses.length; i++) {
	let catChooses = categoryChooses[i]
	catChooses.addEventListener("click", function (e) {
		e.preventDefault()

        var categorySelectedName = catChooses.getAttribute('data-category');
        //console.log(categorySelectedName)

        showCategory(categorySelectedName);
    })
}



//RECOMMENDATION CREATE
async function loadRecommendedMovies(userID) {
    try {
        const response = await fetch('/api/api.php/recommend?id=' + userID);
        const data = await response.json();
        return data.payload;
    }
    catch (error) {
        console.log(error);
    }
}



//CARICAMENTO DEI FILM ALLO START
var defaultCategoryLink = document.querySelector('.categories-link.category-selected');
var defaultCategory = defaultCategoryLink.getAttribute('data-category');
showCategory(defaultCategory);


async function showCategory(category) {
    //console.log('Mostra categoria:', category);

    var categorySelectYet = document.querySelector('.categories-link.category-selected')
    categorySelectYet.classList.remove('category-selected', 'active')

    var categorySelectedElement = document.querySelector(`[data-category="${category}"]`)
    categorySelectedElement.classList.add('category-selected', 'active')

    if (category === "forYou") {
        //console.log('forYou')
        var userid = document.querySelector('script[src$="home.js"]').getAttribute('data-userid');
        
        var categoryMovies = await loadRecommendedMovies(userid);
        //console.log(categoryMovies)
    } else {
        var moviesData = [
            { title: "Film 1", category: "upcoming", imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk" },
            { title: "Film 4", category: "upcoming", imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk" },
            { title: "Film 4", category: "upcoming", imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk" },
            { title: "Film 4", category: "upcoming", imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk" },
            { title: "Film 4", category: "upcoming", imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk" },
            { title: "Film 4", category: "popular", imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk" },
            { title: "Film 4", category: "popular", imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk" },
            { title: "Film 4", category: "popular", imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk" },
            { title: "Film 4", category: "popular", imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk" },
            { title: "Film 4", category: "popular", imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk" },
        ];
    
        var categoryMovies = moviesData.filter(function(movie) {
            return movie.category === category;
        });
    }

    var categoryMoviesContainer = document.querySelector('.category-movies');
    categoryMoviesContainer.innerHTML = '';

    categoryMovies.forEach(function(movie, index) {
        for (let i = 0; i < movie.length; i++) {
            const movieCard = movie[i];
            setTimeout(function() {
                var movieElement = createMovieElement(movieCard);

                movieElement.classList.add('animating');

                categoryMoviesContainer.appendChild(movieElement);
            
        }, index * 500);
        }

    });
}


function createMovieElement(movie) {    
    var movieLink = document.createElement('a');
    movieLink.href = '#';
    movieLink.classList.add('movie-mini', 'pos-relative');

    var movieImage = document.createElement('img');
    movieImage.src = movie.poster;
    movieImage.alt = movie.title;
    movieImage.classList.add('movie-mini-image');

    movieLink.appendChild(movieImage);

    return movieLink;
}















/*// Assicurati che questa funzione venga chiamata quando l'utente si logga con successo
function handleLoginSuccess(sessionID) {
    // Controlla se l'ID di sessione è disponibile
    if (sessionID) {
        // Carica i film consigliati per l'utente loggato
        loadRecommendedMovies(sessionID);

        // Aggiungi la categoria "For You" al menu delle categorie
        addForYouCategory();
    }
}

// Aggiunge la categoria "For You" al menu delle categorie
function addForYouCategory() {
    // Aggiungi il codice per creare il link della categoria "For You" nel menu delle categorie
    // Ad esempio:
    var categoriesMenu = document.querySelector('.categories-menu');
    var forYouCategoryLink = document.createElement('a');
    forYouCategoryLink.textContent = 'For You';
    forYouCategoryLink.href = '#';
    forYouCategoryLink.dataset.category = 'for-you'; // Utilizza il dataset per associare la categoria
    forYouCategoryLink.classList.add('categories-link', 'category-select', 'active');
    categoriesMenu.appendChild(forYouCategoryLink);
}

// Assicurati di chiamare questa funzione quando l'utente si logga con successo
handleLoginSuccess(sessionID); // Passa l'ID di sessione alla funzione handleLoginSuccess



async function getSessionID() {
    try {
        // Effettua una richiesta AJAX per ottenere l'ID di sessione dal server
        const response = await fetch('static/php/get-session-id.php');
        const data = await response.json();
        return data.sessionID;
    } catch (error) {
        throw new Error('Errore durante il recupero dell\'ID di sessione');
    }
}
*/




/*if (category === "forYou") {
        //console.log('forYou')
        var sessionID = getSessionID()
            if (sessionID) {
                console.log('ID di sessione disponibile:', sessionID);
                loadRecommendedMovies(sessionID);
            } else {
                // Se l'ID di sessione non è disponibile, mostra un messaggio o esegui altre azioni necessarie
                //showLoginMessage();
                console.log('ID di sessione non disponibile');
            }
    } else {
        var moviesData = [
            { title: "Film 1", category: "upcoming", imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk" },
            { title: "Film 4", category: "upcoming", imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk" },
            { title: "Film 4", category: "upcoming", imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk" },
            { title: "Film 4", category: "upcoming", imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk" },
            { title: "Film 4", category: "upcoming", imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk" },
            { title: "Film 4", category: "popular", imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk" },
            { title: "Film 4", category: "popular", imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk" },
            { title: "Film 4", category: "popular", imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk" },
            { title: "Film 4", category: "popular", imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk" },
            { title: "Film 4", category: "popular", imageUrl: "https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk" },
        ];
    
        var categoryMovies = moviesData.filter(function(movie) {
            return movie.category === category;
        });
    }


    var categoryMoviesContainer = document.querySelector('.category-movies');
    categoryMoviesContainer.innerHTML = '';

    categoryMovies.forEach(function(movie, index) {
        setTimeout(function() {
            var movieElement = createMovieElement(movie);

            movieElement.classList.add('animating');

            categoryMoviesContainer.appendChild(movieElement);
        }, index * 500);
    });*/



//IDEA PER LA CATEGORA "FOR YOU" QUANDO L'UTENTE NON E' LOGGATO

/*if (categorySelectedElement.dataset.category === "for-you") {
    // Controlla se l'utente è loggato e l'ID di sessione è disponibile
    if (isLoggedIn()) {
        // Se l'utente è loggato, ma l'ID di sessione non è disponibile, potresti mostrare un messaggio
        showLoginMessage();
    } else {
        // Se l'utente non è loggato, puoi semplicemente lasciare vuota la sezione "For You"
        clearCategoryMovies();
    }
} else {
    // Carica i film per altre categorie
    var moviesData = [
        // Inserisci qui i film per altre categorie
    ];*/