<?php
function getFiles($dir)
{
    $files =  glob("../$dir/*");

    $result = [];
    foreach ($files as $file) {
        $path = realpath($file);
        $isDir = is_dir($path);
        $file = explode("../", $file)[1];
        $item = [
            'icon' => 'file.svg',
            'path' => $path,
            'file' => $file,
            'isDir' => $isDir,
            'extraIcons' => [],
            'extraClasses' => "",
            'actions' => [
                'code' => $isDir ? 'vscode1.svg' : 'vscode2.svg',
                'explorer' => 'explorer.svg'
            ]
        ];

        if ($isDir) {
            $item['icon'] = 'folder.svg';
        } else {

            $removeExplorerAction = true;
            $ext = pathinfo($file, PATHINFO_EXTENSION);

            $mapping = [
                'js' => ['jsx'],
                'php' => ['module', 'install'],
                'zip' => ['7z']
            ];

            foreach ($mapping as $key => $list) {
                if (in_array($ext, $list)) {
                    $ext = $key;
                }
            }

            if (file_exists("ext/" . $ext . ".svg")) {
                $item['icon'] =  $ext . ".svg";
            }

            if ($ext == 'zip') {
                $item['editAction'] = false;
            }


            if ($file == 'admin.php') {
                $item['icon'] = 'db.svg';
            }

            if ($removeExplorerAction) {
                unset($item['actions']['explorer']);
            }
        }

        $composerPath = getComposer($path);
        if ($composerPath) {
            $item['extraIcons'][] = 'composer.svg';

            if (isDrupal($composerPath)) {
                $item['extraIcons'][] = 'drupal.svg';
            }
        }




        $result[] = $item;
    }
    return $result;
}

function isDrupal($composerPath)
{

    $content = file_get_contents($composerPath);
    $info = json_decode($content, true);

    if (
        0
        || isset($info['require']['drupal/core-recommended'])
        || isset($info['require']['drupal/drupal'])
    ) {

        return true;
    }
    return false;
}

function getComposer($file)
{
    $composer = false;
    if (file_exists($file . "/composer.json")) {
        $composer = $file . "/composer.json";
    } else if (file_exists($file . "/web/composer.json")) {
        $composer = $file . "/web/composer.json";
    }
    return $composer;
}
