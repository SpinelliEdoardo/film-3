/*CHECK LOGIN STATUS*/
/*checkAuthStatus();

function checkAuthStatus() {
    fetch('static/php/status-login.php')
        .then(response => response.json())
        .then(data => {
            //console.log("Auth status:", data);
            const profilePic = document.querySelector('.profile-img');
            const accountIcon = document.querySelector('.login-icon')
            if (data.loggedIn) {  
                profilePic.src = 'https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcTKNvN1d8BSJPWenCvCOx2oOTDYqBSzjLkuDplC6Iw89KZONqnk';

                accountIcon.classList.add('disactive');
                profilePic.style.display = 'block';            
            } else if (!data.loggedIn) {
                accountIcon.classList.remove('disactive');
                profilePic.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error fetching authentication status:', error);
        });
}
*/