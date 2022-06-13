<?php
// 이 php는 form으로부터 직접적으로 전달
header('Content-Type: text/html; charset=utf-8');

$txtName = $_REQUEST['txtName']; // name으로 불러옴
$selected_index = intval($_REQUEST['selected_index']);
$content_data = $_REQUEST['content'];

// $txtName = "3.txt"; // name으로 불러옴
// $selected_index = "1";
// $content_data = "0^1111111111111111111111111111^!!!!!!!!!!!!!!!!!!!!!!";
$data_path = "../database/" . $txtName;

if($selected_index == null || $content_data == null) {
    echo '저장 실패';
    return;
}

$fp = fopen($data_path, "r") or die("파일을 열 수 없습니다！");

$n = 0; //선택하시오부터 시작
$msg = ""; // 저장될 내용
$endfocus = true;
while (!feof($fp)) {
    $line = fgets($fp); // ''은 공백이고 false는 마지막임
    if ($line === '')
        continue;

    if (strpos($line, "**") !== false || $line === false) { 
        if ($n == $selected_index) {
            $msg .= $content_data;
            $endfocus = false;
        }
        $n++;
    }
    $msg .= $line;
}

// 혹시모를 엔드포커스2
if($endfocus === true)
    $msg .= "\r\n".$content_data;
// 파일 닫기
fclose($fp);

$fp = fopen($data_path, "w") or die("파일을 열 수 없습니다！");
fwrite($fp, $msg);
fclose($fp);

echo ('저장완료');
?>