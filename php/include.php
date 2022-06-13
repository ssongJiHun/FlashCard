<?php
header('Content-Type: text/html; charset=utf-8');

$mode = $_REQUEST['mode'];

switch ($mode) {
        // readSelName.php
    case 'getconfstring':
        require 'readSelName.php';
        echo getConfString();
        break;
    case 'getchapter':
        require 'readSelName.php';
        echo getChapterHtml();
        break;
    case 'getbranch':
        require 'readSelName.php';
        echo getBranchHtml();
        break;
        // read.php
    case 'study':
        require 'read.php';
        echo getStudyJson();
        break;
    case 'txt':
        require 'read.php';
        echo getTxtFileLoad();
        break;
        // otherFunc.php
    case 'iserr':
        require 'otherFunc.php';
        echo IsErr();
        break;
    case 'search':
        require 'otherFunc.php';
        echo getSearchWord();
        break;
        // write.php
    case 'saveconfstring': // conf Save
        require 'write.php';
        echo saveConfString();
        break;
    case 'savejson': // dataBase Save
        require 'write.php';
        echo savejson();
        break;
    case 'savestring':// dataBase Save
        require 'write.php';
        echo saveString();
        break;
    case 'addchapter':
        require 'write.php';
        echo addChapter();
        break;
    case 'addbranch':
        require 'write.php';
        echo addBranch();
        break;
    default:
        echo 'mode Error';
}
