<?php
/**
* API VK
* @author k011E (Александр Каплин)
*/
class API
{
	public $tocken;
	public $secret_code;
	public $confirmation_code;
	public $data;
	public $tocken_app = '089d42df638b4966278e37cae6d0fd0a30009ff9ec1e90bd4cb993f682d25163c1bd1144e7214038ab332';
	public $group_id;

	function __construct($tocken, $secret_code, $confirmation_code, $group_id, $data)
	{
		$this->tocken = $tocken;
		$this->secret_code = $secret_code;
		$this->confirmation_code = $confirmation_code;
		$this->data = $data;
		$this->group_id = $group_id;
	}

	function getType(){
		return $this->data->type;
	}

	function getConfirmationCode(){
		return $this->confirmation_code;
	}

	function checkSubscribe(){
		$sub = json_decode(file_get_contents("https://api.vk.com/method/groups.isMember?group_id=".$this->group_id."&user_id=".$this->data->object->user_id."&v=5.0&extended=1&access_token=".$this->tocken));
		if($sub->response->member == 0){
			return false;
		}else{
			return true;
		}
	}

	function sendMessage($text, $attachment = NULL){
		$request_params = array( 
	      'message' => $text, 
	      'user_id' => $this->data->object->user_id, 
	      'access_token' => $this->tocken, 
	      'v' => '5.0',
	      'attachment' => $attachment 
	    ); 
	    $get_params = http_build_query($request_params); 
		file_get_contents('https://api.vk.com/method/messages.send?'. $get_params); 
	}

	function searchTags(){
		$tag = false;
		if(mb_strripos($this->data->object->body, '!help', 0, "utf-8") !== false OR mb_strripos($this->data->object->body, '!помощь', 0, "utf-8") !== false){
			$tag = 1;
		}elseif(mb_strripos($this->data->object->body, '!top', 0, "utf-8") !== false OR mb_strripos($this->data->object->body, '!топ', 0, "utf-8") !== false){
			$tag = 2;
		}elseif(mb_strripos($this->data->object->body, '!genre', 0, "utf-8") !== false OR mb_strripos($this->data->object->body, '!жанр', 0, "utf-8") !== false){
			$tag = 3;
		}elseif(mb_strripos($this->data->object->body, 'прив', 0, "utf-8") !== false OR mb_strripos($this->data->object->body, 'здаро', 0, "utf-8") !== false OR mb_strripos($this->data->object->body, 'базарь', 0, "utf-8") !== false OR mb_strripos($this->data->object->body, 'здравствуй', 0, "utf-8") !== false){
			$tag = 4;
		}
		return $tag;
	}


	function searchAnswer(){
		global $db;
		global $AA;
		$answer = false;
		if(mb_stripos($this->data->object->body, '1', 0) !== false OR mb_stripos($this->data->object->body, 'да', 0) OR mb_stripos($this->data->object->body, 'da', 0)){
			$answer = 0;
		}elseif(mb_stripos($this->data->object->body, '2', 0) !== false OR mb_stripos($this->data->object->body, 'нет', 0) !== false OR mb_stripos($this->data->object->body, 'net', 0) !== false){
			$answer = 1;
		}elseif(mb_stripos($this->data->object->body, '3', 0) !== false){
			$answer = 2;
		}elseif(mb_stripos($this->data->object->body, '4', 0) !== false){
			$answer = 3;
		}elseif(mb_stripos($this->data->object->body, '5', 0) !== false){
			$answer = 4;
		}

		return $answer;
	}


	function getMessagesUploadServer(){
		$get = json_decode(file_get_contents('https://api.vk.com/method/photos.getMessagesUploadServer?access_token='.$this->tocken.'&v=5.69'));
		return $get;
	}

	function saveMessagesPhoto($photo, $server, $hash){
		$params = array('photo' => $photo,
			'server' => $server,
			'hash' => $hash,
			'access_token' => $this->tocken, 
			'v' => '5.67');
		$get_params = http_build_query($params);
		$photo = json_decode(file_get_contents('https://api.vk.com/method/photos.saveMessagesPhoto?'.$get_params));
		return $photo;
	}

	function getBody(){
		return $this->data->object->body;
	}

	function isAttachement(){
		if(isset($this->data->object->attachments[0]->type)){
			return true;
		}else{
			return false;
		}
	}
}
?>