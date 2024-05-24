<?php
session_start();

if(isset($_SESSION['userID']) && isset($_SESSION['username'])) {
?>

        <div class="welcome-message">Hallå <?php echo $_SESSION['username'] ?>!</div>
        <li><a href="logout.php" class="log-out-btn">Sign Out</a></li>

<?php
} else {
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login/Register</title>
        <link rel="stylesheet" href="style.css">
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </head>

    <body>
        <button class="login-module-btn">Login Here!</button>
        <button class="register-module-btn">Sign Up Here!</button>

        <div class="login-module" id="login-module-id">
            <div class="module-content">
                <span class="login-close-btn">&times;</span>
                <form action="login.php" method="POST" class="login-form" id="login-form-id">
                    <div class="form-validation">
                        <input type="text" class="module-input" id="login-username" name="username" placeholder="Enter your username">
                        <p>Error Message</p>
                    </div>
                    <div class="form-validation">
                        <input type="password" class="module-input" id="login-password" name="password" placeholder="Enter your password">
                        <p>Error Message</p>
                    </div>
                    <input type="submit" class="login-input-btn" value="Login">
                </form>
            </div>
        </div>

        <div class="register-module" id="register-module-id">
            <div class="module-content">
                <span class="register-close-btn">&times;</span>
                <form action="register.php" method="POST" class="register-form" id="register-form-id">
                    <div class="form-validation">
                        <input type="text" class="module-input" id="register-username" name="username" placeholder="Enter your username">
                        <p>Error Message</p>
                    </div>
                    <div class="form-validation">
                        <input type="text" class="module-input" id="register-email" name="email" placeholder="Enter your email">
                        <p>Error Message</p>
                    </div>
                    <div class="form-validation">
                        <input type="password" class="module-input" id="register-password" name="password" placeholder="Enter your password">
                        <p>Error Message</p>
                    </div>
                    <div class="form-validation">
                        <input type="password" class="module-input" id="register-password-conf" name="password-confirm" placeholder="Confirm your password">
                        <p>Error Message</p>
                    </div>
                    <div class="g-recaptcha" data-sitekey="6LcZyuMpAAAAADWQg9RTL8u7OUc_BGQuZBN6CdsR"></div>
                    <input type="submit" class="register-input-btn" value="Sign Up">
                </form>
            </div>
        </div>

        <script>

            //Module-utility
            const loginModule = document.getElementById('login-module-id')
            const openLogin = document.querySelector('.login-module-btn')
            const closeLogin = document.querySelector('.login-close-btn')
            const registerModule = document.getElementById('register-module-id')
            const openRegister = document.querySelector('.register-module-btn')
            const closeRegister = document.querySelector('.register-close-btn')


            openLogin.addEventListener('click', (e) => {
                loginModule.style.display = 'block'
            })

            closeLogin.addEventListener('click', (e) => {
                loginModule.style.display = 'none'
            })

            openRegister.addEventListener('click', (e) => {
                registerModule.style.display = 'block'
            })

            closeRegister.addEventListener('click', (e) => {
                registerModule.style.display = 'none'
            })

            window.addEventListener('click', (e) => {
                if (e.target === loginModule || e.target === registerModule) {
                    loginModule.style.display = 'none'
                    registerModule.style.display = 'none'
                }
            })

            //Validerar input
            const loginForm = document.getElementById('login-module-id')
            const registerForm = document.getElementById('register-module-id')
            const usernameInput = document.getElementById('register-username');
            const emailInput = document.getElementById('register-email');
            const passwordInput = document.getElementById('register-password');
            const passwordConfInput = document.getElementById('register-password-conf');
            const loginUsernameInput = document.getElementById('login-username');
            const loginPasswordInput = document.getElementById('login-password');

            function showError(input, message) {
                const formValidation = input.parentElement;
                formValidation.className = 'form-validation error';
            
                const errorMessage = formValidation.querySelector('p');
                errorMessage.innerText = message;
            }
            
            function showValid(input){
                const formValidation = input.parentElement;
                formValidation.className = 'form-validation valid';
            }
            
            function checkRequired(inputArr){
                inputArr.forEach(function(input){
                    if(input.value.trim() === '') {
                        showError(input, `${getFieldName(input)} is required`);
                    } else {
                        showValid(input);
                    }
                })
            }
            
            function checkEmail(input) {
                const emailRegex = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/;
                if(!emailRegex.test(input.value)){
                    showError(input, "Invalid email");
                } else {
                    showValid(input);
                }
            }
            
            function checkPassword(pass, conf) {
                const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/;
                if(!passwordRegex.test(pass.value)) {
                    showError(pass, "Invalid password");
                } else {
                    showValid(pass);
                }
            
                if(pass.value !== conf.value) {
                    showError(conf, "Passwords does not match");
                } else if (conf.value.length > 0) {
                    showValid(conf);
                } else {
                    showError(conf, "Password confirmation is required");
                }
            }
            
            function getFieldName (input){
                return input.name.charAt(0).toUpperCase() + input.name.slice(1);  
            }
            registerForm.addEventListener('submit', (e) => {
                let hasErrors = false;

                checkRequired([usernameInput, emailInput, passwordInput, passwordConfInput]);
                checkEmail(emailInput);
                checkPassword(passwordInput, passwordConfInput);

                const errorElements = document.querySelectorAll('.form-validation.error');
                if (errorElements.length > 0) {
                    hasErrors = true;
                }

                if (hasErrors) {
                    e.preventDefault();
                }
                
            })

            loginForm.addEventListener('submit', (e) => {
                let hasErrors = false;

                checkRequired([loginUsernameInput, loginPasswordInput]);

                const errorElements = document.querySelectorAll('.form-validation.error');
                if (errorElements.length > 0) {
                    hasErrors = true;
                }

                if (hasErrors) {
                    e.preventDefault();
                }
                
            })

            //AJAX requests
            usernameInput.addEventListener('input', (e) => {
                const username = usernameInput.value.trim();
                if (username !== '') {
                    checkUsernameAvailability(username)
                } else {
                    showError(usernameInput, "Username is required")
                }
            })

            emailInput.addEventListener('input', (e) => {
                const email = emailInput.value.trim();
                if (email !== '') {
                    checkEmailAvailability(email, emailInput)
                } else {
                    showError(emailInput, "Email is required")
                }
            })

            function checkUsernameAvailability(username) {
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'check_username.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            if (response.status === 'error') {
                                showError(usernameInput, response.message);
                            } else {
                                showValid(usernameInput);
                            }
                        }
                    };
                    xhr.send(`username=${encodeURIComponent(username)}`);
                }

            function checkEmailAvailability(email) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'check_email.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.status === 'error') {
                            showError(emailInput, response.message);
                        } else {
                            showValid(emailInput);
                        }
                    }
                };
                xhr.send(`email=${encodeURIComponent(email)}`);
            }
        </script>
    </body>
    </html>
<?php
}
?>