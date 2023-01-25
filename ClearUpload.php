<?

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

function GetListFiles($folder, &$all_files)
{
    $fp = opendir($folder);
    while ($cv_file = readdir($fp)) {
        if (is_file($folder . "/" . $cv_file)) {
            $all_files[] = $folder . "/" . $cv_file;
        } elseif ($cv_file != "." && $cv_file != ".." && is_dir($folder . "/" . $cv_file)) {
            GetListFiles($folder . "/" . $cv_file, $all_files);
        }
    }
    closedir($fp);
}

$module = 'iblock';
$arrPath = [];

// получаем файлы рекурсивно из папке модуля в upload
GetListFiles($_SERVER['DOCUMENT_ROOT'] . '/upload/' . $module, $arrPath);

foreach ($arrPath as $key => &$value) {
    $value = str_replace($_SERVER['DOCUMENT_ROOT'] . '/upload/', '', $value);
}

clg($arrPath, 'arrPath');
// clg(count($arrPath), 'arrPath');

$Element = [];

$query = \Bitrix\Main\FileTable::GetList([
    'select' => ['FILE_NAME', 'SUBDIR'],
    'filter' => [
        'MODULE_ID' => $module,
    ],
    // 'limit' => 100,
]);

while ($arFields = $query->Fetch()) {
    $path = $arFields['SUBDIR'] . '/' . $arFields['FILE_NAME'];
    $Element[] = $path;
}


clg($Element, 'Element');
// clg(count($Element), 'Element');

// вычисляем разность массивов
$result = array_values(array_diff($arrPath, $Element));


clg($result, 'result');
// clg(count($result), 'result');


$size = 0;

// подсчитываем суммарный размер файлов в байтах 
foreach ($result as $key => $value) {
    $size += filesize($_SERVER['DOCUMENT_ROOT'] . '/upload/' . $value);
}

// переводим в гигабайты
clg($size / 1024 / 1024 / 1024, 'size');


// $result = array_map(function ($elem) {
//     $elem = [
//         'unixTime' => filemtime($_SERVER['DOCUMENT_ROOT'] . '/upload/' . $elem),
//         'date' => date('d.m.Y H:i:s', filemtime($_SERVER['DOCUMENT_ROOT'] . '/upload/' . $elem)),
//         'path' => $_SERVER['DOCUMENT_ROOT'] . '/upload/' . $elem,
//     ];
//     return $elem;
// }, $result);

// usort($result, function ($a, $b) {
//     return $a['unixTime'] - $b['unixTime'];
// });

// clg($result, 'result');
