<?php
require_once "../../../config/Database.php";
require_once "../../../models/User.php";
require_once "../../../controllers/UserController.php";
require_once "../../../utils/Auth.php";
\utils\checkSession();

$db = new \config\Database();
$conn = $db->getConnection();

$userController = new \controllers\UserController(new \models\User($conn));
//var_dump($_SESSION['user']);

$userController->userModel->id = $_SESSION['user'];
$userController->userModel->read();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="./profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <a href="../../home/index.php">
        <button class="button"><i class="fa-solid fa-right-from-bracket"></i></button>
    </a>
    <h2>Perfil de <?php echo $userController->userModel->username; ?></h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="form-web">
        <div class="profile">
            <label for="username">Usuario:</label>
            <input type="text" name="username" value="<?php echo $userController->userModel->username; ?>" required><br>

            <label for="email">Correo electrónico:</label>
            <input type="email" name="email" value="<?php echo $userController->userModel->email; ?>" required><br>

            <label for="password">Nueva Contraseña:</label>
            <input type="password" name="password" required><br>

            <button type="submit" class="button"><i class="fa-solid fa-pen-to-square"></i>&nbsp;Actualizar Perfil</button>
        </div>
    </form>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $email = $_POST["email"];
        $password = $_POST["password"];

        $newPassword = !empty($password) ? $password : null;

        $updateResult = $userController->updateUserProfile($username, $email, $newPassword);

        if ($updateResult["success"]) {
            /* echo "
            <div class='message-div'>
                <p class='message'>{$updateResult['message']}</p>
            </div>
            ";*/
        } else {
            /* echo "
            <div class='message-div'>
                <p class='message'>{$updateResult['message']}</p>
            </div>
            ";*/
        }
    }
    ?>
</body>

</html>