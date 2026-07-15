<?php

function upDate(string $data, array $posts)
{
    $result = file_put_contents($data, json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    if (!$result) {
        throw new Exception("Не удалось сохранить пост");
    }
}

function redirectToError(string $code, $message = null, $errorId = null): never
{
    $params = ['code' => $code];

    if ($message !== null) {
        $params['message'] = urlencode($message);
    }

    if ($errorId !== null) {
        $params['id'] = urlencode($errorId);
    }

    $queryString = http_build_query($params);
    header("Location: error-tmp.php?{$queryString}");
    exit();
}




function decodeData(string $data): array
{
    $parsedData = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

    if (!is_array($parsedData)) {
        throw new RuntimeException("Данные в файле не являются строкой");
    }

    return $parsedData;
}

function readFileData(string $fileName): string
{
    $filePath = dirname(__DIR__) . "/$fileName";

    if (!file_exists($filePath)) {
        throw new RuntimeException("Файл не найден");
    }

    $fileData = file_get_contents($filePath);

    if ($fileData === false) {
        throw new RuntimeException("Не удалось прочитать файл");
    }

    if (empty($fileData)) {
        throw new RuntimeException("Файл пуст");
    }

    return $fileData;
}
