<?php
include("config.php");

/* 測試用
echo "";
echo "<br>";
echo "token : " . $tokenKey . "<br>";
echo "repoLink : " . $repoLink . "<br>";
echo "<br>";
echo "測試 http://DNS/webhook_test.php?token=";
echo "<br>";
echo "<br>";
*/


//取得ip和token
$client_token = $_GET['token'];
$client_ip = $_SERVER['REMOTE_ADDR'];

//取得config
//自定義token
$access_token = $tokenKey;
//允許的ip，這邊是gitlab的ip，但也可以用於自訂
$access_ip = array('192.168.0.1','223.137.201.215','61.230.3.61');

//確認路徑
$_currDir = dirname(__FILE__);
$_logDir = $_currDir.'/log';
$_logPath = $_logDir.'/'."latestLog.log";
if (!file_exists($_logDir)) {
  mkdir($_logDir,0777,true);
  shell_exec("cd $_logDir && echo '' > latestLog.log");
}
else {
  //判斷該log內容是否檔案過大(byte)，過大(>5MB)就挪至備份區，並將檔案內容清空
  if (filesize($_logPath)>5000000) {

      copy($_logPath, $_logDir."/".date("Y-m-d H:i:s"));
      file_put_contents($_logPath, " ");
  }
}

//
//開啟目錄下的hooks.log文件，注意文件可讀寫的權限，因為是php開啟該文件，故和人創建該php文件的權限，就等於開該文件的權限
$fs = fopen($_logPath, 'a');
fwrite($fs, '================ Update Start ==============='.PHP_EOL.PHP_EOL);

fwrite($fs, 'Request on ['.date("Y-m-d H:i:s").'] from ['.$client_ip.']'.PHP_EOL);

//驗證
// verifyInfo($client_token,$client_ip);

//驗證token
if ($client_token !== $access_token){
  echo "error 403";
  fwrite($fs, "Invalid token [{$client_token}]".PHP_EOL);
  exit(0);
}

//
//判斷client ip是否為子網路ip
if(isSubnet($client_ip)){
}
else{
  //驗證外部ip
  if ( !in_array($client_ip, $access_ip))
  {
    echo "error 503";
    fwrite($fs, "Invalid ip [{$client_ip}]".PHP_EOL);
    exit(0);
  }
}

//取得gitlab的hooks訊息...
$json = file_get_contents('php://input');
$data = json_decode($json, true);
//寫入gitlab的訊息
fwrite($fs, 'Data : '.print_r($data, true).PHP_EOL);

//執行指令 or shell命令，並將return msg寫進日誌
//git fetch -q --all
$output = shell_exec("cd $_repoPath && git fetch -q --all");
fwrite($fs, 'Info : '. $output.PHP_EOL);

fwrite($fs,PHP_EOL. '================ Update End ==============='.PHP_EOL.PHP_EOL);

$fs and fclose($fs);


?>


<?php
//子網路驗證
function isSubnet($ip = NULL)
{
  $remoteAddr = (!isset($_SERVER['HTTP_X_FORWARDED_FOR']) && isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : NULL);
  $ip = strToLower(is_null($ip) ? $remoteAddr : $ip);

  $part = explode('.', $ip);
  // 10.0.0.0/8   Private network
  // 127.0.0.0/8  Loopback
  // 169.254.0.0/16 & ::1  Link-Local
  // 172.16.0.0/12  Private network
  // 192.168.0.0/16  Private network
  if (count($part) === 4 && ($part[0] === '10' || $part[0] === '127' || ($part[0] === '172' && $part[1] < 16 && $part[1] > 31)
  || ($part[0] === '169' && $part[1] === '254') || ($part[0] === '192' && $part[1] === '168'))
  ) {

    return TRUE;

  }

  return FALSE;
}

?>
