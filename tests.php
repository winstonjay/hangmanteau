<?php
require_once('config.php');
require_once('model.php');
require_once('portmanteau.php');
require_once('hangman.php');
require_once('responder.php');

/**
 * Provides a frame for all the responses we expect to get
 * containting text.
 */
$test_response = json_decode('{
    "entry":[{
        "id":"000000000000003",
        "time":1507994807315,
        "messaging":[{
            "sender":{"id":"200"},
            "recipient":{"id":"000000000000003"},
            "timestamp":1507994806590,
            "message":{
                "mid":"NA",
                "seq":"NA",
                "text":"__DEFAULT__"
            }
        }]
    }]
}', true);
/**
 * Provides a frame for all the quick responses we
 * expect to get.
 */
$test_quick_reply = json_decode('{
    "entry":[{
        "id":"000000000000003",
        "time":1507937849880,
        "messaging":[{
            "sender":{"id":"200"},
            "recipient":{"id":"000000000000003"},
            "timestamp":1507937849230,
            "message":{
                "quick_reply":{"payload":"NEW_GAME"},
                "mid":"NA",
                "seq":"NA",
                "text":"New Game"
            }
        }]
    }]
}', true);


$test_postback = json_decode('{
    "entry":[{
        "id":"000000000000003",
        "time":1508097381982,
        "messaging":[{
            "recipient":{"id":"000000000000003"},
            "timestamp":1508097381982,
            "sender":{"id":"200"},
            "postback":{
                "payload":"NEW_GAME",
                "title":"New Game"
            }
        }]
    }]
}', true);

function set_msg(array &$response, string $msg) {
    $response['entry'][0]['messaging'][0]['message']['text'] = $msg;
}

/**
 * test_hangman(): test use of Hangman class.
 * currently the much of the arguement checks are
 * left to the responder function. is this the best way
 * forward?
 */
function test_hangman() {

    // non numeric user id's should fail.
    try {
        $userid = "poop";
        $game = new Hangman($userid, DB_CONFIG);
    } catch (Exception $e) {
        assert($e->getMessage() == "Non numeric userid.\n");
    }
    // Initial case where user does not initially exist.
    $userid = "404";
    $game = new Hangman($userid, DB_CONFIG);

    // can only do this once.
    // assert($game->user == []);
    $gamestate = $game->new_game("halloween");
    assert($gamestate["guess"] == "_________");
    assert($gamestate["word"]  == "halloween");

    $gamestate = $game->make_move("e");
    $gamestate = $game->make_move("w");
    assert($gamestate["guess"] == "_____wee_");
    assert($gamestate["letters"] == "ew");
    assert($gamestate["lives"] == 7);
    $gamestate = $game->make_move("z");
    $gamestate = $game->make_move("y");
    $gamestate = $game->make_move("d");
    assert($gamestate["guess"] == "_____wee_");
    assert($gamestate["letters"] == "ewzyd");
    assert($gamestate["lives"] == 4);

    $gamestate = $game->make_move("d");
    $gamestate = $game->make_move("d");
    $gamestate = $game->make_move("d");
    assert($gamestate["guess"] == "_____wee_");
    assert($gamestate["letters"] == "ewzyd");
    assert($gamestate["lives"] == 4);

    $gamestate = $game->make_move("l");
    $gamestate = $game->make_move("o");
    $gamestate = $game->make_move("h");
    $gamestate = $game->make_move("a");
    assert($gamestate["guess"] == "hallowee_");
    assert($gamestate["letters"] == "ewzydloha");
    assert($gamestate["lives"] == 4);

    $gamestate = $game->make_move("m");
    $gamestate = $game->make_move("x");
    $gamestate = $game->make_move("n");
    assert($gamestate["guess"] == "halloween");
    assert($gamestate["letters"] == "ewzydlohamxn");
    assert($gamestate["lives"] == 2);
}



if (!count(debug_backtrace())) {

    test_hangman();

    // start a new game.
    $response2 =  \responder\generate_response($test_quick_reply);
    var_dump($response2);

    set_msg($test_response, "a");
    $response = \responder\generate_response($test_response);
    set_msg($test_response, "e");
    $response =  \responder\generate_response($test_response);
    set_msg($test_response, "i");
    $response =  \responder\generate_response($test_response);
    var_dump($response);

    // echo ROOT_PATH . "\n";

    // test_responder();
    printf("-\nALl tests passed\n-\n");
}

