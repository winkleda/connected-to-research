function register_user() {
    var firstname = document.getElementById('register-firstname').value;
    var lastname = document.getElementById('register-lastname').value;
    var email = document.getElementById('register-email').value;
    var password = document.getElementById('register-password').value;
    
    // /* Recites contents in form */
    // alert("First name: " + firstname + "\n" + "Last name: " + lastname
    //     + "\n" + "Email: " + email + "\n" + "Password: " + password);

    /* Creates array object from form entries */
    //var parm = $("#form-register").serializeArray();

    $.ajax({
        type: 'POST',
        url: 'scripts/register.php',
        data: {name_l: lastname, name_f: firstname, email: email, password: password},
        success: function (response) {
            if (response === 'success') {
                window.location.href = 'index.html';
                return false;
            } else {
                alert(response);
            }
        },
        error: function (error) {
            alert("Your internet needs to be checked. Try again later.");
        }
    });
    return false;
}

function login_user() {
    var email = document.getElementById('login-email').value;
    var password = document.getElementById('login-password').value;
    
    // /* Recites contents in form */
    // alert("Email: " + email + "\n" + "Password: " + password);

    /* Creates array object from form entries */
    //var parm = $("#form-login").serializeArray();

    $.ajax({
        type: 'POST',
        url: 'scripts/login.php',
        data: {email: email, password: password},
        success: function (response) {
            if (response === 'success') {
                window.location.href = 'index.html';
                return false;
            } else {
                alert(response);
            }
        },
        error: function (error) {
            alert("Your internet needs to be checked. Try again later.");
        }
    });
    return false;
}