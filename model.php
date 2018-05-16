<?php

class DataModel {

    protected static $connection;

    function __construct(
        string $server, string $user, string $password, string $name
    ) {
        $this->server = $server;
        $this->user   = $user;
        $this->name   = $name;
        $this->pwd    = $password;
    }

    public function connect() {
        if (!isset(self::$connection)) {
            // printf("connecting to db\n");
            self::$connection = new mysqli(
                $this->server, $this->user, $this->pwd, $this->name
            );
        }
        if (mysqli_connect_errno()) {
            die("Connection failed: " . mysqli_connect_error());
        }
        return self::$connection;
    }

    public function get_user(string $userid) : array {
        // if it aint a numeric string it a aint a vaild id.
        if (!is_numeric($userid)) {
            throw new Exception("Non numeric userid.\n");
        }
        // Connect to db; try get user, if not return empty array.
        $conn = $this->connect();
        $stmt = $conn->prepare(
            "SELECT * FROM games WHERE userid = (?) LIMIT 1"
        );
        $stmt->bind_param('s', $userid);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row;
        }
        return [];
    }

    public function new_user(string $new_word, string $userid) : array {
        $conn = $this->connect();
        $new_guess = str_repeat("_", strlen($new_word));
        $stmt = $conn->prepare(
            "INSERT INTO games (userid, guess, word) VALUES (?, ?, ?)"
        );
        $stmt->bind_param('sss', $userid, $new_guess, $new_word);
        $stmt->execute();
        return $this->get_user($userid);
    }

    public function update_user(string $userid, array $data) {
        list($guess, $letters, $lives) = $data;
        $conn = $this->connect();
        $stmt = $conn->prepare(
            "UPDATE games
             SET guess = ?, letters = ?, lives = ?
             WHERE userid = ?"
        );
        $stmt->bind_param('ssis', $guess, $letters, $lives, $userid);
        $stmt->execute();
        return $this->get_user($userid);
    }

    public function reset_user(string $new_word, string $userid) : array {
        $conn = $this->connect();
        $new_guess = str_repeat("_", strlen($new_word));
        $stmt = $conn->prepare(
            "UPDATE games
             SET guess = ?,  word = ?, lives = DEFAULT, letters = DEFAULT
             WHERE userid = ?"
        );
        $stmt->bind_param('sss', $new_guess, $new_word, $userid);
        $stmt->execute();
        return $this->get_user($userid);
    }
}
