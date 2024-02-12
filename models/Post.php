<?php

namespace models;

class Post
{
    private $conn;
    private $table_name = "posts";

    public $id;
    public $title;
    public $content;
    public $user;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        try {
            $query = "INSERT INTO {$this->table_name} SET title=:title, content=:content, user=:user";
            $stmt = $this->conn->prepare($query);

            $this->title = htmlspecialchars(strip_tags($this->title));
            $this->content = htmlspecialchars(strip_tags($this->content));
            $this->user = htmlspecialchars(strip_tags($this->user));

            $stmt->bindParam(":title", $this->title);
            $stmt->bindParam(":content", $this->content);
            $stmt->bindParam(":user", $this->user);

            if ($stmt->execute()) {
                return true;
            }

            throw new \PDOException("Error al ejecutar la consulta de creación de publicación.");
        } catch (\PDOException $e) {
            throw new \PDOException("Error de base de datos: " . $e->getMessage());
        }
    }

    public function read()
    {
        try {
            $query = "SELECT id, title, content, user FROM {$this->table_name} WHERE id=:id";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":id", $this->id);
            $stmt->execute();

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            $this->id = $row['id'];
            $this->title = $row['title'];
            $this->content = $row['content'];
            $this->user = $row['user'];
        } catch (\PDOException $e) {
            throw new \PDOException("Error de base de datos: " . $e->getMessage());
        }
    }

    public function getAll()
    {
        try {
            $query = "SELECT posts.id, posts.title, posts.content, users.username AS user
                      FROM {$this->table_name} AS posts
                      JOIN users ON posts.user = users.id";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            if ($stmt->errorCode() != 0) {
                $errorInfo = $stmt->errorInfo();
                throw new \PDOException("Error de base de datos: " . $errorInfo[2]);
            }

            $posts = array();

            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $post = array(
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'content' => $row['content'],
                    'user' => $row['user']
                );

                $posts[] = $post;
            }

            return $posts;
        } catch (\PDOException $e) {
            throw new \PDOException("Error de base de datos: " . $e->getMessage());
        }
    }


    public function update()
    {
        try {
            $query = "UPDATE {$this->table_name} SET title=:title, content=:content, user=:user WHERE id=:id";
            $stmt = $this->conn->prepare($query);

            $this->id = htmlspecialchars(strip_tags($this->id));
            $this->title = htmlspecialchars(strip_tags($this->title));
            $this->content = htmlspecialchars(strip_tags($this->content));
            $this->user = htmlspecialchars(strip_tags($this->user));

            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":title", $this->title);
            $stmt->bindParam(":content", $this->content);
            $stmt->bindParam(":user", $this->user);

            if ($stmt->execute()) {
                return true;
            }

            throw new \PDOException("Error al ejecutar la consulta de actualización de publicación.");
        } catch (\PDOException $e) {
            throw new \PDOException("Error de base de datos: " . $e->getMessage());
        }
    }

    public function delete($userId)
    {
        try {
            $query = "DELETE FROM {$this->table_name} WHERE id=:id";
            $stmt = $this->conn->prepare($query);

            $userId = htmlspecialchars(strip_tags($userId));
            $stmt->bindParam(":id", $userId);

            if ($stmt->execute()) {
                return true;
            }

            throw new \PDOException("Error al ejecutar la consulta de eliminación de publicación.");
        } catch (\PDOException $e) {
            throw new \PDOException("Error de base de datos: " . $e->getMessage());
        }
    }
}
