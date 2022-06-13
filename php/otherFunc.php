<?php
header('Content-Type: text/html; charset=utf-8');

function IsErr()
{
    $filName = $_REQUEST['csv'];
    $data_path = "../database/" . $filName;
    if ($filName == null)
        return 'not play the function';

    $fp = fopen($data_path, "r") or die("파일을 열 수 없습니다！");
    
    $msg = "";
    $lineNum = 0;
    $count = 0;
    while (!feof($fp)) {
        $line = fgets($fp); // ''은 공백이고 false는 마지막임
        $lineNum++;
        if ($line === false || strpos($line, '**') !== false)
            continue;

        $line = preg_replace('/\r\n|\r|\n/', '', $line);
        if ($line === "")
            continue;

        $fWord = substr($line, 0, 1);
        if (!is_numeric($fWord)) { // 숫자인지 체크
            $count++;
            $msg .= "line(" . $lineNum . ") : " . $line . "\n";
        }
    }
    if ($msg == '')
        $msg = "not found a error";

    return "오류내용(". $count . ") - ".$filName."\n".$msg;
}

function getSearchWord()
{
    $filName = $_REQUEST['csv'];
    $search_data = $_REQUEST['word']; // searchData 검색할 단어
    $href = $_REQUEST['href'];
    $data_path = "../database/" . $filName;
    if ($filName == null)
        return 'not find file';

    $fp = fopen($data_path, "r") or die("파일을 열 수 없습니다！");

    $msg = "";
    $count = 0; // 찾은 개수
    $len = -1;
    $row = 0;

    while (!feof($fp)) {
        $line = fgets($fp); // ''은 공백이고 false는 마지막임
        $currentlen = mb_strlen($line, "UTF-8");
        $len += $currentlen; // 총 len
        $row++;

         if ($line === '')
            continue;

        if (strpos($line, '**') !== false) {
            continue;
         }
         // 일치 단어가 있을 때
        if (strpos($line, $search_data) !== false) {
            $count++;
            
            if($href === "true")                     
                $msg .= '<a href="modify.html?'.$filName.'?'.$row.'?'.($len-$currentlen+1).'?'.$len.'">'.$line.'</a>';
            else
                $msg .= $line;
        }
    }
    if ($msg == '')
        $msg = "not found the word";

    fclose($fp);
    return '<b>찾은내용('.$count.')- '.$filName."</b> \n".$msg;
}



