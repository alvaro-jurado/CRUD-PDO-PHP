<?php

namespace controllers;

use models\Post;
use models\Comment;

class HomeController
{
    private $postModel;
    private $commentModel;

    public function __construct(Post $post, Comment $comment)
    {
        $this->postModel = $post;
        $this->commentModel = $comment;
    }

    public function index()
    {
        $posts = $this->getAllPosts();

        return $posts;
    }

    public function getComments($postId)
    {
        $comments = $this->commentModel->getCommentsForPost($postId);
        return $comments;
    }

    public function addComment($postId, $content)
    {
        $this->commentModel->post_id = $postId;
        $this->commentModel->user_id = $_SESSION['user'];
        $this->commentModel->content = $content;

        if ($this->commentModel->create()) {
            return ["message" => "Comentario creado correctamente.", "success" => true];
        } else {
            return ["message" => "Error al crear el comentario.", "success" => false];
        }
    }

    public function deleteComment($commentId)
    {

        if ($this->commentModel->delete($commentId)) {
            return ["message" => "Comentario eliminado correctamente.", "success" => true];
        } else {
            return ["message" => "Error al eliminar el comentario.", "success" => false];
        }
    }


    private function getAllPosts()
    {
        try {
            $allPosts = $this->postModel->getAll();

            return $allPosts;
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
