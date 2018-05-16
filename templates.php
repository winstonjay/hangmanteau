<?php

$HOW_TEXT = <<<EOT
\u{1F4A9} How to:
You have 7 lives to guess the word.
To make a guess just type a character (currently you cannot guess whole words).
To start a new game, send a message then click the 'New game' button.
EOT;

$ABOUT_TEXT = <<<EOT
\u{1F4A9} About:
Hangmanteau just like the orginal hangman but with only made-up portmanteau words. These words are made up of two real words overlap at least 2 characters. So, for example 'vodkazoo' could be a valid word here because it is 'vodka' + 'kazoo', combined as 'vod' + 'ka' + 'zoo'.

This game is currently under active development and any feedback would be appreciated.
EOT;

function new_button_tmp(string $text, string ...$labels) {
    $buttons = [];
    foreach ($labels as $label) {
        $buttons[] = [
            'type' => 'postback',
            'title' => $label,
            'payload' => str_replace(" ", "_",strtoupper($label))
        ];
    }
    $generic_btn = [
        'attachment' => [
            'type' => 'template',
            'payload' => [
                'template_type' => 'button',
                'text' => $text,
                'buttons' => $buttons
            ]
        ]
    ];
    return $generic_btn;
}

function quick_reply(array &$response) : array {
    $response['message']['quick_replies'] = quick_replies("New Game", "How to?", "About");
    return $response;
}

function quick_replies(string ...$titles) : array {
    $quick_replies = [];
    foreach ($titles as $title) {
        $quick_replies[] = [
            'content_type' => 'text',
            'title' => $title,
            'payload' => str_replace(" ", "_",strtoupper($title)),
        ];
    }
    return $quick_replies;
}