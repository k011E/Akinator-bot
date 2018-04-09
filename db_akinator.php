<?php
$db = new mysqli('localhost', 'root', '', 'akinator');
if($db->connect_errno){
	die('Ошибка подключения к БД: '.$db->connect_error);
}
?>