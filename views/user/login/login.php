<?php
//session_start();
require_once "../../../config/Database.php";
require_once "../../../models/User.php";
require_once "../../../controllers/UserController.php";
require_once "../../../utils/Auth.php";
\utils\startSession();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new \config\Database();
    $conn = $db->getConnection();

    $userController = new \controllers\UserController(new \models\User($conn));

    $username = $_POST["username"];
    $password = $_POST["password"];

    $loginResult = $userController->loginUser($username, $password);

    if ($loginResult["success"]) {
        \utils\loginUser($loginResult["user"], $loginResult["role"]);
        \utils\redirectTo("../../home/index.php");
    } else {
        echo "
        <div class='message-div'>
            <p class='message'>{$loginResult['message']}</p>
        </div>
        ";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./login.css">
</head>

<body>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="form-web">
        <div class="login">
            <label for="username">Usuario:</label>
            <input type="text" name="username" required><br>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" required><br>

            <input type="submit" value="Iniciar sesión" class="button">

            <a href="../register/register.php" class="registrar">¿No tienes cuenta?</a>
        </div>

    </form>
</body>

</html>