<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Editar Usuario</title>
    <link rel="stylesheet" href="./admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <a href="./admin_dashboard.php">
        <button class="button"><i class="fa-solid fa-right-from-bracket"></i></button>
    </a>
    <h2>Admin - Editar Usuario</h2>

    <?php
    require_once "../../controllers/UserController.php";
    require_once "../../config/Database.php";
    require_once "../../models/User.php";
    require_once "../../utils/Auth.php";
    \utils\checkSession();

    $db = new \config\Database();
    $conn = $db->getConnection();

    $userModel = new \models\User($conn);
    $userController = new \controllers\UserController($userModel);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_user"])) {
        $userId = $_POST["user_id"];
        $newUsername = $_POST["new_username"];
        $newEmail = $_POST["new_email"];
        $newRole = $_POST["new_role"];

        $result = $userController->editUser($userId, $newUsername, $newEmail, $newRole);

        if ($result["success"]) {
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "<p>Error al editar el usuario.</p>";
        }
    }

    $userId = $_GET["id"];
    $user = $userController->getUserById($userId);

    if ($user) {
    ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="form-web">
            <div class="edit-user">
                <label for="new_username">Nuevo nombre de usuario:</label>
                <input type="text" name="new_username" value="<?php echo $user['username']; ?>" required><br>

                <label for="new_email">Nuevo correo electrónico:</label>
                <input type="email" name="new_email" value="<?php echo $user['email']; ?>" required><br>

                <label for="new_password">Nueva Contraseña:</label>
                <input type="password" name="new_password">

                <label for="new_role">Nuevo rol:</label>
                <select name="new_role" required>
                    <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="content_writer" <?php echo ($user['role'] === 'content_writer') ? 'selected' : ''; ?>>Escritor de Contenido</option>
                    <option value="subscriber" <?php echo ($user['role'] === 'subscriber') ? 'selected' : ''; ?>>Suscriptor</option>
                </select><br>

                <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                <button type="submit" name="edit_user" class="button"><i class="fa-solid fa-floppy-disk"></i>&nbsp;Guardar Cambios</button>
            </div>
        </form>
    <?php
    } else {
        echo "<p>Usuario no encontrado.</p>";
    }
    ?>
</body>

</html>