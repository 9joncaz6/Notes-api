// auth.js
// =====================================================
// Protection du front + gestion du rôle utilisateur
// =====================================================

// Récupération du token stocké lors du login
const token = localStorage.getItem("token");

// Si aucun token → l'utilisateur n'est pas connecté → redirection
if (!token) {
    window.location.href = "login.html";
}

// Récupération du rôle stocké lors du login
const role = localStorage.getItem("role");

// =====================================================
// URL de base de l’API (utilisée dans app.js)
// =====================================================
window.API_URL = "http://notes-api.test/notes";

// On expose le token globalement pour app.js
window.AUTH_TOKEN = token;
window.USER_ROLE = role;
