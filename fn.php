<?php
function getFiles($dir)
{
    $files =  glob("../$dir/*");


    array_unshift($files, "../$dir");

    $result = [];
    foreach ($files as $file) {
        $path = realpath($file);
        $isDir = is_dir($path);
        $file = explode("../", $file)[1];
        $fileName = pathinfo($file, PATHINFO_FILENAME);
        $ext = pathinfo($file, PATHINFO_EXTENSION);

        $fileNameExt = $fileName . "." . $ext;
        $count = null;
        $size = null;
        if ($isDir) {
            $fileNameExt = $fileName;
            $fi = new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS);
            $count = iterator_count($fi);
        } else {
            $size = filesize($path);
        }

        $item = [
            // 'icon' => 'file.svg',
            'icon' => null,
            'path' => $path,
            'file' => $file,
            'size' => $size,
            'count' => $count,
            'fileNameExt' => $fileNameExt,
            'isDir' => $isDir,
            'extraIcons' => [],
            'extraClasses' => [],
            'actions' => [
                'code' => $isDir ? 'vscode1.svg' : 'vscode2.svg',
                'explorer' => 'explorer.svg'
            ]
        ];

        if ($isDir && $count == 0) {
            $item['extraIcons'][] = srcDesc('empty.svg');;
        }

        if ($isDir) {
            $item['icon'] = 'folder.svg';
            if ($file == './opener') {
                $item['icon'] = 'death.svg';
                $item['extraIcons'][] = srcDesc('death.svg');
                $item['extraClasses'][] = "shallow";
            }
        } else {

            $removeExplorerAction = true;

            $mapping = [
                'js' => ['jsx'],
                'php' => ['module', 'install'],
                'zip' => ['7z'],
                'png' => ['jpg', 'jpeg', 'gif'],
                'ico' => ['icon'],
            ];

            foreach ($mapping as $key => $list) {
                if (in_array($ext, $list)) {
                    $ext = $key;
                }
            }

            if ($ext == 'zip') {
                $item['editAction'] = false;
            }


            if ($file == './admin.php') {
                $item['icon'] = 'db.svg';
                $item['extraClasses'][] = "admin-path";
            }

            if ($removeExplorerAction) {
                unset($item['actions']['explorer']);
            }

            if ($file == './index.php') {
                $item['icon'] = 'death.svg';
                $item['extraClasses'][] = "shallow";
            }
            if ($fileNameExt == 'composer.json' || $fileNameExt == 'composer.lock') {
                $item['icon'] = 'composer.svg';
            }
            if (strpos($ext, "git") !== false) {
                $item['icon'] = 'git.svg';
            }


            if (!$item['icon']) {
                $item['icon'] = 'file.svg';
                if (file_exists("icons/" . $ext . ".svg")) {
                    $item['icon'] =  $ext . ".svg";
                } else if (file_exists("icons/png/$ext.png")) {
                    $item['icon'] =  "png/$ext.png";
                } else {
                    $image = imagecreatefrompng("icons/file.png");
                    if ($image) {
                        // Allocate colors
                        $white = imagecolorallocate($image, 100, 255, 255);
                        $bg = imagecolorallocatealpha($image, 100, 255, 255, 110);
                        $black = imagecolorallocate($image, 0, 0, 0);
                        // Add text to image

                        $font = 'arialbd.ttf';

                        $angle = 0;
                        $size = 350;
                        $x = 00;
                        $y = 750;
                        // Add some shadow to the text
                        imagefilledrectangle($image, $x, $y - 400, $x + 1000, $y - 380 + $size, $bg);

                        imagettftext($image, $size, $angle, $x + 0, $y + 2, $white, $font, $ext);
                        imagettftext($image, $size, $angle, $x + 2, $y + 0, $white, $font, $ext);
                        imagettftext($image, $size, $angle, $x - 2, $y - 2, $white, $font, $ext);
                        imagettftext($image, $size, $angle, $x + 2, $y + 2, $white, $font, $ext);
                        imagettftext($image, $size, $angle, $x, $y, $black, $font, $ext);
                        imagepng($image, "icons/png/$ext.png");

                        $item['icon'] =  "png/$ext.png";
                    }
                    // exit;
                }
            }
        }

        $composerPath = getComposer($path);
        if ($composerPath) {
            $content = file_get_contents($composerPath);
            $info = json_decode($content, true);

            $item['extraIcons'][] = [
                'src' => 'composer.svg',
                'description' => (isset($info['name']) ? "Name: " . $info['name'] . "\n" : "") . print_r($info, true),
            ];
            $otherIcons = getOtherIconsByComposer($info);
            $item['extraIcons'] =  array_merge($item['extraIcons'], $otherIcons);
        }




        $result[] = $item;
    }
    return $result;
}

function srcDesc($src, $desc="")
{
    return ['src' => $src, 'description' => $desc];
}

function getOtherIconsByComposer($info)
{
    $otherIcons = [];



    $mapping = [
        'drupal.svg' => [
            'require->drupal/core-recommended',
            'require->drupal/drupal',
        ],
        'php.svg' => [
            'require->php'
        ],
        'twig.svg' => [
            'require->twig/twig'
        ],
        'symfony.svg' => [
            // 'require->symfony/http-kernel'
        ],
        'http.svg' => [
            'require->symfony/http-kernel'
        ]
    ];

    foreach ($mapping as $icon => $patterns) {
        foreach ($patterns as $pattern) {
            $kotak = explode("->", $pattern);
            $select = null;
            if (count($kotak)) {
                $select = $info;
                foreach ($kotak as $ghin) {
                    if (isset($select[$ghin])) {
                        $select = $select[$ghin];
                    } else {
                        $select = null;
                        break;
                    }
                }
            }
            if ($select) {
                $otherIcons[] = [
                    'src' => $icon,
                    'description' => $ghin . ":" . $select,
                ];
            }
        }
    }
    return $otherIcons;
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
