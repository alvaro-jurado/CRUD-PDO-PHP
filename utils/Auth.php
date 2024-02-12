<?php

namespace utils;

function loginUser($user, $role)
{
    //var_dump("Role value: " . $role);
    //var_dump("User value: " . $user);
    $_SESSION["user"] = $user;
    $_SESSION["role"] = $role;
}

function logoutUser()
{
    unset($_SESSION["user"]);
}

function isAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isContentWriter()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'content_writer';
}

function isSubscriber()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'subscriber';
}

function isBasicUser()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'basic_user';
}

function getUser()
{
    return isset($_SESSION["user"]) ? $_SESSION["user"] : null;
}

function getRole()
{
    return isset($_SESSION["role"]) ? $_SESSION["role"] : null;
}

function redirectTo($location)
{
    header("Location: $location");
    exit();
}

function startSession()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

function checkSession()
{
    startSession();

    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }
}
