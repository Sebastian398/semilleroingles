<?php

// Configura la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Usuarios";

// Habilitar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar el buffer de salida
ob_start();

// Crea una conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del formulario
if (isset($_POST['name'], $_POST['lastName'], $_POST['email'], $_POST['password'])) {
    $name = $_POST['name'];
    $last_name = $_POST['lastName'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validar el formato del correo electrónico
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Correo inválido, por favor ingresa un correo válido');</script>";
        header("Refresh:0; url=/SemilleroIngles/SignUp.html");
        exit();
    }

    // Hacer hash de la contraseña
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Verificar si el correo ya existe
    $sql_check = "SELECT email FROM usuarios WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "<script>alert('El correo ya está registrado. Por favor, usa otro correo.');</script>";
        header("Refresh:0; url=/SemilleroIngles/SignUp.html");
        exit();
    } else {
        // Preparar la consulta SQL para insertar
        $sql_insert = "INSERT INTO usuarios (nombre, apellido, email, contraseña) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);

        // Verificar si la preparación fue exitosa
        if ($stmt_insert === false) {
            echo "<script>alert('Error al preparar la consulta. Contacta con un desarrollador.');</script>";
            header("Refresh:0; url=/SemilleroIngles/SignUp.html");
            exit();
        }

        // Enlazar parámetros e insertar los datos
        $stmt_insert->bind_param("ssss", $name, $last_name, $email, $hashed_password);

        if ($stmt_insert->execute()) {
            echo "<script>alert('Registro exitoso. Redirigiendo al inicio de sesión.');</script>";
            header("Refresh:0; url=/SemilleroIngles/Login.html");
        } else {
            echo "<script>alert('Error al registrar: " . $stmt_insert->error . "');</script>";
            header("Refresh:0; url=/SemilleroIngles/SignUp.html");
        }

        $stmt_insert->close();
    }

    $stmt_check->close();
    $conn->close();
} else {
    echo "<script>alert('Faltan datos del formulario. Completa todos los campos.');</script>";
    header("Refresh:0; url=/SemilleroIngles/SignUp.html");
}

// Cerrar el buffer de salida
ob_end_flush();
?>
