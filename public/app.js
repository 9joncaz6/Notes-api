// URL de base de l’API
const API_URL = "http://notes-api.test/notes";

// Variables globales utilisées dans toute l’application
let selectedNoteId = null;   // ID de la note actuellement sélectionnée (pour le style + offcanvas)
let allNotes = [];           // Tableau contenant toutes les notes chargées depuis l’API


/**
 * Fonction principale d’affichage des notes.
 * Paramètre fetchFromApi :
 *   - true  → recharge les données depuis l’API
 *   - false → réutilise les données déjà présentes dans allNotes
 */
async function loadNotes(fetchFromApi = true) {

    // Si demandé, on recharge les données depuis l’API
    if (fetchFromApi) {
        const res = await fetch(API_URL);
        const json = await res.json();
        allNotes = json.data; // On stocke toutes les notes dans la variable globale
    }

    // Sélection du conteneur et réinitialisation
    const container = document.getElementById("notes");
    container.innerHTML = "";

    // Grille Bootstrap
    const row = document.createElement("div");
    row.className = "row g-3";

    // Affichage de chaque note
    allNotes.forEach(note => {

        // Colonne Bootstrap (3 par ligne)
        const col = document.createElement("div");
        col.className = "col-md-4";

        // Card contenant la note
        const card = document.createElement("div");
        card.className = "card p-3 shadow-sm h-100";
        card.style.cursor = "pointer";

        // Style spécial si la note est sélectionnée
        if (note._id === selectedNoteId) {
            card.classList.add("note-selected");
        }

        // Clic sur la card → sélection + ouverture du offcanvas
        card.addEventListener("click", () => {
            selectedNoteId = note._id;
            openNoteCanvas(note);
            loadNotes(false); // Re-render sans recharger depuis l’API
        });

        // Titre de la note
        const h3 = document.createElement("h3");
        h3.className = "h6 text-primary";
        h3.textContent = note.title;
        card.appendChild(h3);

        // Aperçu du contenu (80 caractères max)
        const p = document.createElement("p");
        p.textContent = note.content.substring(0, 80) + "...";
        card.appendChild(p);

        // Ligne contenant les boutons Modifier / Supprimer
        const btnRow = document.createElement("div");
        btnRow.className = "mt-3 d-flex justify-content-between";

        // Bouton Modifier → ouvre la modale d’édition
        const editBtn = document.createElement("button");
        editBtn.textContent = "Modifier";
        editBtn.className = "btn btn-warning btn-sm";
        editBtn.addEventListener("click", (e) => {
            e.stopPropagation(); // Empêche l’ouverture du offcanvas
            openEditModal(note);
        });

        // Bouton Supprimer → ouvre la modale de confirmation
        const deleteBtn = document.createElement("button");
        deleteBtn.textContent = "Supprimer";
        deleteBtn.className = "btn btn-danger btn-sm";
        deleteBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            openDeleteModal(note._id);
        });

        // Ajout des boutons à la card
        btnRow.appendChild(editBtn);
        btnRow.appendChild(deleteBtn);
        card.appendChild(btnRow);

        // Ajout de la card à la grille
        col.appendChild(card);
        row.appendChild(col);
    });

    // Mise à jour de la liste déroulante (recherche par titre)
    updateNoteFilter();

    // Ajout de la grille au conteneur
    container.appendChild(row);
}




// Affiche une note dans l’offcanvas latéral
 
function openNoteCanvas(note) {
    document.getElementById("noteCanvasTitle").textContent = note.title;
    document.getElementById("noteCanvasContent").textContent = note.content;

    const offcanvas = new bootstrap.Offcanvas(document.getElementById("noteCanvas"));
    offcanvas.show();
}




// Création d’une nouvelle note

document.getElementById("createForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const title = document.getElementById("title").value;
    const content = document.getElementById("content").value;

    await fetch(API_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ title, content })
    });

    document.getElementById("createForm").reset();
    loadNotes(); // Recharge depuis l’API
});




// Suppression d’une note (appelée depuis la modale)
 
async function deleteNote(id) {
    await fetch(`${API_URL}/${id}`, { method: "DELETE" });
    loadNotes(); // Recharge depuis l’API
}




// Ouvre la modale d’édition avec les données pré-remplies

function openEditModal(note) {
    document.getElementById("editId").value = note._id;
    document.getElementById("editTitle").value = note.title;
    document.getElementById("editContent").value = note.content;

    const modal = new bootstrap.Modal(document.getElementById("editModal"));
    modal.show();
}




// Sauvegarde des modifications d’une note
 
document.getElementById("saveEditBtn").addEventListener("click", async () => {
    const id = document.getElementById("editId").value;
    const title = document.getElementById("editTitle").value;
    const content = document.getElementById("editContent").value;

    await fetch(`${API_URL}/${id}`, {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ title, content })
    });

    bootstrap.Modal.getInstance(document.getElementById("editModal")).hide();
    loadNotes(); // Recharge depuis l’API
});




// Ouvre la modale de confirmation de suppression

function openDeleteModal(id) {
    document.getElementById("deleteId").value = id;

    const modal = new bootstrap.Modal(document.getElementById("deleteModal"));
    modal.show();
}




// Confirmation de suppression
 
document.getElementById("confirmDeleteBtn").addEventListener("click", async () => {
    const id = document.getElementById("deleteId").value;

    await fetch(`${API_URL}/${id}`, { method: "DELETE" });

    bootstrap.Modal.getInstance(document.getElementById("deleteModal")).hide();
    loadNotes(); // Recharge depuis l’API
});




// Met à jour la liste déroulante contenant les titres des notes
 
function updateNoteFilter() {
    const select = document.getElementById("noteFilter");
    select.innerHTML = "";

    // Option par défaut
    const defaultOption = document.createElement("option");
    defaultOption.value = "";
    defaultOption.textContent = "-- Toutes les notes --";
    select.appendChild(defaultOption);

    // Tri alphabétique pour un affichage propre
    const sortedNotes = [...allNotes].sort((a, b) =>
        a.title.localeCompare(b.title)
    );

    // Ajout d’une option par note
    sortedNotes.forEach(note => {
        const option = document.createElement("option");
        option.value = note._id;
        option.textContent = note.title;
        select.appendChild(option);
    });
}




 // Filtrage par titre via la liste déroulante

document.getElementById("noteFilter").addEventListener("change", () => {
    const selectedId = document.getElementById("noteFilter").value;

    // Affichage de toutes les notes
    if (selectedId === "") {
        selectedNoteId = null;
        loadNotes(); // Recharge depuis l’API
        return;
    }

    // Sélection d’une note précise
    selectedNoteId = selectedId;

    const note = allNotes.find(n => n._id === selectedId);

    loadNotes(false); // Re-render sans refetch
    openNoteCanvas(note);
});



//Tri alphabétique A → Z
document.getElementById("sortBtn").addEventListener("click", () => {
    allNotes.sort((a, b) => a.title.localeCompare(b.title));
    loadNotes(false); // Re-render sans refetch
});



// Chargement initial des notes
loadNotes();
