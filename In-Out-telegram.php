<?php


$token = '6479866632:AAGPwWNRmHJF97Ww-U87tLUYoegUUi6oJxY';

$channel_id = '6522269794';
 
// Function to make API requests
function apiRequest($method, $params = []) {
    $url = "https://api.telegram.org/bot" . $GLOBALS['token'] . "/" . $method;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
    $response = curl_exec($curl);
    if (!$response) {
        exit("Error: " . curl_error($curl));
    }
    curl_close($curl);
    return json_decode($response, true);
}

// Fetch updates (messages) from the channel
$response = apiRequest("getUpdates", ["chat_id" => $channel_id]);

// Process and display messages
if ($response["ok"]) {
    $messages = $response["result"];
    foreach ($messages as $message) {
        // Check if the keys exist before accessing them
        if (isset($message["message"]["text"]) && isset($message["message"]["from"]["first_name"])) {
            $text = $message["message"]["text"];
            $sender = $message["message"]["from"]["first_name"];
            $direction = $message["message"]["chat"]["id"] == $channel_id ? "Outgoing" : "Incoming";
            echo "$direction message from $sender: $text\n";
        } else {
            echo "Error: Unable to parse message.\n";
        }
    }
} else {
    echo "Error fetching messages: " . $response["description"];
}

?>