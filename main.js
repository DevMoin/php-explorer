
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

    

    for (let action in file.actions) {
        let icon = file.actions[action];
        let vsCode = createElement(`<button class="action" data-action="${action}"><img class="icon" src="icons/${icon}"></button>`);
        actions.append(vsCode);
        vsCode.addEventListener("click", () => {
            ajaxAction(file, action);
        });
    }

    if (file.isDir || true) {
        actions.append(createElement(`<a target="__blank" href="../${file.file}">Visit</a>`))
    }

    let link = document.createElement("td");
    let size = file.isDir ? `<span class="size">(${(file.count)}files)</span>` : `<span class="size">(${bytesToSize(file.size)})</span>`;
    link.innerHTML = `<a class='name' href="?dir=${file.file}"><img src="icons/${file.icon}" class="icon">
        ${file.fileNameExt} ${size}
    </a>`;
    tr.append(link);

    tr.classList.add("li-" + (file.file.toLowerCase().replaceAll(/[^a-z0-9]/gi, "_")));
    
    for(let cls of  file.extraClasses )
    {
        tr.classList.add(cls);
    }

    let tags = document.createElement("span");
    link.append(tags);
    for (let img of file.extraIcons) {
        let desc = img.description && img.description.length < 30 ? `<b>${img.description}` : '';
        tags.append(createElement(`<span class="tag"><img title="${img.description}" class="icon" src="icons/${img.src}" />${desc}</span > `));
    }


}

function ajaxAction(file, action) {
    fetch(`ajax.php ? file = ${ file.file }& action=${ action } `);
}

function createElement(str) {
    let ele = document.createElement("div");
    ele.innerHTML = str;
    return ele.firstChild;
}

function bytesToSize(bytes) {
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes == 0) return '0 Byte';
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
 }