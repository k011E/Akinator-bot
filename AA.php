<?php
/**
* 
*/
class Akinator
{
	function searchUser($id){
		global $db;
		if($db->query("SELECT `id` FROM `users` WHERE `id_user`='".$id."'")->num_rows==0){
			return false;
		}else{
			return true;
		}
	}

	function newSession($id){
		global $db;
		$data = json_decode(file_get_contents("http://api-ru1.akinator.com/ws/new_session?partner=1"));
		$arr['session'] = $data->parameters->identification->session;
    	$arr['sig'] = $data->parameters->identification->signature;
    	$arr['question'] = $data->parameters->step_information->question;
    	$arr['step'] = $data->parameters->step_information->step;
    	$db->query("INSERT INTO `users` SET `id_user`='".$id."', `session`='".$arr['session']."', `signature`='".$arr['sig']."', `step`='".$arr['step']."'");
    	return $arr;
	}

	function answer($id, $session, $sig, $answer, $step) {
		global $db;
	    $data = json_decode(file_get_contents("http://api-ru1.akinator.com/ws/answer?session=$session&signature=$sig&step=$step&answer=$answer"));
	    $arr['session'] = $session;
	    $arr['sig']  = $sig;
	    $arr['question'] = $data->parameters->question;
	    $arr['step'] = $data->parameters->step;
	    $arr['progress'] = round(intval($data->parameters->progression));
	    $db->query("UPDATE `users` SET `step`='".$arr['step']."', `progress`='".$arr['progress']."' WHERE `id_user`='".$id."'");
	    return $arr;
	}

	function lists($id, $session, $sig, $step)
	{
    	global $db;
	    $data = json_decode(file_get_contents("http://api-ru1.akinator.com/ws/list?session=$session&signature=$sig&step=$step&size=2&max_pic_width=246&max_pic_height=299&pref_photos=OK-FR&mode_question=0"));
	    $arr['name'] = $data->parameters->elements[0]->element->name;
	    $arr['description'] = $data->parameters->elements[0]->element->description;
	    $arr['image'] = $data->parameters->elements[0]->element->picture_path;
	    $db->query("DELETE FROM `users` WHERE `id_user`='".$id."'");
	    return $arr;
	}


	// Проверяет присланное сообщение на валидность
	/*function validMessage($message){
		if(is_numeric($message)){
			return true;
		}else{
			$message = strtolower($message);
		}
	}*/

	// Перезапускает игровую сессию пользователя
	function resetSession($id){

	}
}
?>