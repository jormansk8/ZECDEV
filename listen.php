<?php
session_start();

if (!isset($_GET['token'])) {
    die("<p style='color: red; text-align: center;'>Token no válido o faltante.</p>");
}

$token = $_GET['token'];

// Verificar que el token existe y corresponde a un archivo
if (!isset($_SESSION['one_time_links'][$token])) {
    die("<p style='color: red; text-align: center;'>Enlace inválido o ya utilizado.</p>");
}

$filePath = $_SESSION['one_time_links'][$token];
$fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

// Validar que el archivo existe y es un MP3 o WAV
if (!file_exists($filePath) || !in_array(strtolower($fileExtension), ['mp3', 'wav'])) {
    die("<p style='color: red; text-align: center;'>Archivo no encontrado o formato inválido.</p>");
}

// Eliminar el enlace para que solo pueda ser usado una vez
unset($_SESSION['one_time_links'][$token]);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reproducir Audio</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f4f4f9;
            overflow: hidden;
        }

        .container {
            text-align: center;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.6s ease, opacity 0.6s ease;
        }

        audio {
            margin-top: 20px;
            width: 100%;
            max-width: 500px;
        }

        p {
            font-size: 16px;
            color: #333;
        }

        .explode {
            transform: scale(3) rotate(360deg);
            opacity: 0;
        }

        .particles {
            position: absolute;
            width: 5px;
            height: 5px;
            background: #f00;
            border-radius: 50%;
            animation: fly 1s ease-out forwards;
        }

        @keyframes fly {
            to {
                transform: translate(calc(var(--x) * 100px), calc(var(--y) * 100px));
                opacity: 0;
            }
        }

        .deleting {
            font-size: 20px;
            color: #ff0000;
            margin-top: 20px;
            animation: fadeOut 1.5s ease forwards;
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container" id="audioContainer">
        <p style="color: red; font-weight: bold;">Este audio solo se puede escuchar una vez. Disfrútalo.</p>
        <audio controls onended="explodeContainer()" controlsList="nodownload">
            <source src="<?php echo htmlspecialchars($filePath); ?>" type="audio/<?php echo $fileExtension; ?>">
            Tu navegador no soporta la reproducción de este formato de audio.
        </audio>
    </div>

    <p id="deleteMessage" class="deleting" style="display: none;">Audio eliminado...</p>

    <script>
        function explodeContainer() {
            const container = document.getElementById('audioContainer');
            container.classList.add('explode');
            generateParticles(container);

            setTimeout(() => {
                container.remove();
                showDeletingMessage();
            }, 600); // Remover el contenedor después de la animación
        }

        function generateParticles(container) {
            for (let i = 0; i < 30; i++) {
                const particle = document.createElement('div');
                particle.className = 'particles';
                particle.style.setProperty('--x', Math.random() * 2 - 1);
                particle.style.setProperty('--y', Math.random() * 2 - 1);
                container.appendChild(particle);
            }
        }

        function showDeletingMessage() {
            const deleteMessage = document.getElementById('deleteMessage');
            deleteMessage.style.display = 'block';
            setTimeout(() => {
                deleteMessage.style.display = 'none';
            }, 1500); // Ocultar el mensaje después de 1.5 segundos
        }
    </script>
</body>

</html>