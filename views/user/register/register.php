<?php
require_once "../../../config/Database.php";
require_once "../../../models/User.php";
require_once "../../../controllers/UserController.php";
require_once "../../../utils/Auth.php";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="./register.css">
</head>

<body>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="form-web">
        <div class="register">
            <label for="username">Usuario:</label>
            <input type="text" name="username" required><br>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" required><br>

            <label for="email">Correo electrónico:</label>
            <input type="email" name="email" required><br>

            <input type="submit" value="Registrarse" class="button">
            <a href="../login/login.php" class="login">¿Ya tienes una cuenta?</a>
        </div>

    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $db = new \config\Database();
        $conn = $db->getConnection();

        $userController = new \controllers\UserController(new \models\User($conn));

        $username = $_POST["username"];
        $password = $_POST["password"];
        $email = $_POST["email"];

        $registerResult = $userController->registerUser($username, $password, $email);

        if ($registerResult["success"]) {
            echo "
            <div class='message-div'>
                <p class='message'>{$registerResult['message']}</p>
            </div>
            ";
        } else {
            echo "
            <div class='message-div'>
                <p class='message'>{$registerResult['message']}</p>
            </div>
            ";
        }
    }

    // closeSession();
    ?>
</body>

</html>