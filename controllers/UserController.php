<?php

namespace controllers;

use models\User;

class UserController
{
    public $userModel;

    public function __construct(User $user)
    {
        $this->userModel = $user;
    }

    public function registerUser($username, $password, $email)
    {
        $this->userModel->username = $username;
        $this->userModel->password = $password;
        $this->userModel->email = $email;

        if ($this->userModel->register()) {
            return ["message" => "Usuario registrado correctamente.", "success" => true];
        } else {
            return ["message" => "Error al registrar usuario.", "success" => false];
        }
    }

    public function loginUser($username, $password)
    {
        $this->userModel->username = $username;
        $this->userModel->password = $password;

        if ($this->userModel->login()) {
            //var_dump($this->userModel);
            //\utils\loginUser($this->userModel);
            return ["message" => "Inicio de sesión exitoso.", "success" => true, "user" => $this->userModel->id, "role" => $this->userModel->role];
        } else {
            return ["message" => "Error al iniciar sesión. Credenciales incorrectas.", "success" => false];
        }
    }


    public function getUserById($userId)
    {
        $this->userModel->id = $userId;

        $user = $this->userModel->readSingle();

        // Almacena la información del usuario en la sesión
        //$_SESSION['user'] = $user;

        return $user;
    }


    public function updateUserProfile($username, $email, $newPassword)
    {
        $this->userModel->username = $username;
        $this->userModel->email = $email;

        if (!empty($newPassword)) {
            $this->userModel->password = password_hash($newPassword, PASSWORD_BCRYPT);
        }
        if ($this->userModel->update()) {
            //var_dump($username, $email, $newPassword);
            return ["message" => "Perfil actualizado correctamente.", "success" => true];
        } else {
            return ["message" => "Error al actualizar el perfil.", "success" => false];
        }
    }

    public function editUser($userId, $newUsername, $newEmail, $newRole, $newPassword = null)
    {
        $user = $this->getUserById($userId);

        if (!$user) {
            return ["message" => "Usuario no encontrado.", "success" => false];
        }

        $this->userModel->username = $newUsername;
        $this->userModel->email = $newEmail;
        $this->userModel->role = $newRole;

        if (!empty($newPassword)) {
            $this->userModel->edit($newPassword);
        } else {
            $this->userModel->edit();
        }

        if ($this->userModel->edit()) {
            return ["message" => "Usuario editado correctamente.", "success" => true];
        } else {
            return ["message" => "Error al editar el usuario.", "success" => false];
        }
    }



    public function getAllUsers()
    {
        return $this->userModel->getAllUsers();
    }

    public function deleteUser($userId)
    {
        $this->userModel->deleteComments($userId);

        if ($this->userModel->delete($userId)) {
            return ["message" => "Perfil eliminado correctamente.", "success" => true];
        } else {
            return ["message" => "Error al eliminar el usuario.", "success" => false];
        }
    }
}
