<?php
header('Content-Type: text/html; charset=utf-8');
$data_path = "../database/idiom.txt";

$fp = fopen($data_path, "r") or die("파일을 열 수 없습니다！");
$words = array();
$n = 0;

// 1day로 불러오기
while (!feof($fp)) {
    $line = fgets($fp);
    if ($line === false)
        continue;

    $line = preg_replace('/\r\n|\r|\n/', '', $line);
    if ($line === "")
        continue;

    // 새로운 테이블추가
    if (strpos($line, "**") !== false) {
        //$line = preg_replace("/[*\\s]*/", "", $line); // *과 공백제거
        //$html .= "<tr><td>" .$line." </td></tr>";
        //$html .= "<tr> </tr>"; // 한줄 띄기
        continue;
    }
    if (strpos($line, "^") === false)
        continue;

    $split = explode("^", $line);

    if ($split[0] < 1)
        continue;

    $word = array('question' => $split[1], 'answer' => $split[2]);
    array_push($words, $word);
}

fclose($fp);
?>


<body>
</body>
<script>
    var n_row = 4;
    var n_cell = 30;
    var n = 0;
    var words = <?= json_encode($words) ?>;
    var max = words.length;

    for (var i = 0; i < parseInt(max/(n_cell*n_row)) + 15; i++)
        tablecreate();


    function tablecreate() {
        var trs = new Array();
        for (var i = 0; i < n_cell; i++)
            trs.push(document.createElement("tr"));

        for (var row = 0; row < n_row; row++) {
            for (var cell = 0; cell < n_cell; cell++) {

                var td1 = document.createElement("td");
                td1.innerHTML = words[n]['question'];

                var td2 = document.createElement("td");
                td2.innerHTML = words[n++]['answer'];

                trs[cell].appendChild(td1);
                trs[cell].appendChild(document.createElement("td"));
                trs[cell].appendChild(td2);
                trs[cell].appendChild(document.createElement("td"));

                if (max == n)
                    break;


            }
        }
        var table = document.createElement("table");
        for (tr of trs)
            table.appendChild(tr);

        document.body.appendChild(table);
    }
</script>