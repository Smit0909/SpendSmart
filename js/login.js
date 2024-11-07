document.getElementById("loginForm").addEventListener("submit", (event) => {
    let email = document.getElementById("email").value;
    let password = document.getElementById("password").value;
    let emptyfield = document.getElementsByClassName("emptyfield")[0];
    let validemail = document.getElementsByClassName("validemail")[0]; 

    let re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    emptyfield.style.display = "none";
    validemail.style.display = "none";

    if (email == "" || password == "") {
        event.preventDefault();
        emptyfield.style.display = "block";
    } else if (!re.test(email)) {
        event.preventDefault();
        validemail.style.display = "block";
    }
});
