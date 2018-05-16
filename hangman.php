<?php

/**
 * Hagman class extends the DataModel by implementing
 * the logic of the game around the database connections,
 * updating the state based on a given user action.
 */
class Hangman extends DataModel {

    function __construct(string $userid, array $db_setup) {
        // set up db connections.
        list($db_server, $db_user, $db_pwd, $db_name) = $db_setup;
        parent::__construct($db_server, $db_user, $db_pwd, $db_name);
        // get the user of this game. this could return empty if they are not playing.
        $this->userid = $userid;
        $this->gamestate = $this->get_user($userid);

        if ($this->gamestate) {
            $has_won = $this->gamestate['word'] === $this->gamestate['guess'];
            $has_lost = $this->gamestate['lives'] <= 0;
            $this->finished = ($has_won || $has_lost);
        } else {
            // its finished if it aint started.
            $this->finished = true;
        }
    }

    public function new_game(string $new_word) : array {
        // is user isnt defined in our db make a new entry.
        if (!$this->gamestate) {
            $this->gamestate = $this->new_user($new_word, $this->userid);
        } else {
            $this->gamestate = $this->reset_user($new_word, $this->userid);
        }
        return $this->gamestate;
    }


    public function make_move(string $char) : array {
        $gamestate = $this->gamestate;
        if (strpos($gamestate["letters"], $char) !== false) {
            // nothing will change they have used that letter before.
            // printf("Nothing updated\n");
            return $gamestate;
        }
        $letters = $gamestate["letters"] . $char;
        $word = $gamestate["word"];
        $guess = $this->update_guess($word, $gamestate["guess"], $char);
        if ($guess === $word) {
            $this->finished = true;
        }
        $lives = $gamestate["lives"];
        if ($guess === $gamestate["guess"]) {
            $lives--;
            if ($lives <= 0) {
                $this->finished = true;
            }
        }
        $this->gamestate = $this->update_user($this->userid, [$guess, $letters, $lives]);
        return $this->gamestate;
    }

    public function terminal_state() {
        return $this->finished;
    }

    private function update_guess(string $word, string $guess, string $char) : string {
        for ($i = 0; $i < strlen($word); $i++){
            if ($char === $word[$i]) {
                $guess[$i] = $char;
            }
        }
        return $guess;
    }
}