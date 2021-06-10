//import "../node_modules/highlight.js/"
//import "highlight.js/styles/default.css"
//import hljs from "highlight.js";
import "highlight.js";
import hljs from 'highlight.js';

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
    console.log("aa")

    sep = (25.0) / (n_blocks + 1);
    block_width = 75.0 / n_blocks;
    for (let i = 0; i < 4; i++) {
        arrows[i] = document.getElementById("a" + (i + 1));
    }

    for (let i = 0; i < 12; i++) {
        arr[i] = i;
        blocks[i] = document.getElementById("block" + i);
        blocks[i].style.width = block_width + "vw";
        blocks[i].style.height = block_width + "vw";
        blocks[i].style.marginLeft = ((i + 1) * sep + i * block_width) + "vw";
    }
    setArrow(0, 0);
    setArrow(n_blocks - 1, 1);
    setArrow(0, 2);
    setArrow(0, 3);

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

let partition = async function (l, r) {
    arrows[2].classList.remove("nope");
    arrows[3].classList.remove("nope");
    setArrow(l, 2);
    setArrow(l, 3);
    if (stop) return;
    let i = l - 1;
    let val = arr[r];
    lawen(r, "orange");
    for (let j = l; j < r; j++) {
        if (stop) return;
        setArrow(j, 2);
        await sleep(700);
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
            setArrow(i + 1, 3);
        }
    }
    swap(i + 1, r, arr);
    swapId(i + 1, r);
    arrows[2].classList.add("nope");
    arrows[3].classList.add("nope");
    return i + 1;


};

let quicksort = async function (l, r) {
    if (stop) return;

    if (l <= r) {
        setArrow(l, 0);
        setArrow(r, 1);
        await sleep(700);
        if (stop) return;
    }
    if (l === r) {
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
qsort.addEventListener("click", async function () {
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


let qblocks = [];
let qblock_width;
let qn_blocks = 6;
let qa7mer = "#b51919";
let qblack = "black";
let qgreen = "green";
let qstop = false;

function qsleep(qms) {
    return new Promise(qresolve => setTimeout(qresolve, qms));
}

function qgetRandomInt(qmax) {
    return Math.floor(Math.random() * qmax);
}

let qswapId = (qid1, qid2) => {
    let qx = qblocks[qarr[qid1]].style.marginLeft;
    qblocks[qarr[qid1]].style.marginLeft = qblocks[qarr[qid2]].style.marginLeft;
    qblocks[qarr[qid2]].style.marginLeft = qx;
};
let qswap = (qa, qb, qar) => {
    let qx = qar[qa];
    qar[qa] = qar[qb];
    qar[qb] = qx;
};

let qlawen = (qid, qcol) => {
    qblocks[qarr[qid]].style.backgroundColor = qcol;


};
let qblacken = () => {
    for (let qi = 0; qi < qn_blocks; qi++) {
        qlawen(qi, qblack);
    }
};
var qarr = [];

let bubsort = async function (qinputArr) {
    qstop = false;
    qbadd.setAttribute("disabled", "disabled");
    qbsort.setAttribute("disabled", "disabled");
    qbrandom.setAttribute("disabled", "disabled");
    let qlen = qn_blocks - 1;
    let qlast = qlen;
    let qswapped;
    do {
        qswapped = false;
        for (let qi = 0; qi < qlast; qi++) {


            qlawen(qi, qa7mer);
            qlawen(qi + 1, qa7mer);
            if (qstop) {
                qstop = false;
                qbadd.removeAttribute("disabled", "disabled");
                qbsort.removeAttribute("disabled", "disabled");
                qbrandom.removeAttribute("disabled", "disabled");
                return;
            }
            if (qinputArr[qi] > qinputArr[qi + 1]) {
                qswap(qi, qi + 1, qinputArr);
                qswapId(qi, qi + 1);
                qswapped = true;
            }
            await qsleep(700);
            qlawen(qi, qblack);
            qlawen(qi + 1, qblack);
            if (qstop) {
                qstop = false;
                qbadd.removeAttribute("disabled", "disabled");
                qbsort.removeAttribute("disabled", "disabled");
                qbrandom.removeAttribute("disabled", "disabled");

                return;
            }

        }
        qlawen(qlast, qgreen);
        qlast--;
    } while (qswapped);
    for (let qi = 0; qi < qlen + 1; qi++) {
        qlawen(qi, qgreen);
    }
    qbadd.removeAttribute("disabled", "disabled");
    qbsort.removeAttribute("disabled", "disabled");
    qbrandom.removeAttribute("disabled", "disabled");

    return qinputArr;
};

let qinit = () => {

    qblock_width = 75.0 / qn_blocks;
    for (let qi = 0; qi < 12; qi++) {
        qarr[qi] = qi;
        qblocks[qi] = document.getElementById("qblock" + qi);
        qblocks[qi].style.width = qblock_width + "vw";
        qblocks[qi].style.height = qblock_width + "vw";
        let qsep = (25.0) / (qn_blocks + 1);
        qblocks[qi].style.marginLeft = ((qi + 1) * qsep + qi * qblock_width) + "vw";
    }


};
let qrandomize = () => {
    let qa, qb;
    for (let qi = 0; qi < qn_blocks; qi++) {
        qa = qgetRandomInt(qn_blocks);
        qb = qgetRandomInt(qn_blocks);
        qswapId(qa, qb);
        qswap(qa, qb, qarr);
    }
    qblacken();

};
let qcheck = () => {
    qblacken();

    let qval = qbadd.value - 0;
    document.getElementById("qrange-val").innerHTML = qval;


    if (qval < qn_blocks) {
        // console.log(n_blocks);
        for (let qi = qn_blocks; qi > qval; qi--) {
            qblocks[qi - 1].classList.add("nope");
        }

        qn_blocks = qval - 0;
        qinit();
    }
    if (qval > qn_blocks) {
        for (let qi = qn_blocks; qi < qval; qi++) {
            qblocks[qi].classList.remove("nope");
        }
        qn_blocks = qval - 0;
        qinit();

    }


};
var qbrandom = document.getElementById("qrandomize");
qbrandom.addEventListener("click", qrandomize);

var qbsort = document.getElementById("qsort");
qbsort.addEventListener("click", () => bubsort(qarr));

var qbadd = document.getElementById("qmyRange");
qbadd.addEventListener("change", qcheck);

var qbstop = document.getElementById("qstop");
qbstop.addEventListener("click", () => {
    qstop = true;
});


qinit();
qrandomize();
hljs.highlightAll();
