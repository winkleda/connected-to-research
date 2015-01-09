function register_user() {
    var firstname = document.getElementById('register-firstname').value;
    var lastname = document.getElementById('register-lastname').value;
    var email = document.getElementById('register-email').value;
    var password = document.getElementById('register-password').value;
    
    /* Recites contents in form */
    alert("First name: " + firstname + "\n" + "Last name: " + lastname
        + "\n" + "Email: " + email + "\n" + "Password: " + password);

    window.location.href = "index.html";
}

function login_user() {
    var email = document.getElementById('login-email').value;
    var password = document.getElementById('login-password').value;
    
    /* Recites contents in form */
    alert("Email: " + email + "\n" + "Password: " + password);

    window.location.href = "index.html";
}