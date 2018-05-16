<?php
require_once('../../config.php');

// respond to any challenges from facbook.
if (isset($_REQUEST['hub_challenge'])) {

    $challenge = $_REQUEST['hub_challenge'];
    $hub_verify_token = $_REQUEST['hub_verify_token'];

    if ($hub_verify_token == $verify_token) {
        echo $challenge;
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SERVER["CONTENT_TYPE"] == "application/json") {

    // check x hub signature against app secret hash to see validate requst or die.
    list($algo, $hash) = explode('=', $_SERVER['HTTP_X_HUB_SIGNATURE'], 2);

    $payload = file_get_contents('php://input');

    $payload_hash = hash_hmac($algo, $payload, $app_secret);

    if (!hash_equals($payload_hash, $hash)) {
        die ("Santa sold some bad hash");
    }

    // read and process response.
    $input = json_decode($payload, true);

    if(!empty($input['entry'][0]['messaging'][0]['message']) || !empty($input['entry'][0]['messaging'][0]['postback'])) {

        if ($DEBUGGING) {
            file_put_contents('../../fb_response.txt', json_encode($input) . PHP_EOL, FILE_APPEND);
        }
        // just require this here because we dont need it if we dont get here.
        require_once('../../responder.php');

        try {
    	    // generate all information to return back to the user.
            $responseJSON = json_encode(\responder\generate_response($input, $DB_CONFIG));

        } catch (Exception $e) {

	        file_put_contents('../../err_log.txt', $e . PHP_EOL, FILE_APPEND);
            $responseJSON = json_encode([
                'recipient' => ["id" => $userid],
                'message' => ['text' => 'We had a little problem here...']
            ], true);
        }
    	// Make the response with cUrl.
    	$ch = curl_init($api_url);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $responseJSON);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        // Finlly send the response.
        $result = curl_exec($ch);
        // close the connection.
        curl_close($ch);
        exit;
    }
}


// if we visit this page in the browser or gret request...
echo "Is this the real life... \u{1F4A9}\n";
