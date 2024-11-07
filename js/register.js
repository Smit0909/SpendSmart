document.getElementById("signUpForm").addEventListener("submit", (event) => {
  let username = document.getElementById("username").value;
  let email = document.getElementById("email").value;
  let password = document.getElementById("password").value;
  let cpassword = document.getElementById("confirm-password").value;

  let emptyfield = document.getElementsByClassName("emptyfield")[0];
  let validemail = document.getElementsByClassName("validemail")[0];
  let samePassword = document.getElementsByClassName("samePassword")[0];

  let re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  emptyfield.style.display = "none";
  validemail.style.display = "none";
  samePassword.style.display = "none";

  if (username === "" || email === "" || password === "" || cpassword === "") {
    event.preventDefault();
    emptyfield.style.display = "block";
  } else if (!re.test(email)) {
    event.preventDefault();
    validemail.style.display = "block";
  } else if (password != cpassword) {
    event.preventDefault();
    samePassword.style.display = "block";
  }
});
