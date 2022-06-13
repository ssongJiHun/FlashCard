<?php
header('Content-Type: text/html; charset=utf-8');

function saveJson()
{
    // $post_data = $_REQUEST['json'];
    // if ($post_data == null)
    //     return "error"; // 그냥 오류
    // $words = json_decode($post_data, true);
    $words = json_decode($_REQUEST['json'], true);

    $fileName = $_REQUEST['csv'];
    $data_path = "../database/" . $fileName;

    $fp = fopen($data_path, "r") or die("파일을 열 수 없습니다！");

    $index = 1;
    $msg = ""; // 저장될 내용
    // 비교할 데이터
    while (!feof($fp)) {
        $line = fgets($fp);
        if ($line === false)
            continue;

        // 새로운 테이블추가
        if (strpos($line, "**") !== false) {
            $msg .= $line;
            continue;
        }
        if (strpos($line, "^") === false)
            continue;

        $line = preg_replace('/\r\n|\r|\n/', '', $line);
        if ($line === "")
            continue;

        $split = explode("^", $line);

        $word = $words['n'.$index++]; // 찾아들어가기
        if($word !== null) {
            if($split[0] != $word['wrongCount'])
                $split[0] = $word['wrongCount'];

            if($split[1] != $word['question'])
                $split[1] = $word['question'];
            
            if($split[2] != $word['answer'])
                $split[2] = $word['answer'];
        }

        $msg .= $split[0] . "^" .$split[1]."^".$split[2]."\n";
    }

    fclose($fp);

    $fp = fopen($data_path, "w") or die("파일을 열 수 없습니다！");
    fwrite($fp, $msg);
    fclose($fp);

    return $fileName . "저장완료-study.html";
}
function saveString()
{
    $post_data = $_REQUEST['string'];
    if ($post_data == null)
        return "error"; // 그냥 오류

    $fileName = $_REQUEST['csv'];
    $data_path = "../database/" . $fileName;

    $fp = fopen($data_path, "w") or die("파일을 열 수 없습니다！");
    fwrite($fp, $post_data);
    fclose($fp);

    return $fileName . " 저장 완료-modify.html";
}

function addChapter()
{
    $input = $_REQUEST['input'];
    require 'readSelName.php';
    $conf = getConf();
    $input_explode = explode("/", $input);
    $input_explode = array('chaptername' => $input_explode[0], 'fileurl' => $input_explode[1], 'partition' => $input_explode[2], 'wrongfiliter' => $input_explode[3], 'mark' => $input_explode[4], 'subtitle' => $input_explode[5]);

    $isOverlap = false;
    for ($i = 1; $i < count($conf); $i++) {
        if ($input_explode['fileurl'] === $conf[$i]['fileurl'])
            $isOverlap = true;
    }
    if ($isOverlap)
        return "overlap";

    $fp = fopen("../conf.txt", "a") or die("파일을 열 수 없습니다！");
    fwrite($fp, "\r\n" . $input);
    fclose($fp);

    $newfp = fopen("../database/" . $input_explode['fileurl'], "w") or die("파일을 열 수 없습니다！");
    fwrite($newfp, "");
    fclose($newfp);
    return "conf추가 완료, 파일생성 완료";
}

function addBranch()
{
    $input = $_REQUEST['input'];
    if ($input == null | $input == '')
        return 'transmit error';

    $fileName = $_REQUEST['csi'];
    $data_path = "../database/" . $fileName;

    $fp = fopen($data_path, "a") or die("파일을 열 수 없습니다！");
    fwrite($fp, "\r\n" . "**" . $input);
    fclose($fp);
    return "추가완료.\n내용은 추가는 selbox를 선택하지 말고 +클릭";
}

function saveConfString()
{
    $input = $_REQUEST['input'];
    if ($input == null | $input == '')
        return 'transmit error';

    $data_path = "../conf.txt";

    $fp = fopen($data_path, "w") or die("파일을 열 수 없습니다！");
    fwrite($fp, $input);
    fclose($fp);
    return "저장완료";
}
