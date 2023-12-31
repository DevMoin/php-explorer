<?php
require_once 'fn.php';
$dir = isset($_GET['dir'])?$_GET['dir']:".";
$files = getFiles($dir);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        a {
            text-decoration: none;
        }

        .list-output {}

        .sep {
            width: 300px;
            border-bottom: 2px solid black;
            margin: 20px 0px;
        }

        .icon {
            width: 20px;
            display: inline-block;
            /* margin-right: 10px; */

        }

        .name {
            padding: 10px;
        }

        .action {
            margin-left: 10px;
        }

        .li-opener {
            opacity: 0.5;
        }

        .li-admin_php {
            font-size: 20px;
            margin: 20px 0px;
        }

        :is(.li-admin_php) .name {}

        .li-index_php {
            opacity: 0.5;
        }
    </style>
</head>

<body>
    <a href="https://www.svgrepo.com/vectors/vscode/">Download other icons</a>
    <table border="1" width="100%" class="list-output">
        <thead>
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
        let dir = "<?= $dir?>";
        let listOutput = document.querySelector(".list-output tbody");

        for (let file of files.filter(f => f.isDir)) {
            createRow(file);
        }

        // listOutput.append(createElement("<li class='sep'></li>"));

        for (let file of files.filter(f => !f.isDir)) {
            createRow(file);
        }

        function createRow(file) {
            let tr = document.createElement("tr");
            listOutput.append(tr);

            let actions = document.createElement("td");
            actions.className = "actions";
            tr.append(actions);

            if(file.isDir)
            {
                actions.append(createElement(`<a href="?dir=${file.file}">Opener</a>`))
            }

            for (let action in file.actions) {
                let icon = file.actions[action];
                let vsCode = createElement(`<button class="action" data-action="${action}"><img class="icon" src="${icon}"></button>`);
                actions.append(vsCode);
                vsCode.addEventListener("click", () => {
                    ajaxAction(file, action);
                });
            }

            let link = document.createElement("td");
            link.innerHTML = `<a class='name' href="../${file.file}"><img src="ext/${file.icon}" class="icon">
                ${file.file}
            </a>`;
            tr.append(link);

            tr.classList.add("li-" + (file.file.toLowerCase().replaceAll(/[^a-z0-9]/gi, "_")));

            let tags = document.createElement("span");
            link.append(tags);
            for (let img of file.extraIcons) {
                tags.append(createElement(`<img class="icon" src="${img}" />`));
            }


        }

        function ajaxAction(file, action) {
            fetch(`ajax.php?file=${file.file}&action=${action}`);
        }

        function createElement(str) {
            let ele = document.createElement("div");
            ele.innerHTML = str;
            return ele.firstChild;
        }
    </script>
</body>

</html>