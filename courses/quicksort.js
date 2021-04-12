let blocks = [];
let block_width;
let sep;
let n_blocks = 6;
let a7mer = "#b51919";
let black = "black";
let green = "green";
let stop = false;

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function getRandomInt(max) {
    return Math.floor(Math.random() * max);
}
let swapId = (id1, id2) => {
    let x = blocks[arr[id1]].style.marginLeft;
    blocks[arr[id1]].style.marginLeft = blocks[arr[id2]].style.marginLeft;
    blocks[arr[id2]].style.marginLeft = x;
};
let swap = (a, b, ar) => {
    let x = ar[a];
    ar[a] = ar[b];
    ar[b] = x;
};

let lawen = (id, col) => {
    blocks[arr[id]].style.backgroundColor = col;


};
let blacken = () => {
    for (let i = 0; i < n_blocks; i++) {
        lawen(i, black);
    }
};
let setArrow = (pos, id) => {
    pos++;
    arrows[id].style.marginLeft = (pos * (sep + block_width) - block_width / 2 - 1.4 * 1) + "vw";
};
var arr = [];
let arrows = [];
let init = () => {

    sep = (25.0) / (n_blocks + 1);
    block_width = 75.0 / n_blocks;
    arrows[0] = document.getElementById("a1");
    arrows[1] = document.getElementById("a2");

    for (let i = 0; i < 12; i++) {
        arr[i] = i;
        blocks[i] = document.getElementById("block" + i);
        blocks[i].style.width = block_width + "vw";
        blocks[i].style.height = block_width + "vw";
        blocks[i].style.marginLeft = ((i + 1) * sep + i * block_width) + "vw";
    }
    setArrow(0, 0);
    setArrow(n_blocks - 1, 1);
};
let randomize = () => {
    let a, b;
    for (let i = 0; i < n_blocks; i++) {
        a = getRandomInt(n_blocks);
        b = getRandomInt(n_blocks);
        swapId(a, b);
        swap(a, b, arr);
    }
    blacken();

};
let check = () => {
    blacken();

    let val = badd.value - 0;
    document.getElementById("range-val").innerHTML = val;


    if (val < n_blocks) {
        // console.log(n_blocks);
        for (let i = n_blocks; i > val; i--) {
            blocks[i - 1].classList.add("nope");
        }

        n_blocks = val - 0;
        init();
    }
    if (val > n_blocks) {
        for (let i = n_blocks; i < val; i++) {
            blocks[i].classList.remove("nope");
        }
        n_blocks = val - 0;
        init();

    }
    randomize();
};

let partition = async function(l, r) {
    if (stop) return;
    let i = l - 1;
    let val = arr[r];
    lawen(r, "orange");
    for (let j = l; j < r; j++) {
        if (stop) return;
        if (arr[j] <= val) {
            lawen(i + 1, a7mer);
            lawen(j, a7mer);

            swap(i + 1, j, arr);
            swapId(i + 1, j);

            await sleep(700);
            if (stop) return;
            lawen(i + 1, black);
            lawen(j, black);
            i++;
        }
    }
    swap(i + 1, r, arr);
    swapId(i + 1, r);
    return i + 1;


};

let quicksort = async function(l, r) {
    if (stop) return;

    if (l <= r) {
        setArrow(l, 0);
        setArrow(r, 1);
        await sleep(700);
        if (stop) return;
    }
    if (l == r) {
        lawen(l, green);
    }
    if (l >= r) return;
    let q = await partition(l, r);
    if (stop) return;

    lawen(q, green);
    await sleep(700);
    await quicksort(l, q - 1);
    await sleep(700);
    if (stop) return;
    await quicksort(q + 1, r);
};





var brandom = document.getElementById("randomize");
brandom.addEventListener("click", randomize);

var qsort = document.getElementById("sort");
qsort.addEventListener("click", async function() {
    stop = false;
    blacken();
    badd.setAttribute("disabled", "disabled");
    qsort.setAttribute("disabled", "disabled");
    brandom.setAttribute("disabled", "disabled");

    await quicksort(0, n_blocks - 1);

    badd.removeAttribute("disabled", "disabled");
    qsort.removeAttribute("disabled", "disabled");
    brandom.removeAttribute("disabled", "disabled");

});

var badd = document.getElementById("myRange");
badd.addEventListener("change", check);

var bstop = document.getElementById("stop");
bstop.addEventListener("click", () => {
    stop = true;
});


init();
randomize();
