<?php

$result = null;
$operation = 0;
$x = 0;
$y = 0;
$error = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $x = trim($_POST['x'] ?? '');
    $y = trim($_POST['y'] ?? '');
    $operation = trim($_POST['operation'] ?? '');
}

if ($x === '') {
    $error[] = 'Первое значение не может быть пустым';
}
if ($y === '') {
    $error[] = 'Второе значение не может быть пустым';
}

if ($x !== '' && !is_numeric($x)) {
    $error[] = 'Первое значение должно быть числом';
}
if ($y !== '' && !is_numeric($y)) {
    $error[] = 'Второе значение должно быть числом';
}

if (empty($error)) {

    $x = (float)$x;
    $y = (float)$y;

    switch ($operation) {
        case 'plus':
            $result = $x + $y;
            break;
        case 'minus':
            $result = $x - $y;
            break;
        case 'mult':
            $result = $x * $y;
            break;
        case 'div':
            if ($y === 0.0) {
                $error[] = 'Деление на ноль невозможно';
            } else {

                $result = $x / $y;
            }
            break;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Калькулятор</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include __DIR__ . '/menu.php'; ?>
    <form action="" method="post">
        <input type="text" name="x" placeholder="0" value="<?= htmlspecialchars($x ?? '') ?>">
        <select name="operation">

            <option value="plus" <?= $operation === 'plus' ? 'selected' : '' ?>>+</option>
            <option value="minus" <?= $operation === 'minus' ? 'selected' : '' ?>>-</option>
            <option value="mult" <?= $operation === 'mult' ? 'selected' : '' ?>>*</option>
            <option value="div" <?= $operation === 'div' ? 'selected' : '' ?>>/</option>
        </select>
        <input type="text" name="y" placeholder="0" value="<?= htmlspecialchars($y ?? '') ?>">

        <input type="submit" value="=" style="margin-left: 10px;">
        <?php if (!empty($error)): ?>
            <div class="error">
                <?= htmlspecialchars(implode('<br>', $error)) ?>
            </div>
        <?php elseif ($result !== null): ?>
            <div class="result">
                <?= rtrim(rtrim(number_format($result, 10, '.', ''), '0'), '.') ?: '0' ?>
            </div>
        <?php endif; ?>



    </form>

    <p><a href="../index.php">← На главную</a></p>

    <?php include __DIR__ . '/footer.php'; ?>

</body>

</html>