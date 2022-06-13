<?php
header('Content-Type: text/html; charset=utf-8');

function getConfString(){
    $data_path = "../conf.txt";
    $res = "";

    $fp = fopen($data_path, "r") or die("파일을 열 수 없습니다！");
    $line = "";
    while (!feof($fp)) {
        $line = fgets($fp);
        if ($line === false)
            continue;

        $res .= $line;
    }
    // 파일 닫기
    fclose($fp);
    return $res;
}
function getConf()
{
    $data_path = "../conf.txt";
    $n = 1;
    $res[0] = null;

    $fp = fopen($data_path, "r") or die("파일을 열 수 없습니다！");
    $line = fgets($fp); // 첫줄 무시
    while (!feof($fp)) {
        $line = fgets($fp);
        if ($line === false)
            continue;

        $line = preg_replace('/\r\n|\r|\n/', '', $line);
        if ($line === "")
            continue;

        $explode = explode("/", $line);
        for($i = 0; $i<6; $i++)
            if($explode[$i] == null)
                $explode[$i] = "";

        $res[$n++] = array('chaptername' => $explode[0], 'fileurl' => $explode[1], 'partition' => $explode[2], 'wrongfiliter' => $explode[3], 'mark' => $explode[4], 'subtitle' => $explode[6]);
        // ChapterName / FileUrl / Partition / wrongFiliter / Mark / Subtitle)
    }
    // 파일 닫기
    fclose($fp);
    return $res;
}
function getChapterHtml()
{
    $conf = getConf();
    $res = "<option value='0'>선택해주세요</option>";
    for ($i = 1; $i < count($conf); $i++)
        $res .= "<option value='" . $conf[$i]['fileurl'] . "'>" . $conf[$i]['chaptername'] . "</option> ";
    return $res;
}

function getBranchHtml()
{
    $conf = getConf();
    $csi = $_REQUEST['csi']; //chapterSelectedIndex
    $selectedConf = $conf[$csi];

    $res = "&&";
    $partitionVal = intval($_REQUEST['pv']);
    if ($partitionVal == -1) { // conf에서 불러오는 선택 여기에 안들어오면 직접설정한 분할 값
        $partitionVal = intval($selectedConf['partition']);
        $res = $partitionVal . "&" . $selectedConf['wrongfiliter'] . "&";
    }

    // developMode
    // $csi = 1;
    // $partitionVal = 3;

    $isAsc = $_REQUEST['ia'];
    
    $o_branchNames = null;
    // getBranchNames 오름차순이 아닌경우
    if($isAsc === 'false' || $isAsc === null) // 데이터가 없거나 오름차순x
        $o_branchNames = getBranchNames($selectedConf['fileurl']);
    else // 오름 차순일 경우
        $o_branchNames = array('','A', 'B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

    // partition  오름차순도 개수별로 나눠줄수 있음 (a~c)(d~f)(g~h) 요론식
    $outName = getPartitionNames($o_branchNames, $partitionVal, $selectedConf['mark'], $selectedConf['subtitle']);

    // output
    $res .= "<option value='0'>선택해주세요</option>";
    for ($i = 0; $i < count($outName); $i++) {
        $res .= "<option value='" . ($i + 1) . "'>" . $outName[$i] . "</option> ";
    }
    return $res;
}

function getBranchNames($fileName)
{
    // getBranchSelBox
    $data_path = "../database/" . $fileName;

    // createTable
    $res[0] = null;
    $fp = fopen($data_path, "r") or die("파일을 열 수 없습니다！");
    while (!feof($fp)) {
        $line = fgets($fp);
        if ($line === false)
            continue;

        if (strpos($line, "**") !== false) {
            $line = preg_replace("/[*\\s]*/", "", $line); // *과 공백제거
            array_push($res, $line); // **를 빼고 추가
            continue;
        }
    }
    fclose($fp);
    return $res;
}
function getPartitionNames($arr, $partitionVal, $mark, $subtitle)
{
    // $mark 앞에 기준
    $res = array();
    // 강성태(bookname) Day(mark)1 ~ Day(mark)  
    for ($i = 1; $i < count($arr); $i++) {
        if ($i % $partitionVal === 0) {
            $index = ($i - $partitionVal + 1);
            $full_name = $mark . $subtitle . (($partitionVal == 1) ? $arr[$i] : $arr[$index] . "~" . $subtitle . $arr[$i]);
            array_push($res, $full_name);
        } else if ($i === count($arr) - 1) {
            $remainder = $i % $partitionVal; // 나머지
            $index = ($i - $remainder + 1);
            $full_name = $mark . $subtitle . (($remainder == 1) ? $arr[$i] : $arr[$index] . "~" . $subtitle . $arr[$i]);
            array_push($res, $full_name);
        }
    }
    return $res;
}
