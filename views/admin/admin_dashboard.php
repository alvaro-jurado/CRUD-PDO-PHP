<?php
require_once "../../utils/Auth.php";
require_once "../../models/User.php";
require_once "../../controllers/UserController.php";
require_once "../../config/Database.php";
\utils\checkSession();
//var_dump($_SESSION['user']);
//var_dump(\utils\getRole());
//var_dump(\utils\isAdmin());

if (!\utils\isAdmin()) {
    header("Location: ../home/index.php");
    exit();
}
$db = new \config\Database();
$dbConnection = $db->getConnection();
$userModel = new \models\User($dbConnection);
$userController = new \controllers\UserController($userModel);



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["delete_user"])) {
        $userId = $_POST["user_id"];
        $result = $userController->deleteUser($userId);
        if ($result["success"]) {
            echo "<p>{$result['message']}</p>";
        } else {
            echo "<p>Error al editar el usuario.</p>";
        }
    } elseif (isset($_POST["edit_user"])) {
        $userId = $_POST["user_id"];
        header("Location: admin_edit_user.php?id={$userId}");
        exit();
    }
}

$users = $userController->getAllUsers();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="./admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <a href="../home/index.php">
        <button class="button"><i class="fa-solid fa-right-from-bracket"></i></button>
    </a>

    <h1>Admin Dashboard</h1>

    <h2>Lista de Usuarios</h2>
    <div class="container-table">
        <table>
            <tr>
                <th><i class="fa-regular fa-id-card"></i>&nbsp;ID</th>
                <th><i class="fa-solid fa-user"></i>&nbsp;Usuario</th>
                <th><i class="fa-solid fa-envelope"></i>&nbsp;Correo Electrónico</th>
                <th><i class="fa-solid fa-person-circle-exclamation"></i>&nbsp;Rol</th>
                <th><i class="fa-solid fa-location-crosshairs"></i>&nbsp;Acciones</th>
            </tr>
            <?php foreach ($users as $user) : ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['role']; ?></td>
                    <td>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="delete_user"><i class="fa-solid fa-trash-can"></i></button>
                            <button type="submit" name="edit_user"><i class="fa-solid fa-pen"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>

</html>