<?php

if (!empty($_COOKIE['sid'])) {
    // check session id in cookies
    session_id($_COOKIE['sid']);
}
session_start();
require_once './classes/Auth.class.php';

?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PHP Ajax Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<?php if (Auth\User::isAuthorized()): ?>
    <h1>Your are already registered!</h1>
    <form class="ajax" method="post" action="./ajax.php">
        <button type="submit" name="act" value="logout">Logout</button>
    </form>
<?php else: ?>
<form class="form-signin ajax" method="post" action="./ajax.php">
    <h2>Please sign up</h2>
    <input name="username" type="text" placeholder="Username" autofocus>
    <input name="email" type="text" placeholder="Email">
    <input name="login" type="text" placeholder="Login">
    <input name="password1" type="password" placeholder="Password">
    <input name="password2" type="password" placeholder="Confirm password">
    <button type="submit" name="act" value="register">Register</button>
</form>
<p>Already have account? <a href="/index.php">Sign In.</a>
    <?php endif; ?>
    <script src="./vendor/jquery-2.0.3.min.js"></script>
    <script src="./js/ajax-form.js"></script>
</body>
</html>
