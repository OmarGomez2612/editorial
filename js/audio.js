document.addEventListener('DOMContentLoaded', function() {
    const audioPlayer = document.getElementById("audioPlayer");
    const minimizeBtn = document.getElementById("minimizeBtn");
    const maximizeBtn = document.getElementById("maximizeBtn");
    const closeBtn = document.getElementById("closeBtn");

    // Verificar el estado del reproductor usando sessionStorage
    if (sessionStorage.getItem("audioPlayerClosed") === "true") {
        audioPlayer.style.display = "none"; // Si se cerró, lo ocultamos al cargar la página
    } else {
        audioPlayer.style.display = "block"; // Si no se cerró, mostramos el reproductor
    }

    minimizeBtn.addEventListener("click", function() {
        audioPlayer.classList.add("minimized");
        minimizeBtn.style.display = "none";
        maximizeBtn.style.display = "inline-block";
    });

    maximizeBtn.addEventListener("click", function() {
        audioPlayer.classList.remove("minimized");
        minimizeBtn.style.display = "inline-block";
        maximizeBtn.style.display = "none";
    });

    closeBtn.addEventListener("click", function() {
        audioPlayer.style.display = "none"; // Oculta el reproductor
        sessionStorage.setItem("audioPlayerClosed", "true"); // Guarda el estado de cerrado
    });
    closeBtn.addEventListener("click", function() {
        if (confirm("¿Estás seguro de que quieres cerrar el reproductor?")) {
            audioPlayer.style.display = "none";
            sessionStorage.setItem("audioPlayerClosed", "true");
        }
    });
    
});
