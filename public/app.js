// app.js
// =====================================================
// Script principal de l'application Notes
// =====================================================

// Variables globales
let selectedNoteId = null;   // ID de la note sélectionnée
let allNotes = [];           // Toutes les notes chargées depuis l’API



// =====================================================
// Fonction principale : chargement + affichage des notes
// =====================================================
async function loadNotes(fetchFromApi = true) {

    // Si demandé → on recharge depuis l’API
    if (fetchFromApi) {
        const res = await fetch(window.API_URL, {
            headers: {
                "Authorization": window.AUTH_TOKEN
            }
        });

        const json = await res.json();
        allNotes = json.data;
    }

    // Conteneur principal
    const container = document.getElementById("notes");
    container.innerHTML = "";

    // Grille Bootstrap
    const row = document.createElement("div");
    row.className = "row g-3";

    // Affichage de chaque note
    allNotes.forEach(note => {

        const col = document.createElement("div");
        col.className = "col-md-4";

        const card = document.createElement("div");
        card.className = "card p-3 shadow-sm h-100";
        card.style.cursor = "pointer";

        // Style si sélectionnée
        if (note._id === selectedNoteId) {
            card.classList.add("note-selected");
        }

        // Clic → sélection + offcanvas
        card.addEventListener("click", () => {
            selectedNoteId = note._id;
            openNoteCanvas(note);
            loadNotes(false);
        });

        // Titre
        const h3 = document.createElement("h3");
        h3.className = "h6 text-primary";
        h3.textContent = note.title;
        card.appendChild(h3);

        // Aperçu contenu
        const p = document.createElement("p");
        p.textContent = note.content.substring(0, 80) + "...";
        card.appendChild(p);

        // Boutons
        const btnRow = document.createElement("div");
        btnRow.className = "mt-3 d-flex justify-content-between";

        // Modifier (ADMIN ONLY)
        const editBtn = document.createElement("button");
        editBtn.textContent = "Modifier";
        editBtn.className = "btn btn-warning btn-sm admin-only";
        editBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            openEditModal(note);
        });

        // Supprimer (ADMIN ONLY)
        const deleteBtn = document.createElement("button");
        deleteBtn.textContent = "Supprimer";
        deleteBtn.className = "btn btn-danger btn-sm admin-only";
        deleteBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            openDeleteModal(note._id);
        });

        btnRow.appendChild(editBtn);
        btnRow.appendChild(deleteBtn);
        card.appendChild(btnRow);

        col.appendChild(card);
        row.appendChild(col);
    });

    updateNoteFilter();
    container.appendChild(row);

    // Masquer les boutons selon le rôle
    applyRoleVisibility();
}



// =====================================================
// Offcanvas : affichage d’une note
// =====================================================
function openNoteCanvas(note) {
    document.getElementById("noteCanvasTitle").textContent = note.title;
    document.getElementById("noteCanvasContent").textContent = note.content;

    const offcanvas = new bootstrap.Offcanvas(document.getElementById("noteCanvas"));
    offcanvas.show();
}



// =====================================================
// Création d’une note
// =====================================================
document.getElementById("createForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const title = document.getElementById("title").value;
    const content = document.getElementById("content").value;

    await fetch(window.API_URL, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Authorization": window.AUTH_TOKEN
        },
        body: JSON.stringify({ title, content })
    });

    document.getElementById("createForm").reset();
    loadNotes();
});



// =====================================================
// Suppression d’une note
// =====================================================
async function deleteNote(id) {
    await fetch(`${window.API_URL}/${id}`, {
        method: "DELETE",
        headers: {
            "Authorization": window.AUTH_TOKEN
        }
    });

    loadNotes();
}



// =====================================================
// Modale d’édition
// =====================================================
function openEditModal(note) {
    document.getElementById("editId").value = note._id;
    document.getElementById("editTitle").value = note.title;
    document.getElementById("editContent").value = note.content;

    const modal = new bootstrap.Modal(document.getElementById("editModal"));
    modal.show();
}



// =====================================================
// Sauvegarde des modifications
// =====================================================
document.getElementById("saveEditBtn").addEventListener("click", async () => {
    const id = document.getElementById("editId").value;
    const title = document.getElementById("editTitle").value;
    const content = document.getElementById("editContent").value;

    await fetch(`${window.API_URL}/${id}`, {
        method: "PATCH",
        headers: {
            "Content-Type": "application/json",
            "Authorization": window.AUTH_TOKEN
        },
        body: JSON.stringify({ title, content })
    });

    bootstrap.Modal.getInstance(document.getElementById("editModal")).hide();
    loadNotes();
});



// =====================================================
// Modale de suppression
// =====================================================
function openDeleteModal(id) {
    document.getElementById("deleteId").value = id;

    const modal = new bootstrap.Modal(document.getElementById("deleteModal"));
    modal.show();
}

document.getElementById("confirmDeleteBtn").addEventListener("click", async () => {
    const id = document.getElementById("deleteId").value;

    await fetch(`${window.API_URL}/${id}`, {
        method: "DELETE",
        headers: {
            "Authorization": window.AUTH_TOKEN
        }
    });

    bootstrap.Modal.getInstance(document.getElementById("deleteModal")).hide();
    loadNotes();
});



// =====================================================
// Filtre par titre
// =====================================================
function updateNoteFilter() {
    const select = document.getElementById("noteFilter");
    select.innerHTML = "";

    const defaultOption = document.createElement("option");
    defaultOption.value = "";
    defaultOption.textContent = "-- Toutes les notes --";
    select.appendChild(defaultOption);

    const sortedNotes = [...allNotes].sort((a, b) =>
        a.title.localeCompare(b.title)
    );

    sortedNotes.forEach(note => {
        const option = document.createElement("option");
        option.value = note._id;
        option.textContent = note.title;
        select.appendChild(option);
    });
}

document.getElementById("noteFilter").addEventListener("change", () => {
    const selectedId = document.getElementById("noteFilter").value;

    if (selectedId === "") {
        selectedNoteId = null;
        loadNotes();
        return;
    }

    selectedNoteId = selectedId;
    const note = allNotes.find(n => n._id === selectedId);

    loadNotes(false);
    openNoteCanvas(note);
});



// =====================================================
// Tri alphabétique
// =====================================================
document.getElementById("sortBtn").addEventListener("click", () => {
    allNotes.sort((a, b) => a.title.localeCompare(b.title));
    loadNotes(false);
});



function applyRoleVisibility() {
    const role = localStorage.getItem("role");

    if (role !== "admin") {
        document.querySelectorAll(".admin-only").forEach(el => {
            el.style.display = "none";
        });
    }
}





// =====================================================
// Chargement initial
// =====================================================
loadNotes();
