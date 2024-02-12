<?php

namespace models;

use PDO;

class Comment
{
    private $conn;
    private $table_name = "comments";

    public $id;
    public $post_id;
    public $user_id;
    public $content;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " SET post_id=:post_id, user_id=:user_id, content=:content";
        $stmt = $this->conn->prepare($query);

        $this->post_id = htmlspecialchars(strip_tags((int)$this->post_id));
        $this->user_id = htmlspecialchars(strip_tags((int)$this->user_id));
        $this->content = htmlspecialchars(strip_tags((string)$this->content));

        $stmt->bindParam(":post_id", $this->post_id);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":content", $this->content);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function read()
    {
        $query = "SELECT id, post_id, user, content FROM " . $this->table_name . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id = $row['id'];
        $this->post_id = $row['post_id'];
        $this->user_id = $row['user'];
        $this->content = $row['content'];
    }


    public function update()
    {
        $query = "UPDATE " . $this->table_name . " SET post_id=:post_id, user_id=:user_id, content=:content WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->post_id = htmlspecialchars(strip_tags($this->post_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->content = htmlspecialchars(strip_tags($this->content));

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":post_id", $this->post_id);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":content", $this->content);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }


    public function delete($commentId)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($commentId));
        $stmt->bindParam(":id", $commentId);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function getCommentsByUser($userId)
    {
        $query = "SELECT id FROM " . $this->table_name . " WHERE user = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->execute();

        $comments = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $comments[] = $row;
        }

        return $comments;
    }

    public function getCommentsByPost($postId)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE post_id = :post_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":post_id", $postId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateCommentUser($commentId, $newUser)
    {
        $query = "UPDATE " . $this->table_name . " SET user = :user WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user", $newUser);
        $stmt->bindParam(":id", $commentId);

        return $stmt->execute();
    }


    public function getCommentsForPost($postId)
    {
        $query = "
            SELECT c.id, u.username, c.content 
            FROM " . $this->table_name . " c
            INNER JOIN users u ON c.user_id = u.id
            WHERE c.post_id = :post_id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":post_id", $postId);
        $stmt->execute();

        $comments = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $comment = array(
                "id" => $row['id'],
                "user" => $row['username'],
                "content" => $row['content']
            );
            $comments[] = $comment;
        }

        return $comments;
    }
}
