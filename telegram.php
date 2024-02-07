<?php
$token = '6479866632:AAGPwWNRmHJF97Ww-U87tLUYoegUUi6oJxY';

$channel_id = '6479866632';
 
// Function to make API requests
function apiRequest($method, $params = []) {
    global $token;
    $url = "https://api.telegram.org/bot$token/$method";
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

// Function to send a message to Telegram
function sendMessageToTelegram($message) {
    global $token, $channel_id;
    $telegramUrl = "https://api.telegram.org/bot$token/sendMessage";
    $data = http_build_query([
        'chat_id' => $channel_id,
        'text' => $message,
    ]);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $telegramUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "Form submitted!<br>"; // Debug statement

    $messageToSend = $_POST["message"];
    echo "Message to send: $messageToSend<br>"; // Debug statement

    // Send the message to Telegram
    $response = sendMessageToTelegram($messageToSend);
    var_dump($response); // Debug statement
}
// Fetch updates (messages) from the channel
$response = apiRequest("getUpdates", ["chat_id" => $channel_id]);

// Process and display messages
$messages = [];
if ($response["ok"]) {
    $messages = $response["result"];
} else {
    echo "Error fetching messages: " . $response["description"];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Telegram Messages</title>
</head>
<body>
    <h1>Telegram Messages</h1>

    <h2>Incoming and Outgoing Messages</h2>
    <ul>
        <?php foreach ($messages as $message) {
            if (isset($message["message"]["text"]) && isset($message["message"]["from"]["first_name"])) {
                $text = $message["message"]["text"];
                $sender = $message["message"]["from"]["first_name"];
                $direction = $message["message"]["chat"]["id"] == $channel_id ? "Outgoing" : "Incoming";
                $timestamp = date("Y-m-d H:i:s", $message["message"]["date"]);
                echo "<li>$direction message from $sender ($timestamp): $text</li>";
            } else {
                echo "<li>Error: Unable to parse message.</li>";
            }
        }?>
    </ul>

    <h2>Send Message to Telegram</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="message">Enter your message:</label><br>
        <textarea id="message" name="message" rows="4" cols="50"></textarea><br>
        <input type="submit" value="Send Message">
    </form>
</body>
</html>