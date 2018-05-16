<?php
require_once("../../config.php");
require_once("../../portmanteau.php");
$portman = new Portmanteau(ROOT_PATH . "words.txt");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hangmanteau</title>
    <link rel="stylesheet" href="static/base.css">
</head>
<body>
    <header>
        <h1>Hangmanteau <?php echo "\u{1F4A9}" ?></h1>
        <p>(A game of Hangman with only made-up portmanteau words)</p>
     </header>
    <main>
        <div class="demo">
            <div class="top-text">
                <h1 id="game-word"></h1>
                <a id="restart" href="/hangmanteau/">Play Again?</a>
            </div>
            <div id="game-image"></div>
            <div class="letters">
                <?php foreach(range("a", "z") as $letter) {
                    echo '<div class="letter">' . $letter . '</div>';
                } ?>
            </div>
        </div>
    </main>
    <script type="text/javascript">
        "use strict";

        var lives = 7,
            word = "<?php echo $portman->make_new_word() ?>",
            guessed = Array(word.length+1).join("_"),
            gameImage = document.getElementById("game-image"),
            gameWord = document.getElementById("game-word");

        function updateState() {
            if (lives > 0) {
                var char = this.textContent;
                if (word.includes(char)) {
                    gameWord.textContent = addLetter(char);
                } else {
                    lives -= 1;
                    gameImage.style.backgroundImage = (
                        "url('static/images/life"+ lives +".png')");
                }
                this.className += " hidden";
                this.removeEventListener("click", updateState);
                checkOutcome(guessed == word ? true : false);
            }
        }

        function addLetter(char) {
            var guess = "";
            for (var i = 0; i < word.length; i++) {
                guess += char == word[i] ? char : guessed[i];
            }
            guessed = guess;
            return guess;
        }

        function checkOutcome(hasWon) {
            if (hasWon) {
                gameWord.style.color = "#00C853";
                gameImage.style.backgroundImage = "url('static/images/youwin.png')";
                showRestart();
            } else if (lives <= 0) {
                gameWord.style.color = "#FF1744";
                gameWord.textContent = word;
                showRestart();
            }
        }

        function showRestart() {
            var res = document.getElementById("restart");
            res.style.display = "block";
        }

        document.addEventListener("DOMContentLoaded", function() {
            var letters = document.getElementsByClassName('letter');
            for (var i = 0; i < letters.length; i++) {
                letters[i].addEventListener("click", updateState);
            }
            gameWord.textContent = guessed;
            console.log(word); // for degugging atm.
        });
    </script>
</body>
</html>
