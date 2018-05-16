<?php namespace responder;
require_once('config.php');
require_once('model.php');
require_once('portmanteau.php');
require_once('hangman.php');
require_once('templates.php');


function generate_response(array $input) : array {

    $message_data = $input['entry'][0]['messaging'][0];
    $userid = $message_data['sender']['id'];
    // set initial generic response varibles.
    $response = ['recipient' => ["id" => $userid]];

    // just to cover everthing for now. needs refactoring.
    if (isset($message_data['postback'])) {
        $data = $message_data['postback']['payload'];
        return proccess_button($userid, $response, $data);
    }

    $message = $message_data['message'];
    $message_text = trim($message['text']);

    if (isset($message['quick_reply'])) {
        $data = $message['quick_reply']['payload'];
        return proccess_button($userid, $response, $data);
    }

    if (strlen($message_text) == 1 && ctype_alpha($message_text)) {
        // init game.
        $game = new \Hangman($userid, DB_CONFIG);
        // user has sent a character.
        if ($game->terminal_state() == true) {
            // they are not playing a game anymore send them basic options and a quote.
            $response['message']['text'] = "No game in session. \u{1F4A9}";
            return quick_reply($response);
        }
        $char = strtolower($message_text);
        if (strpos($game->gamestate['letters'], $char) !== false) {
            $response['message']['text'] = "'".$char."'? You made that move already right? \u{1F4A9}";
            return $response;
        }
        $result = $game->make_move($char);
        return current_gameframe($response, $result);
    }

    // they didnt post anything related.
    $response['message']['text'] = "I am just a game to be honest \u{1F4A9}";
    return quick_reply($response);
}

function proccess_button(string $userid, array &$response, string $data) : array {
    switch ($data) {
    case 'NEW_GAME':
        echo ROOT_PATH . 'words.txt' . "\n";
        $porter = new \Portmanteau(ROOT_PATH . 'words.txt');
        $new_word = $porter->make_new_word();
        // init game.
        $game = new \Hangman($userid, DB_CONFIG);
        $gamestate = $game->new_game($new_word);
        $response = current_gameframe($response, $gamestate);
        break;
    case 'ABOUT':
        global $ABOUT_TEXT;
        $response['message'] = new_button_tmp($ABOUT_TEXT, "New game", "How to?", "About");
        break;
    case 'HOW_TO?':
        global $HOW_TEXT;
        $response['message'] = new_button_tmp($HOW_TEXT, "New game", "How to?", "About");
        break;
    default:
        $response['message']['text'] = "I literally have no clue.  \u{1F4A9}";
        break;
    }
    return $response;
}

function current_gameframe(array $response, array $gamestate) : array {
    global $IMAGE_ROOT;
    $use_buttons = false;
    if ($gamestate['word'] === $gamestate['guess']) {
        // Won
        $image = "youwin.png";
        $title_text = $gamestate['word'];
        $subtitle_text = "Winner!";
        $use_buttons = true;
    } else if ($gamestate['lives'] <= 0) {
        // lost
        $image = "life" . $gamestate['lives'] . ".png";
        $title_text = $gamestate['word'];
        $subtitle_text = "Game over.";
        $use_buttons = true;
    } else {
        // still playing.
        $image = "life" . $gamestate['lives'] . ".png";
        $title_text = implode(" ", str_split($gamestate['guess']));
        $subtitle_text = implode(" ", str_split($gamestate['letters']));
    }
    $response['message'] = [
        'attachment' => [
            'type' => 'template',
            'payload' => [
                'template_type' => 'generic',
                'image_aspect_ratio' => 'square',
                'elements' => [
                    [
                        'title' => $title_text,
                        'subtitle' => $subtitle_text,
                        'image_url' => $IMAGE_ROOT .  $image
                    ]
                ]
            ]
        ]
    ];
    if ($use_buttons) {
        $response['message']['attachment']['payload']['elements'][0]['buttons'] = [
            [
                'type' => 'postback',
                'title' => 'New Game',
                'payload' => 'NEW_GAME'
            ]
        ];
    }
    return $response;
}
