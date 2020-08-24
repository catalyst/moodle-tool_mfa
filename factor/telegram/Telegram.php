<?php
$path = "https://api.telegram.org/bot1204730014:AAEMpzxxRPRnQTLZ9eCMNgCAmBd0d9pX8iQ";

$update = json_decode(file_get_contents("php://input"), TRUE);

$chatId = 1292580991;
$message = $update["message"]["text"];
$text = "hello";
file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=".$text);
?>