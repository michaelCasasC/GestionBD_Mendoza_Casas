const d = document,
  $email = d.getElementById("email"),
  $password = d.getElementById("password"),
  $submit = d.getElementById("submit");

function handleEstudiante($btn) {
  $email.value = "estudiante@universidad.edu";
  $password.value = "123456";
  login();
}
function handleDocente($btn) {
  $email.value = "administrativo@universidad.edu";
  $password.value = "123456";
  login();
}
function handleAdmin($btn) {
  $email.value = "admin@universidad.edu";
  $password.value = "123456";
  login();
}

function login() {
  $submit.click();
}

d.addEventListener("click", (e) => {
  if (e.target.matches("#btnEstudiante")) {
    handleEstudiante(e.target);
  }
  if (e.target.matches("#btnDocente")) {
    handleDocente(e.target);
  }
  if (e.target.matches("#btnAdmin")) {
    handleAdmin(e.target);
  }
});
