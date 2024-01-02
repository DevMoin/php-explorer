
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

    if (file.isDir) {
        actions.append(createElement(`<a href="../${dir}/${file.file}">Opener</a>`))
    }

    for (let action in file.actions) {
        let icon = file.actions[action];
        let vsCode = createElement(`<button class="action" data-action="${action}"><img class="icon" src="icons/${icon}"></button>`);
        actions.append(vsCode);
        vsCode.addEventListener("click", () => {
            ajaxAction(file, action);
        });
    }

    let link = document.createElement("td");
    link.innerHTML = `<a class='name' href="?dir=${file.file}"><img src="icons/${file.icon}" class="icon">
        ${file.file}
    </a>`;
    tr.append(link);

    tr.classList.add("li-" + (file.file.toLowerCase().replaceAll(/[^a-z0-9]/gi, "_")));

    let tags = document.createElement("span");
    link.append(tags);
    for (let img of file.extraIcons) {
        tags.append(createElement(`<img class="icon" src="icons/${img}" />`));
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