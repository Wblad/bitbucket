<?php

if (!empty($_COOKIE['sid'])) {
    // check session id in cookies
    session_id($_COOKIE['sid']);
}

session_start();
require_once 'classes/Auth.class.php';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Authorization</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<?php if (Auth\User::isAuthorized()): ?>
    <h1>Your are welcome!</h1>
    <form class="ajax" method="post" action="./ajax.php">
        <button type="submit" name="act" value="logout">Logout</button>
    </form>
<?php else: ?>
    <h2>Please sign in</h2>
    <form method="post" action="./ajax.php">
        <input name="Login" type="text" placeholder="Login" autofocus>
        <input name="password" type="password" placeholder="Password">
        <button type="submit" name="act" value="login">Sign in</button>
    </form>
<p>Not have an account? <a href="/register.php">Register it.</a>
<?php endif; ?>
<script src="./vendor/jquery-2.0.3.min.js"></script>
<script src="./js/ajax-form.js"></script>
</body>
</html>
