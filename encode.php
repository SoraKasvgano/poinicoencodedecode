<?php
require("1.php");
file_put_contents("1.txt", $_POST['data']); //作用是将POST接收到的名字叫data的textarea数据写入到1.txt
$encode = new Encoder('1.txt');
echo($encode->getEncode());