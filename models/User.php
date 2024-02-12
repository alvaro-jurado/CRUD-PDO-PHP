<?php

namespace models;

class User
{
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $password;
    public $email;
    public $role;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " SET username=:username, password=:password, email=:email, role=:role";
        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->role = htmlspecialchars(strip_tags($this->role));

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":role", $this->role);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }


    public function read()
    {
        $query = "SELECT id, username, email, role FROM " . $this->table_name . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->id = $row['id'];
        $this->username = $row['username'];
        $this->email = $row['email'];
        $this->role = $row['role'];
    }

    public function readSingle()
    {
        $query = "SELECT id, username, email, role FROM " . $this->table_name . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);

        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . " SET username=:username, password=:password, email=:email WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->email = htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":email", $this->email);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updateCommentsUser($userId)
    {
        $query = "UPDATE comments SET user = 'Cuenta Eliminada' WHERE user = :user_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $userId);

        $stmt->execute();
    }

    public function delete($userId)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $userId);
        return $stmt->execute();
    }

    public function deleteComments($userId)
    {
        $query = "DELETE FROM comments WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $userId);

        $stmt->execute();
    }

    public function getAllUsers()
    {
        $query = "SELECT id, username, email, role FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function edit($newPassword = null)
    {
        $query = "UPDATE users SET username = :new_username, email = :new_email, role = :new_role";

        if ($newPassword !== null) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $query .= ", password = :new_password";
        }

        $query .= " WHERE id = :user_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":new_username", $this->username);
        $stmt->bindParam(":new_email", $this->email);
        $stmt->bindParam(":new_role", $this->role);

        if ($newPassword !== null) {
            $stmt->bindParam(":new_password", $hashedPassword);
        }

        $stmt->bindParam(":user_id", $this->id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }


    public function register()
    {
        $existingUserQuery = "SELECT id FROM " . $this->table_name . " WHERE username=:username OR email=:email";
        $existingUserStmt = $this->conn->prepare($existingUserQuery);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));

        $existingUserStmt->bindParam(":username", $this->username);
        $existingUserStmt->bindParam(":email", $this->email);

        $existingUserStmt->execute();

        if ($existingUserStmt->rowCount() > 0) {
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " SET username=:username, password=:password, email=:email, role='basic_user'";
        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        $this->email = htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":email", $this->email);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function login()
    {
        $query = "SELECT id, username, password, role FROM " . $this->table_name . " WHERE username=:username";
        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));

        $stmt->bindParam(":username", $this->username);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row && password_verify($this->password, $row['password'])) {
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->role = $row['role'];
            return true;
        }

        return false;
    }
}
