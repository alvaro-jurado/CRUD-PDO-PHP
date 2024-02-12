<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="./home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <script>
        var showAll = false;

        function toggleComments(postId) {
            var commentsDiv = document.getElementById('comments_' + postId);
            if (commentsDiv.style.display === 'none' || commentsDiv.style.display === '') {
                commentsDiv.style.display = 'block';
            } else {
                commentsDiv.style.display = 'none';
            }
        }

        function toggleForm(postId) {
            var formDiv = document.getElementById('commentForm_' + postId);
            if (formDiv.style.display === 'none' || formDiv.style.display === '') {
                formDiv.style.display = 'block';
            } else {
                formDiv.style.display = 'none';
            }
        }

        function togglePosts() {
            var allPosts = document.querySelectorAll('.post');
            var showAllButton = document.getElementById('showAllButton');
            var showLessButton = document.getElementById('showLessButton');

            allPosts.forEach(function(post, index) {
                if (index >= 2) {
                    post.style.display = (showAll) ? 'none' : 'block';
                }
            });

            showAll = !showAll;

            showAllButton.style.display = showAll ? 'none' : 'block';
            showLessButton.style.display = showAll ? 'block' : 'none';
        }
    </script>
    <?php
    require_once "../../models/Post.php";
    require_once "../../models/Comment.php";
    require_once "../../controllers/HomeController.php";
    require_once "../../controllers/PostController.php";
    require_once "../../config/Database.php";
    require_once "../../utils/Auth.php";
    \utils\startSession();

    //var_dump($_SESSION);
    //var_dump(\utils\getUser());

    $db = new \config\Database();
    $dbConnection = $db->getConnection();

    $postModel = new \models\Post($dbConnection);
    $commentModel = new \models\Comment($dbConnection);

    $homeController = new \controllers\HomeController($postModel, $commentModel);
    $postController = new \controllers\PostController($postModel, $commentModel);
    $posts = $homeController->index();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["delete_comment"])) {
            $commentId = $_POST["comment_id"];
            $result = $homeController->deleteComment($commentId);
            if ($result["success"]) {
                //echo("<script>console.log({$result['message']})</script>");
            } else {
                //echo("<script>console.log(Error al eliminar el comentario.)</script>");
            }
        } elseif (isset($_POST["delete_post"])) {
            $postId = $_POST["post_id"];
            $result = $postController->deletePost($postId);
            if ($result["success"]) {
                //echo("<script>console.log({$result['message']})</script>");
            } else {
                //echo("<script>console.log(Error al eliminar el comentario.)</script>");
            }
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["comment"])) {
        $postId = $_POST["post_id"];
        $content = $_POST["comment"];

        $result = $homeController->addComment($postId, $content);

        if ($result["success"]) {
            header("Location: index.php");
            exit();
        } else {
            echo "<p>Error al procesar el comentario.</p>";
        }
    }

    if (!\utils\getUser()) : ?>
        <div>
            <a href="../user/login/login.php">
                <button class="button">Iniciar Sesión</button>
            </a>
            <a href="../user/register/register.php">
                <button class="button">Registrarse</button>
            </a>
        </div>
    <?php else : ?>
        <div>
            <a href="../user/profile/profile.php">
                <button class="button">Perfil</button>
            </a>
            <?php if (\utils\isAdmin() || \utils\isContentWriter()) :
            ?>
                <a href="../post/post.php">
                    <button class="button">Publicar</button>
                </a>
            <?php endif;
            ?>
            <?php if (\utils\isAdmin()) :
            ?>
                <a href="../admin/admin_dashboard.php">
                    <button class="button">Admin</button>
                </a>
            <?php endif;
            ?>
            <form action="../user/logout/logout.php" method="post" id="cerrar_sesion">
                <input type="submit" value="Cerrar Sesión" class="button">
            </form>
        </div>
    <?php endif; ?>
    <h1 class="titulo">Publicaciones Recientes</h1>
    <?php if (empty($posts)) : ?>
        <p>No hay publicaciones disponibles.</p>
    <?php else : ?>
        <?php $count = 0; ?>
        <?php foreach ($posts as $post) : ?>
            <div class="post blog" style="<?php echo ($count >= 2) ? 'display: none;' : ''; ?>">
                <h2><?php echo $post['title']; ?> &nbsp; @<?php echo $post['user']; ?></h2>
                <p><?php echo nl2br($post['content']); ?></p>
                <p></p>


                <button onclick="toggleComments(<?php echo $post['id']; ?>)"><i class="fa-solid fa-comment-dots"></i></button>
                <?php if (\utils\isAdmin() || \utils\isContentWriter() || \utils\isSubscriber()) : ?>
                    <button onclick="toggleForm(<?php echo $post['id']; ?>)"><i class="fa-solid fa-message"></i></button>
                <?php endif;
                ?>

                <?php if (\utils\isAdmin()) :
                ?>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display: inline;">
                        <input type="hidden" name="delete_post" value="1">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <button type="submit"><i class="fa-solid fa-trash-can"></i></button>
                    </form>
                <?php endif;
                ?>

                <div id="comments_<?php echo $post['id']; ?>" style="display: none;">

                    <?php
                    $comments = $homeController->getComments($post['id']);
                    foreach ($comments as $comment) : ?>
                        <p><?php echo "@{$comment['user']}: {$comment['content']}"; ?>

                            <?php if (\utils\isAdmin()) :
                            ?>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="delete_comment" value="1">
                            <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                            <button type="submit"><i class="fa-solid fa-trash-can"></i></button>
                        </form>
                    <?php endif;
                    ?>

                    </p>
                <?php endforeach; ?>
                </div>

                <div id="commentForm_<?php echo $post['id']; ?>" style="display: none;">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <textarea id="comment_<?php echo $post['id']; ?>" name="comment" rows="10" cols="60" class="comment" placeholder="Escribe tu comentario..."></textarea><br>
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <button type="submit"><i class="fa-solid fa-upload"></i></button>
                    </form>
                </div>
            </div>
            <?php $count++; ?>
        <?php endforeach; ?>

        <button class="button mostrar" onclick="togglePosts()" id="showAllButton" <?php echo ($count <= 2) ? 'style="display: none;"' : ''; ?>>Mostrar Todos</button>
        <button class="button mostrar" onclick="togglePosts()" id="showLessButton" style="display: none;">Mostrar Menos</button>
    <?php endif; ?>
</body>

</html>