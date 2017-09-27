function User(){
    return {
        xhr: new XMLHttpRequest(),
        register: function(){
            userDetails = {
                "username": document.getElementById("username").value,
                "password": document.getElementById("password").value,
                "name": document.getElementById("name").value,
                "email": document.getElementById("email").value,
                "phone": document.getElementById("phone").value
            }
            alert("Register");
            console.log(userDetails);
            window.location.href = "http://localhost/Pitcher/enter-details.php";
        },
        login: function(){
            alert("Login");
        }
    };
}

user = new User();