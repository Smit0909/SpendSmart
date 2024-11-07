document.getElementById("fpForm").addEventListener("submit", (event) => {
  let emptyfield = document.getElementsByClassName("emptyfield")[0];

  emptyfield.style.display = "none";

  if (email == "") {
    event.preventDefault();
    emptyfield.style.display = "block";
  }
});
