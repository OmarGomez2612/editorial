<style>
        /* Estilos solo para el reproductor de audio */
        audio {
            width: 100%;
            max-width: 400px;
            border: 2px solid #000;
            border-radius: 5px;
            margin-top: 20px;
            background-color: #fff; /* Fondo blanco */
        }

        /* Estilo para el contenedor del reproductor */
        .container {
            width: 100%;
            height: 100vh; /* Utilizamos el 100% de la altura de la pantalla */
            display: flex;
            flex-direction: column;
            align-items: center; /* Centra el contenido horizontalmente */
            text-align: center;
        }

        /* Estilo de mensaje vacío (error) */
        .empty {
            color: #f00;
            font-size: 1.2rem;
            margin-top: 20px;
        }
    </style>
<?php

// Verificar si el usuario tiene un audiolibro en reproducción (almacenado en la sesión)
$audio_playing = isset($_SESSION['audio_playing']) ? $_SESSION['audio_playing'] : null;
$audio_file_path = isset($_SESSION['audio_file_path']) ? $_SESSION['audio_file_path'] : null;
$audio_book_name = isset($_SESSION['audio_book_name']) ? $_SESSION['audio_book_name'] : null;

if ($audio_playing && $audio_file_path) {
    // Si el usuario tiene un audiolibro en reproducción, mostrar el reproductor
    ?>
    <div class="audio-bar">
        <h1>Reproduciendo: <?= htmlspecialchars($audio_book_name); ?></h1>
        <audio controls controlsList="nodownload" autoplay>
            <source src="<?= htmlspecialchars($audio_file_path); ?>" type="audio/mpeg">
            Tu navegador no soporta el elemento de audio.
        </audio>
    </div>
    <?php
}
?>
