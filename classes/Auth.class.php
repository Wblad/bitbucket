<?php

namespace Auth;
class User
{
    private $user_id;
    private $username;
    private $login;
    private $email;
    private $password;
    private $db;
    private $is_authorized = false;

    public static function isAuthorized()
    {
        if (!empty($_SESSION["user_id"])) {
            return (bool)$_SESSION["user_id"];
        }
        return false;
    }

    public function passwordHash($password, $salt = null, $iterations = 10)
    {
        $salt || $salt = uniqid();
        $hash = md5(md5($password . md5(sha1($salt))));

        for ($i = 0; $i < $iterations; ++$i) {
            $hash = md5(md5(sha1($hash)));
        }

        return array('hash' => $hash, 'salt' => $salt);
    }

    public function getSalt($username)
    {
        $query = "select salt from users where username = :username limit 1";
        $sth = $this->db->prepare($query);
        $sth->execute(
            array(
                ":username" => $username
            )
        );
        $row = $sth->fetch();
        if (!$row) {
            return false;
        }
        return $row["salt"];
    }

    public function authorize($login, $password)
    {
        $query = "select id, username from users where
            username = :username and password = :password limit 1";


        $sth = $this->db->prepare($query);
        $salt = $this->getSalt($login);

        if (!$salt) {
            return false;
        }

        $hashes = $this->passwordHash($password, $salt);
        $sth->execute(
            array(
                ":username" => $login,
                ":password" => $hashes['hash'],
            )
        );
        $this->login = $sth->fetch();

        if (!$this->login) {
            $this->is_authorized = false;
        } else {
            $this->is_authorized = true;
            $this->user_id = $this->login;
            $this->saveSession();
        }
        return $this->is_authorized;
    }

    public function logout()
    {
        if (!empty($_SESSION["user_id"])) {
            unset($_SESSION["user_id"]);
        }
    }

    public function saveSession($remember = false, $http_only = true, $days = 7)
    {
        $_SESSION["user_id"] = $this->user_id;

        if ($remember) {
            // Save session id in cookies
            $sid = session_id();

            $expire = time() + $days * 24 * 3600;
            $domain = ""; // default domain
            $secure = false;
            $path = "/";

            $cookie = setcookie("sid", $sid, $expire, $path, $domain, $secure, $http_only);
        }
    }

    public function create($username, $login, $email, $password, $confirm_password)
    {
        $user_exists = $this->getSalt($username);

        if ($user_exists) {
            throw new \Exception("User exists: " . $username, 1);
        }

        $query = "insert into users (username, password, salt)
            values (:username, :password, :salt)";
        $hashes = $this->passwordHash($password);
        $sth = $this->db->prepare($query);

        try {
            $this->db->beginTransaction();
            $result = $sth->execute(
                array(
                    ':username' => $username,
                    ':login' => $login,
                    ':email' => $email,
                    ':password' => $hashes['hash'],
                    ':salt' => $hashes['salt'],
                )
            );
            $this->db->commit();
        } catch (\PDOException $e) {
            $this->db->rollback();
            echo "Database error: " . $e->getMessage();
            die();
        }

        if (!$result) {
            $info = $sth->errorInfo();
            printf("Database error %d %s", $info[1], $info[2]);
            die();
        }

        return $result;
    }

    private function connectdb(): void
    {
        $this->db = fopen("../db.json", 'a+') or die("File no opened!");
    }
}
/*
class Database
{
    private $db;

    private function db_conn(): void
    {
        $this->db = fopen("../Database/DB.json", 'a+') or die("File no opened!");
    }

    public function write($json)
    {
        $null = "nul";
        var_dump($json);
        $this->db_conn();
        if ($json == $null) return fclose($this->db);
        else fwrite($this->db, $json);
        fwrite($this->db, $json);
        fclose($this->db);
    }

    public function read(): string
    {
        $this->db_conn();
        $str = fread($this->db, filesize($this->db));
        fclose($this->db);
        return $str;
    }
}*/