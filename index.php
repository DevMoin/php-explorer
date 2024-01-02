<?php
require_once 'fn.php';
$dir = isset($_GET['dir']) ? $_GET['dir'] : ".";
$realDir = realpath("../$dir");
if(!$realDir)
{
    echo "Bro wtf";exit;
}
$realDir = trim($realDir, "\\");

$realDir = "http://localhost".str_replace("\\", "/", substr($realDir, strlen($_SERVER['DOCUMENT_ROOT'])));
$back = dirname($dir);
if ($back == "\\") {
    $back = ".";
}

$files = getFiles($dir);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <a href="https://www.svgrepo.com/vectors/vscode/">Download other icons</a>
    <table border="1" width="100%" class="list-output">
        <thead>
            <tr>
                <th><a href="./?dir=<?= $back ?>"><button class="btn-back">Back to <?= $back ?></button></a></th>
                <th><h1><a target="__blank" href="<?= $realDir?>"><?= $realDir?></a></h1></th>
            </tr>
            <tr>
                <th>Action</th>
                <th>File</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    <script>
        let files = <?= json_encode($files); ?>;
        let dir = "<?= $dir ?>";
    </script>
    <script src="main.js"></script>
</body>

</html>