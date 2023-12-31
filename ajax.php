<?php

print_r($_GET);

$file = $_GET['file'];
$action = $_GET['action'];
switch($action)
{
    case 'explorer':
        $command = "start %windir%\\explorer.exe \"".realpath("../$file")."\"";
        shell_exec($command);
        break;
    case 'code':
        $command = "code \"".realpath("../$file")."\"";
        shell_exec($command);
        break;
}