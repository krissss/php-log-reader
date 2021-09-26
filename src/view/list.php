<?php
/**
 * @var Kriss\LogReader\Objects\DirObject[] $dirs
 * @var Kriss\LogReader\Objects\FileObject[] $files
 * @var callable $urlBuilder
 * @var bool $canDelete
 * @var int $viewMaxSize
 * @var string $bootstrapCssUrl
 * @var int $tailLine
 */
?>
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log Reader</title>
    <link rel="stylesheet" href="<?= $bootstrapCssUrl ?>" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <?php if ($dirs): ?>
        <h1>Dirs</h1>
        <ul>
            <?php foreach ($dirs as $dir): ?>
                <li>
                    <a href="<?= $urlBuilder('index', ['key' => $dir->getKey()]) ?>"><?= $dir->getName() ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <?php if ($files): ?>
        <h1>Files</h1>
        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Size</th>
                <th>Modify At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($files as $file): ?>
                <tr>
                    <td><?= $file->getName() ?></td>
                    <td><?= $file->getSizeForHumans() ?></td>
                    <td><?= $file->getModifyAtForHumans() ?></td>
                    <td>
                        <?php if ($file->getSizeKB() < $viewMaxSize): ?>
                            <a href="<?= $urlBuilder('view', ['key' => $file->getKey()]) ?>"
                        <?php endif; ?>
                           class="btn btn-primary btn-xs" target="_blank">view</a>
                        <a href="<?= $urlBuilder('tail', ['key' => $file->getKey(), 'line' => $tailLine]) ?>"
                           class="btn btn-primary btn-xs" target="_blank">tail</a>
                        <a href="<?= $urlBuilder('download', ['key' => $file->getKey()]) ?>"
                           class="btn btn-warning btn-xs">download</a>
                        <?php if ($canDelete): ?>
                            <a href="<?= $urlBuilder('delete', ['key' => $file->getKey()]) ?>"
                               class="btn btn-danger btn-xs">delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
