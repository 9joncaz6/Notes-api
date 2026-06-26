// login.js
// Gère la connexion utilisateur + stockage du token + redirection

const API_URL = "http://notes-api.test"; // ton domaine Laragon

const form = document.getElementById("loginForm");
const errorBox = document.getElementById("error");

form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    try {
        const res = await fetch(`${API_URL}/login`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ email, password })
        });

        const data = await res.json();

        if (!res.ok) {
            errorBox.textContent = data.error || "Erreur inconnue";
            errorBox.classList.remove("d-none");
            return;
        }

        // Stockage du token + rôle
        localStorage.setItem("token", data.token);
        localStorage.setItem("role", data.role);

        // Redirection vers l'application
        window.location.href = "index.html";

    } catch (err) {
        errorBox.textContent = "Impossible de contacter le serveur";
        errorBox.classList.remove("d-none");
    }
});
