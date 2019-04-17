<?php
/*
 * Created on : Apr 3, 2019, 4:18:00 PM
 * Author: Tran Trong Thang
 * Email: trantrongthang1207@gmail.com
 * Skype: trantrongthang1207
 */
include_once 'config.php';
if (!empty($_SESSION["login"])) {
    header('Location: createlink.php');
} else {
    
}
if (!empty($_REQUEST['islogin'])) {
    $user_login = $_REQUEST['user_login'];
    $user_pass = $_REQUEST['user_pass'];
    $table = TB_USER;
    $fields = "*";
    $where = "WHERE username = '" . $user_login . "' AND password = MD5('" . $user_pass . "')";
    $orderby = "";
    $limit = "";
    $sql = "SELECT " . $fields . " FROM " . $table . " " . $where . $orderby . $limit;
    $db->query($sql);
    $user = $db->loadObject();
    if ($user) {
        $_SESSION["login"] = 1;
        header('Location: createlink.php');
    }
}
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
        <meta name="generator" content="Jekyll v3.8.5">
        <title>Create Link</title>
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="assets/js/bootstrap.js" type="text/javascript"></script>
        <style>
            .bd-placeholder-img {
                font-size: 1.125rem;
                text-anchor: middle;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }

            @media (min-width: 768px) {
                .bd-placeholder-img-lg {
                    font-size: 3.5rem;
                }
            }
        </style>
        <link href="assets/css/floating-labels.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <form class="form-signin" method="post">

            <div class="form-label-group">
                <input name="user_login" type="text" id="inputEmail" class="form-control" placeholder="User name" required autofocus>
                <label for="inputEmail">User name</label>
            </div>

            <div class="form-label-group">
                <input name="user_pass" type="password" id="inputPassword" class="form-control" placeholder="Password" required>
                <label for="inputPassword">Password</label>
            </div>
            <input type="hidden" name="islogin" value="1"/>

            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
            
        </form>
    </body>
</html>
