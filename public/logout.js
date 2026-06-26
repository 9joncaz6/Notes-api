// logout.js
// Supprime le token + rôle et renvoie vers la page de login

function logout() {
    localStorage.removeItem("token");
    localStorage.removeItem("role");
    window.location.href = "login.html";
}
