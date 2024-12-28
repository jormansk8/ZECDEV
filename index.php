<?php
session_start();

// Configuración
$uploadDir = 'uploads/'; // Carpeta donde se guardarán los archivos
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Función para generar un identificador único
function generateUniqueId() {
    return bin2hex(random_bytes(16));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['audioFile'])) {
    $file = $_FILES['audioFile'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $filename = basename($file['name']);
        $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);

        // Validar que el archivo sea MP3 o WAV
        $allowedExtensions = ['mp3', 'wav'];
        if (in_array(strtolower($fileExtension), $allowedExtensions)) {
            $uniqueId = generateUniqueId();
            $targetPath = $uploadDir . $uniqueId . '_' . $filename;

            // Mover archivo subido
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                // Guardar enlace en sesión
                $_SESSION['one_time_links'][$uniqueId] = $targetPath;

                // Generar URL única
                $oneTimeUrl = "http://" . $_SERVER['HTTP_HOST'] . "/listen.php?token=$uniqueId";
                echo "<div style='font-family: Arial, sans-serif; text-align: center; margin-top: 20px;'>";
                echo "<p style='color: green; font-size: 18px;'>Archivo subido con éxito. Aquí está tu enlace único:</p>";
                echo "<a href='$oneTimeUrl' style='color: blue; text-decoration: none; font-size: 16px;'>$oneTimeUrl</a>";
                echo "</div>";
            } else {
                echo "<p style='color: red; text-align: center;'>Error al mover el archivo.</p>";
            }
        } else {
            echo "<p style='color: red; text-align: center;'>Formato no válido. Solo se permiten archivos MP3 o WAV.</p>";
        }
    } else {
        echo "<p style='color: red; text-align: center;'>Error al subir el archivo.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir y Generar Enlace Único</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f4f4f9;
        }
        .container {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        label {
            font-size: 16px;
            margin-bottom: 10px;
        }
        input[type="file"] {
            margin-bottom: 20px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Subir un Archivo de Audio</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="audioFile">Seleccionar archivo de audio (MP3 o WAV):</label>
            <input type="file" name="audioFile" id="audioFile" accept="audio/mp3,audio/wav" required>
            <button type="submit">Subir Archivo</button>
        </form>
    </div>
</body>
</html>
