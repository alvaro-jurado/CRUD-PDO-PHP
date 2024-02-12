<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Publicación</title>
    <link rel="stylesheet" href="./post.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <a href="../home/index.php">
        <button class="button"><i class="fa-solid fa-right-from-bracket"></i></button>
    </a>
    <h2>Nueva Publicación</h2>

    <?php
    session_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        require_once '../../controllers/PostController.php';
        require_once '../../models/Post.php';
        require_once '../../models/Comment.php';
        require_once "../../config/Database.php";
        require_once "../../utils/Auth.php";

        $db = new \config\Database();
        $dbConnection = $db->getConnection();

        $postModel = new models\Post($dbConnection);
        $commentModel = new \models\Comment($dbConnection);
        $postController = new controllers\PostController($postModel, $commentModel);

        $title = $_POST['title'];
        $content = $_POST['content'];

        $result = $postController->createPost($title, $content);

        if ($result['success']) {
            echo "
            <div class='message-div'>
                <p class='message'>{$result['message']}</p>
            </div>
            ";
        } else {
            echo "
            <div class='message-div'>
                <p class='message'>{$result['message']}</p>
            </div>
            ";
        }
    }
    ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="form-web">
        <div class="post">
            <label for="title">Título:</label>
            <input type="text" name="title" required><br>

            <label for="content">Contenido:</label>
            <textarea name="content" style="width: 450px; height: 200px;" required></textarea><br>

            <button type="submit" class="button"><i class="fa-solid fa-upload"></i>&nbsp;Publicar</button>
        </div>
    </form>

</body>

</html>