/*DATA FIELD ANIMATION*/
var inputList = document.querySelectorAll('input');

inputList.forEach(input => {
    input.addEventListener('focus', function(event) {
        event.preventDefault();

        event.target.classList.add('selected');

        var toggleButton = event.target.parentElement.querySelector('.password-visibility-button');
        toggleButton.style.display = 'block';
    });

    input.addEventListener('blur', function(event) {
        event.preventDefault();

        var pwVisib

        var toggleButtonList = event.target.parentElement.querySelectorAll('.password-visibility-button');
        toggleButtonList.forEach(toggleButton => {
            toggleButton.addEventListener('click', function(event) {
                event.preventDefault();
    
                pwVisib = true 
            });
        });
    
        setTimeout(function() {
            if (!pwVisib) {
                event.target.classList.remove('selected');
    
                var toggleButton = event.target.parentElement.querySelector('.password-visibility-button');
                //console.log(toggleButton)
                toggleButton.style.display = 'none';
            }
        }, 200);
    });
});



/*PROFILE FOTO SELECTION*/
const profileImagesList = document.querySelectorAll('.profilePicker-image');
const profileInput = document.getElementById('profile_picture');

profileImagesList.forEach(profileImage => {
    profileImage.addEventListener('click', function(e) {
        e.preventDefault();

        profileImagesList.forEach(profileImage => {
            profileImage.classList.remove('selected')
        });

        profileImage.classList.add('selected');
        profileInput.value = profileImage.id;
    });
});


/*PASSWORD VISIBILITY*/
var pwVisibButton = document.getElementById('pw-visib');
pwVisibButton.addEventListener('click', function(event) {
    event.preventDefault();

    var passwordBox = document.getElementById('password-input');
    var icon = document.querySelector('#pw-visib-icon');

    passwordBox.classList.add('selected');
    passwordBox.focus();

    togglePwVisibility(icon, passwordBox);
});

var confPwVisibButton = document.getElementById('confirm-pw-visib');
confPwVisibButton.addEventListener('click', function(event) {
    event.preventDefault();

    var passwordBox = document.getElementById('confirm-password-input');
    var icon = document.querySelector('#confirm-password-visib-icon');

    passwordBox.classList.add('selected');
    passwordBox.focus();

    togglePwVisibility(icon, passwordBox);
});



function togglePwVisibility(icon, passwordInput) {
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.textContent = "visibility_off";
    } else {
        passwordInput.type = "password";
        icon.textContent = "visibility";
    }    
}



/*FORM VALIDATION*/
var lastError

document.querySelector("#register-form").addEventListener("submit", async (e) => {
    e.preventDefault()
    try {
        const form = e.target
        const formData = new FormData(form)
        const data = Object.fromEntries([...formData.entries()])
        const formdata = new FormData()

        Object.keys(data).map(key => {
            formdata.append(key, data[key])
        })
        const response = await (await fetch(form.action, {
            method: form.method,
            body: formdata
        })).json()
        //console.log(response)
        //console.log(response.fieldError)
        //console.log(response.success)

        var registerSuccessfull = validateForm(response)
        //console.log(registerSuccessfull)

        if (registerSuccessfull) {
            window.location.href = "login.html"
        } else {
            var fieldError = document.getElementById(`${response.fieldError}-error`);
            fieldError.textContent = response.error;
            fieldError.classList.add('active');  

            //console.log(lastError)
            if (lastError !== response.fieldError && lastError !== undefined) {
                var removeError = document.getElementById(`${lastError}-error`);
                //console.log(removeError)
                //console.log(removeError.textContent)
                removeError.textContent = "";
                removeError.classList.remove('active');

                if (lastError === "password" || lastError === "confirm-password") {
                    var inputContainer = document.querySelector(`.${lastError}-field .data-field`);
                    inputContainer.style.marginBottom = "5px"
                }
            }
            lastError = response.fieldError;
            //console.log(lastError)

            if (response.fieldError === "password" || response.fieldError === "confirm-password") {
                var inputContainer = document.querySelector(`.${response.fieldError}-field .data-field`);
                inputContainer.style.marginBottom = "0px"
            }
        }
    } catch (err) {
        console.error(err)
    }
})


function validateForm(response) {
    if (response.success) {
        return true
    } else {
        return false
    }
}