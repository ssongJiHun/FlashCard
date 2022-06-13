<?php
header('Content-Type: text/html; charset=utf-8');

function getStudyJson()
{
    // 데이터 받기
    $chapterSelBoxVal = $_REQUEST['csv'];
    $partitionVal = intval($_REQUEST['pv']);
    $branchSelBox = intval($_REQUEST['bsv']);
    $wrongFiliter = intval($_REQUEST['wf']);
    $isRandom = $_REQUEST['ir'];
    $isAsc = $_REQUEST['ia'];
    $test = $_REQUEST['test'];

    // $chapterSelBoxVal = "1.txt";
    // $partitionVal = 3;
    // $branchSelBox = 20;
    // $wrongFiliter = 0;
    // $isRandom = "false";

    $data_path = "../database/" . $chapterSelBoxVal;

    $fp = fopen($data_path, "r") or die("파일을 열 수 없습니다！");
    // 파일 내용 출력
    // 목차 리스트
    $dataTable = array(); // chaperlist(ex:1day~end)
    $dataTable[0] = null;
    $words = array();
    $chapterIndex = 0;
    $index = 1;
    // 1day로 불러오기
    while (!feof($fp)) {
        $line = fgets($fp);
        if ($line === false)
            continue;

        // 새로운 테이블추가
        if (strpos($line, "**") !== false) {
            array_push($dataTable, array());
            $chapterIndex++;
            continue;
        }
        if (strpos($line, "^") === false)
            continue;
        $line = preg_replace('/\r\n|\r|\n/', '', $line);
        if ($line === "")
            continue;

        $split = explode("^", $line);

        $arr = array('wrongCount' => $split[0], 'question' => $split[1], 'answer' => $split[2]);
        if($isAsc === 'false' && $test === 'false')
            $dataTable[$chapterIndex]['n'.$index++] = $arr;
        else
            $words['n'.$index++] = $arr;
    }
    fclose($fp);
    // 전체 단어 테스트
    if ($test === "true") {
        $res = array();
        $i = 0;
        while($i < 30) {
            $selected = array_rand($words);
            $m = intval($words[$selected]['wrongCount']);
            if ($m > 900 || $m !== 0) 
                continue;
            $res[$selected] = $words[$selected];
            unset($words[$selected]);
            $i++;
        }
        // output
        return output($res);
    }

    // 오름차순
    if ($isAsc === 'true') 
        $dataTable = getAscArray($words); // a~z 까지 테이블로 만들어줌

    // 분할요청
    $words = null;
    $words = getPartitionDataTable($dataTable, $partitionVal, $branchSelBox); // 앞에서 선택한 단위기준으로 분할되고 단어끼리만 합쳐 논 
    
    if ($words === null) 
        return "data is partition_error";

    // precondition
    if ($isRandom === "true") {
        $arr = array();
        foreach ($words as $n => $word) { // except the guideline;
            if (intval($word['wrongCount']) > 900)
                unset($words[$n]);
            else 
                $words[$n]['wrongCount'] = 0;
        }
        $words= randomArr($words); // suffle 쓰면 인덱스 값이 바꿔서 직접 바꿔줘야함
    } else if ($wrongFiliter > 0) { //    global $wrong_standard; 몇개체크 이상부터 
        foreach ($words as $n => $word) {
            $m = intval($word['wrongCount']);
            if ($m < $wrongFiliter || $m > 900) {
                unset($words[$n]);
            }
        }
    }
    return output($words);
   
}
function output($words)
{
    // output
    if (count($words) > 0)
        return json_encode($words, JSON_UNESCAPED_UNICODE);
    else
        return "학습 할 데이터가 없습니다.";
}

function getAscArray($words) {
    $questions = array();
    foreach ($words as $n=>$word) {
        if(intval($word['wrongCount']) > 900 ) {
            unset($words[$n]);
            continue;
       }
        $questions[$n] = strtolower($word['question']);
    }
    array_multisort($questions, SORT_ASC, $words);
    // 챕터화 a-z
    $dataTable = array();
    $dataTable[0] = null;
    for($i = 1; $i <= 26; $i++)
        array_push($dataTable, array());

    foreach ($words as $n => $word) {
        if($word == null)
            continue;
        $chr = ord(substr($word['question'], 0, 1)) -96;
        if($chr <= 0) // 오류처리
            $chr += 32;
        $dataTable[$chr][$n] = $word;
    }
    return $dataTable;
}

function getPartitionDataTable($arr, $partitionVal, $branchSelBox)
{
    // $mark 앞에 기준
    $res = array();
    $n = 1;
    // 강성태(bookname) Day(mark)1 ~ Day(mark)  , 배열의 처음 인덱스는 null이기에 count($arr)보다 작은 수까지
    for ($i = 1; $i < count($arr); $i++) {
        if ($i % $partitionVal === 0) { // partition 
            if ($branchSelBox == $n) { // branchVal
                for ($j = $i - $partitionVal + 1; $j <= $i; $j++)
                    $res = arrMerge($res, $arr[$j]);
                return $res;
            }
            $n++; // 배수마다
        } else if ($i === count($arr) - 1) { // end
            $remainder = $i % $partitionVal;
            $nStart = $i - $remainder + 1; //자신포함

            for ($j = $nStart; $j < count($arr); $j++)
                $res = arrMerge($res, $arr[$j]);
            return $res;
        }
    }
    return null;
}
// txt file load
function getTxtFileLoad()
{
    $txtName = $_REQUEST['txtName'];
    $data_path = "../database/" . $txtName;
    $res = "";
    $fp = fopen($data_path, "r") or die("파일을 열 수 없습니다！");
    while (!feof($fp)) {
        $res = $res . fgets($fp);
    }
    fclose($fp);
    return $res;
}
function arrMerge($arr1, $arr2){
    foreach ($arr2 as $key => $value) {
        $arr1[$key] = $value;
    }
    return $arr1;
}

function randomArr($arr) { // 랜덤 정렬
    $res = array();
    $size = count($arr);
    for($i = 0; $i < $size; $i++)  {
        $selected = array_rand($arr);
        $res[$selected] = $arr[$selected];
        unset($arr[$selected]);
    }
    return $res;
}