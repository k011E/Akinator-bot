<?php
if(!isset($_REQUEST)){
	return ;
	exit();
}
include_once($_SERVER["DOCUMENT_ROOT"].'/API.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/db_akinator.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/AA.php');
$data = json_decode(file_get_contents("php://input"));
$VK = new API('089d42df638b4966278e37cae6d0fd0a30009ff9ec1e90bd4cb993f682d25163c1bd1144e7214038ab332', 'dsadasd234321', '33a98275', '149252310', $data);
$AA = new Akinator();
if($VK->getType() == 'confirmation'){
	echo $VK->getConfirmationCode();
	exit();
}elseif($VK->getType() == 'message_new'){
		if(!$AA->searchUser($data->object->user_id) OR mb_stripos($data->object->body, '6', 0) !== false){
			$db->query("DELETE FROM `users` WHERE `id_user`='".$data->object->user_id."'");
			$array = $AA->newSession($data->object->user_id);
			$VK->sendMessage('Вопрос №'.($array['step']+1).'
				➖➖➖➖➖
				'.$array['question'].'
				➖➖➖➖➖
				1) Да
				2) Нет
				3) Не знаю
				4) Возможно, частично
				5) Скорее нет, не совсем
				6) Начать заново');
			$VK->sendMessage('Пожалуйста отвечайте цифрами (бот пока не распознает текстовых ответов)');
		}else{
			$user = $db->query("SELECT * FROM `users` WHERE `id_user`='".$data->object->user_id."'")->fetch_assoc();
			if($user['excl']==1){
				###########################################
				####   ХЗ ЧТО ЭТО, НО НУЖНО ДОПИСАТЬ   ####
				###########################################
			}else{
				$array = $AA->answer($data->object->user_id, $user['session'], $user['signature'], $VK->searchAnswer(), $user['step']);
				$user = $db->query("SELECT * FROM `users` WHERE `id_user`='".$data->object->user_id."'")->fetch_assoc();
				if($user['progress']<99){
					$VK->sendMessage('Вопрос №'.($array['step']+1).'
					➖➖➖➖➖
					'.$array['question'].'
					➖➖➖➖➖
					1) Да
					2) Нет
					3) Не знаю
					4) Возможно, частично
					5) Скорее нет, не совсем
					6) Начать заново');
				}else{
					$array = $AA->lists($data->object->user_id, $user['session'], $user['signature'], $user['step']);
					$uploadServer = $VK->getMessagesUploadServer();
					$link = $uploadServer->response->upload_url;
    				$lala = "http://api-ru1.akinator.com/photo0/".$array['image'];
    				copy($lala, '1.jpg');
    				$img_path  = dirname(__FILE__).'/1.jpg'; 
    				$cfile = curl_file_create($img_path,'image/jpeg','1.jpg');
    				$curl=curl_init();
      				curl_setopt_array($curl, array(
      					CURLOPT_POST => true,
        				CURLOPT_RETURNTRANSFER => TRUE,
        				CURLOPT_URL => $link,
        				CURLOPT_POSTFIELDS => array("photo" => $cfile)
      				));
      				$resul_arr = json_decode(curl_exec($curl));
      				curl_close($curl);
      				$resul_photo = stripslashes($resul_arr->photo);
      				$ph = $VK->saveMessagesPhoto($resul_photo, 
      					$resul_arr->server, 
      					$resul_arr->hash);
      				if(isset($ph->error)){
      					$VK->sendMessage($ph->error->error_msg.'///'.$resul_arr->server);
      				}
      				$attach = 'photo'.$ph->response[0]->owner_id.'_'.$ph->response[0]->id;
					$VK->sendMessage('Я думаю... Это:
						'.$array['name'].' - '.$array['description'].'
						Хочешь сыграть ещё?', $attach);
					if(!$VK->checkSubscribe()){
						$VK->sendMessage('Не забудь на меня подписаться — https://vk.com/bot_akkinator :3
							1. Начать заново.');
					}
					unlink($_SERVER["DOCUMENT_ROOT"].'/1.jpg');
				}
			}
		}
}elseif($VK->getType() == 'group_leave'){
	$VK->sendMessage('Спасибо что был с нами, возвращайся 😔😔😔');
}elseif($VK->getType() == 'group_join'){
	$VK->sendMessage('Отлично, теперь мы можем сразиться👊');
}
echo "ok";
?>