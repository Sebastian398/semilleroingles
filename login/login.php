<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Usuarios";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los datos del formulario y validar
if (isset($_POST['name']) && isset($_POST['password'])) {
    $name = trim($_POST['name']);
    $contraseña = trim($_POST['password']);

    // Verificar que los campos no estén vacíos
    if (empty($name) || empty($contraseña)) {
        die("Por favor, complete ambos campos.");
    }

    // Preparar la consulta SQL para obtener el usuario
    $sql = "SELECT * FROM usuarios WHERE nombre = ?";
    $stmt = $conn->prepare($sql);

    // Verificar si la preparación fue exitosa
    if ($stmt === false) {
        die('Error al preparar la consulta: ' . $conn->error);
    }

    // Enlazar parámetros y ejecutar la consulta
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si el usuario existe
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verificar la contraseña con el hash almacenado
        if (password_verify($contraseña, $user['contraseña'])) {
            // Iniciar sesión
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre'];

            // Redirigir a la página protegida
            header("Location: ../SemilleroIngles/Categories.php");
            exit();
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "Usuario no encontrado.";
    }

    // Cerrar la declaración
    $stmt->close();
} else {
    echo "Faltan datos del formulario.";
}

// Cerrar la conexión
$conn->close();
?>
