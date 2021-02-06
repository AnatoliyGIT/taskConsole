<?php

$fileNamePeople = 'people.csv';

function echoToConsole(string $str): void
{
    echo $str . PHP_EOL;
}

function error(string $str): void
{
    echoToConsole($str);
    exit(1);
}

function isParametersValid(array $arguments): void
{
    if (!$arguments[1] || !$arguments[2]) error('Error! The request must be with two parameters');
    foreach ($arguments as $key => $value) {
        if ($key == 1) {
            if ($value !== 'comma' && $value !== 'semicolon')
                error('Error! The first parameter must be: "comma" or "semicolon"');
        }
        if ($key == 2) {
            if ($value !== 'countAverageLineCount' && $value !== 'replaceDates')
                error('Error! The second parameter must be: "countAverageLineCount" or "replaceDates"');
        }
    }
}

function averageLineCount(int $user_id): float
{
    $dir = './texts';
    $files = scandir($dir);
    $countFiles = 0;
    $count_all_lines = 0;
    foreach ($files as $file) {
        $file_name = basename($file);
        $file_id = (int)explode('-', $file_name)[0];
        if ($user_id === $file_id) {
            $countFiles++;
            $count_all_lines += count(file($dir . '/' . $file_name));
        }
    }
    if ($countFiles) return $count_all_lines / $countFiles;
    else return 0;
}

function replace(int $user_id): int
{
    $regexDates = '/([a-zA-Zа-яА-Я.,?\s]+)/';
    $regexWords = '/[\s,.]+/';
    $dir = './texts';
    $files = scandir($dir);
    $countFiles = 0;
    $countAllReplaces = 0;
    foreach ($files as $file) {
        $file_name = basename($file);
        $file_id = (int)explode('-', $file_name)[0];

        if ($user_id === $file_id) {
            $countFiles++;
            $string = file_get_contents('./texts/' . $file_name);
            $words = preg_split($regexWords, $string);
            $arrayDates = preg_split($regexDates, $string , -1 );
            foreach ($arrayDates as $date) {
                $countReplaces = 0;
                foreach ($words as $word) {
                    if ($word === $date) {
                        $arrChars = str_split($date);
                        if ($arrChars[6] > 2) {
                            array_splice($arrChars, 6, 0, ['1', '9']);
                        } else {
                            array_splice($arrChars, 6, 0, ['2', '0']);
                        }
                        $date = implode($arrChars);
                        $date = str_replace('/', '-', $date);
                        $string = str_replace($word, $date, $string);
                        if (!is_dir('output_texts')) mkdir('output_texts');
                        $countReplaces++;
                        createNewFile($file_name, $string);
                    }
                }
                $countAllReplaces += $countReplaces;
            }
        }
    }
    return $countAllReplaces;
}

function countAverageLineCount($separator): void
{
    global $fileNamePeople;
    $content_arr = file($fileNamePeople);
    foreach ($content_arr as $users) {
        $user = explode($separator, $users);
        $user_id = $user[0];
        $user_name = $user[1];
        echoToConsole($user_id . ' User: ' . $user_name . ' - Average line count: ' . averageLineCount($user_id));
    }
}

function replaceDates($separator): void
{
    global $fileNamePeople;
    $content_arr = file($fileNamePeople);
    foreach ($content_arr as $users) {
        $user = explode($separator, $users);
        $user_id = $user[0];
        $user_name = $user[1];
        echoToConsole($user_id . ' User: ' . $user_name . ' - Replacements made: ' . replace($user_id));
    }
}

function checkSeparator($separator): void
{
    global $fileNamePeople;
    $people = file_get_contents($fileNamePeople);
    if (stristr($people, $separator) === false) {
        error('Error! You are using the wrong separator.');
    }
}

function createNewFile($fileName, $content)
{
    $file = fopen('output_texts/' . $fileName, "w");
    fwrite($file, $content);
    fclose($file);
}