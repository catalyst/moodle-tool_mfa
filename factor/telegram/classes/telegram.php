<?php


namespace factor_telegram;


class telegram {

    private $token;

    public function __construct($token) {
        $this->token = $token;
    }

    public function send_message($userid, $text) {
        $path = "https://api.telegram.org/bot".$this->token;
        file_get_contents($path."/sendmessage?chat_id=".$userid."&text=".$text);
    }
}