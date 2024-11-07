document.getElementById("PasswordForm").addEventListener("submit", (event) => {
  let password = document.getElementById("password").value;
  let cpassword = document.getElementById("confirm-password").value;

  let emptyfield = document.getElementsByClassName("emptyfield")[0];
  let samePassword = document.getElementsByClassName("samePassword")[0];

  emptyfield.style.display = "none";
  samePassword.style.display = "none";

  if (password === "" || cpassword === "") {
    event.preventDefault();
    emptyfield.style.display = "block";
  } else if (password != cpassword) {
    event.preventDefault();
    samePassword.style.display = "block";
  }
});
