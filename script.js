$(document).ready(function() {
    $("#registration-form").submit(function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        
        $.ajax({
            url: "register.php",
            type: "POST",
            data: formData,
            success: function(response) {
                alert(response.message);
                if (response.redirect) {
                    window.location.href = response.redirect;
                }
            },
            error: function() {
                alert("An error occurred during registration.");
            }
        });
    });
    
    $("#login-form").submit(function(e) {
        e.preventDefault();
        
        const loginData = $(this).serialize();
        
        $.ajax({
            url: "login.php",
            type: "POST",
            data: loginData,
            success: function(response) {
                alert(response.message);
                if (response.redirect) {
                    window.location.href = response.redirect;
                }
            },
            error: function() {
                alert("An error occurred during login.");
            }
        });
    });
});
