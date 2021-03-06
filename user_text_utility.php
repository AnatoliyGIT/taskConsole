<?php
require_once 'functions.php';

$fileNamePeople = 'people.csv';

echoToConsole('');
echoToConsole('####################################################################');
echoToConsole('#                  Welcome to user_text_utility!                   #');
echoToConsole('####################################################################');
echoToConsole('');

isParametersValid($argv);

$arg_1 = $argv[1];
$arg_2 = $argv[2];

if (!file_exists($fileNamePeople)) {
    error('Error! File not found!');
}

$separator = '';

if ($arg_1 === 'comma') {
    $separator = ',';
}
if ($arg_1 === 'semicolon') {
    $separator = ';';
}

checkSeparator($separator);

if ($arg_2 === 'countAverageLineCount') {
    countAverageLineCount($separator);
}

if ($arg_2 === 'replaceDates') {
    replaceDates($separator);
}

exit(1);
