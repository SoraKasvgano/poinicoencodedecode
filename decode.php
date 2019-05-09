<?php
require("2.php");
file_put_contents("1.txt", $_POST['data']); //作用是将POST接收到的名字叫data的textarea数据写入到1.txt
$decode = new Decoder('1.txt');
echo($decode->getDecode());