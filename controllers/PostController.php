<?php

namespace controllers;

use models\Post;
use models\Comment;

class PostController
{
    private $postModel;
    private $commentModel;

    public function __construct(Post $post, Comment $comment)
    {
        $this->postModel = $post;
        $this->commentModel = $comment;
    }

    public function createPost($title, $content)
    {
        $this->postModel->title = $title;
        $this->postModel->content = $content;
        $this->postModel->user = $_SESSION['user'];

        if ($this->postModel->create()) {
            return ["message" => "Publicación creada correctamente.", "success" => true];
        } else {
            return ["message" => "Error al crear publicación.", "success" => false];
        }
    }

    public function readPost($postId)
    {
        $this->postModel->id = $postId;
        $this->postModel->read();

        return $this->postModel;
    }

    public function updatePost($postId, $title, $content, $user)
    {
        $this->postModel->id = $postId;
        $this->postModel->title = $title;
        $this->postModel->content = $content;
        $this->postModel->user = $user;

        if ($this->postModel->update()) {
            return ["message" => "Publicación actualizada correctamente.", "success" => true];
        } else {
            return ["message" => "Error al actualizar publicación.", "success" => false];
        }
    }

    public function deletePost($postId)
    {
        $comments = $this->commentModel->getCommentsByPost($postId);

        foreach ($comments as $comment) {
            $this->commentModel->delete($comment['id']);
        }

        if ($this->postModel->delete($postId)) {
            return ["message" => "Post eliminado correctamente.", "success" => true];
        } else {
            return ["message" => "Error al eliminar el post.", "success" => false];
        }
    }
}
