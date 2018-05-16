<?php

/*
Portmanteau generation code follows closely from a specification of a Udacity
question from the program design course run by Peter Norvig. The only diffence
being that, in this case, words are scored and then selected by weighted chance
for use within a game.

The rules of what constitutes a portmanteau in this case are as follows:
A portmanteau consists of a two overlapping words where a prefix and suffix are
equal to each other. for example, 'cooper' and 'operation' can form cooperation
where the word is consitent of the parts 'co' + 'oper' + 'ation'.
*/

class Portmanteau {

    function __construct(string $words_file) {
        // Init with path to a words file your want to use for
        // generating portmanteaus from.
        if (!file_exists($words_file)) {
            die("ERROR: Words file not found.\n");
        }
        $handle = fopen(($words_file), "r");
        if (!$handle) {
            die("ERROR: Words file empty.\n");
        }
        // Read words file into an array.
        $this->words = file($words_file, FILE_IGNORE_NEW_LINES);
        fclose($handle);
    }

    public function make_new_word() : string {
        $portmans = [];
        // $words = $this->random_subset($this->words, 200);
        $words = $this->words;
        $ends = $this->compute_ends($words);
        foreach ($words as $word) {
            foreach($this->split_chunks($word) as list($start, $mid)) {
                if (array_key_exists($mid, $ends)) {
                    foreach($ends[$mid] as $end) {
                        if ($mid . $end !== $word) {
                            $new_word = $start . $mid . $end;
                            if (!array_key_exists($new_word, $portmans)) {
                                $score = $this->word_score($start, $mid, $end);
                                $portmans[] = [$new_word, $score];
                            }
                        }
                    }
                }
            }
        }
        return $this->weighted_choice($portmans);
    }

    private function word_score(string $start, string $middle, string $end) : float {
        $s = strlen($start);
        $m = strlen($middle);
        $e = strlen($end);
        $T = $s + $m + $e;
        return abs($T - abs($s - $T/4) - abs($m - $T/2) - abs($e - $T/4));
    }

    private function compute_ends(array $words) : array {
        $ends = [];
        foreach ($words as $word) {
            foreach ($this->split_chunks($word) as list($mid, $end)) {
                if (!isset($ends[$mid])) {
                    $ends[$mid] = [];
                }
                $ends[$mid][] = $end;
            }
        }
        return $ends;
    }

    private function split_chunks(string $word) : array {
        $splits = [];
        for ($i = 1; $i < strlen($word) - 1; $i++) {
            $split = [substr($word, 0, $i), substr($word, $i)];
            $splits[] = $split;
        }
        return $splits;
    }

    function weighted_choice(array $choices) : string {
        // Weighted_choice algorithm will return a randomonly selected word from a
        // given list of portmanteaus with a distribution reletive to their wordscore.
        // ref links: Python algo: https://goo.gl/zR1SxP, Go Lib: https://goo.gl/d3bz5E
        $weights_sum = 0;
        foreach ($choices as list($choice, $weight)) {
            $weights_sum += $weight;
        }
        if ($weights_sum <= 0) {
            return "WORD_ERROR";
        }
        $index = rand(0, $weights_sum);
        foreach ($choices as list($choice, $weight)) {
            $index -= $weight;
            if ($index <= 0) {
                return $choice;
            }
        }
    }

    function random_subset(array $words, int $number) : array {
        $rand_indexes = array_rand($words, $number);
        $subset = [];
        foreach ($rand_indexes as $idx) {
            $subset[] = $words[$idx];
        }
        return $subset;
    }
}