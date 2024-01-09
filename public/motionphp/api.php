<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "motionjs";

// Conexión a la base de datos utilizando PDO
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consultar si la tabla existe
    $query = "SHOW TABLES LIKE 'imagenes'";
    $statement = $conn->prepare($query);
    $statement->execute();
    $tableExists = $statement->rowCount() > 0;

    // Crear la tabla 'imagenes' si no existe
    if (!$tableExists) {
        $query = "CREATE TABLE imagenes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            url VARCHAR(255) NOT NULL,
            descripcion VARCHAR(255) NOT NULL
        )";
        $conn->exec($query);
    }

    // Verificar si se recibió una solicitud GET para obtener las imágenes
    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        // Consultar las imágenes desde la tabla
        $query = "SELECT * FROM imagenes";
        $statement = $conn->prepare($query);
        $statement->execute();
        $images = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Devolver las imágenes como respuesta en formato JSON
        header("Content-Type: application/json");
        echo json_encode($images);
    }

    // Verificar si se recibió una solicitud POST para insertar una imagen
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $imageUrl = $_POST["image-url"];
        $imageDescription = $_POST["image-description"];

        // Insertar la imagen en la tabla 'imagenes'
        $query = "INSERT INTO imagenes (url, descripcion) VALUES (?, ?)";
        $statement = $conn->prepare($query);
        $statement->execute([$imageUrl, $imageDescription]);

        $response = array("message" => "Imagen creada con éxito.");
        echo json_encode($response);
    }

    // Verificar si se recibió una solicitud PUT para actualizar una imagen
    if ($_SERVER["REQUEST_METHOD"] === "PUT") {
        parse_str(file_get_contents("php://input"), $putParams); // Obtener los datos del cuerpo de la solicitud PUT
        $imageId = $putParams["image-id"];
        $imageUrl = $putParams["image-url"];
        $imageDescription = $putParams["image-description"];

        // Actualizar la imagen en la tabla 'imagenes'
        $query = "UPDATE imagenes SET url = ?, descripcion = ? WHERE id = ?";
        $statement = $conn->prepare($query);
        $statement->execute([$imageUrl, $imageDescription, $imageId]);

        $response = array("message" => "Imagen actualizada con éxito.");
        echo json_encode($response);
    }
} catch (PDOException $e) {
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}

// Cerrar la conexión a la base de datos
$conn = null;
?>