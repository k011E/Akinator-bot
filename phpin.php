<?php
$db = new mysqli("localhost", "root", "", "akinator"); // Connect DataBase
function ololo($string){
    return $db->real_escape_string(filter_var($string, FILTER_SANITIZE_STRING));
}
if(isset($_GET['add'])){
    $_GET['name'] = ololo($_GET['name']);
    $_GET['surname'] = ololo($_GET['surname']);
    $_GET['group'] = ololo($_GET['group']);
    $_GET['uz'] = ololo($_GET['uz']);
    if(empty($_GET['name']) || empty($_GET['surname']) || empty($_GET['group']) || empty($_GET['uz'])){
        exit();
    }else{
        $db->query("INSERT INTO `table` SET `name`='".$_GET['name']."', `surname`='".$_GET['surname']."', `group`='".$_GET['group']."', `uz`='".$_GET['uz']."'");
    }
}
?>