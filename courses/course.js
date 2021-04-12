let blocks = [];
let block_width ;
let n_blocks = 6;
let a7mer="#b51919";
let black="black";
let green="green";
let stop=false;
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
let swap=(a,b,ar)=>{
    let x=ar[a];
    ar[a]=ar[b];
    ar[b]=x;
};

let lawen=(id,col)=>{
    blocks[arr[id]].style.backgroundColor=col;

    
};   
let blacken=()=>{
    for(let i=0;i<n_blocks;i++)
    {
        lawen(i,black);
    }
};
var arr=[];

let sort = async function(inputArr) {
    stop=false;
    badd.setAttribute("disabled","disabled");
    bsort.setAttribute("disabled","disabled");
    brandom.setAttribute("disabled","disabled");
    let len = n_blocks-1;
    let last=len;
    let swapped;
    do {
        swapped = false;
        for (let i = 0; i < last; i++) {


        
            lawen(i,a7mer);
            lawen(i+1,a7mer);
            if(stop){stop=false;
                badd.removeAttribute("disabled","disabled");
                bsort.removeAttribute("disabled","disabled");
                brandom.removeAttribute("disabled","disabled");
                return;}
            if (inputArr[i] > inputArr[i + 1]) {
                swap(i,i+1,inputArr);
                swapId(i,i+1);
                swapped = true;
            }
            await sleep(700);
            lawen(i,black);
            lawen(i+1,black);
            if(stop){stop=false;
                badd.removeAttribute("disabled","disabled");
                bsort.removeAttribute("disabled","disabled");
                brandom.removeAttribute("disabled","disabled");

                return;}

        }
        lawen(last,green);
        last--;
    } while (swapped);
    for(let i=0;i<len+1;i++)
    {
        lawen(i,green);


    }
    badd.removeAttribute("disabled","disabled");
    bsort.removeAttribute("disabled","disabled");
    brandom.removeAttribute("disabled","disabled");

    return inputArr;
};

let init = () => {

    block_width = 75.0/n_blocks;
    for (let i = 0; i < 12; i++) {
        arr[i]=i;
        blocks[i] = document.getElementById("block" + i);
        blocks[i].style.width = block_width + "vw";
        blocks[i].style.height = block_width + "vw";
        let sep = (25.0) / (n_blocks + 1);
        blocks[i].style.marginLeft = ((i + 1) * sep + i * block_width) + "vw";
    }




};
let randomize=()=>{
    let a,b;
    for(let i=0;i<n_blocks;i++)
    {
        a=getRandomInt(n_blocks);
        b=getRandomInt(n_blocks);
        swapId(a,b);      
        swap(a,b,arr);
    }
    blacken();

};
let check=()=>{
    let val=badd.value-0;
    document.getElementById("range-val").innerHTML=val;
    

    if(val<n_blocks)
    {
        // console.log(n_blocks);
        console.log(val);
        for(let i=n_blocks;i>val;i--){
            blocks[i-1].classList.add("nope");
        }
    
        n_blocks=val-0;
        init();
    }
    if(val>n_blocks)
    {
        for(let i=n_blocks;i<val;i++)
        {
            console.log(i);
            blocks[i].classList.remove("nope");
        }
        n_blocks=val-0;
        init();

    }
    
    

};
var  brandom = document.getElementById("randomize");
brandom.addEventListener("click", randomize);

var bsort = document.getElementById("sort");
bsort.addEventListener("click", ()=>sort(arr));

var badd = document.getElementById("myRange");
badd.addEventListener("change", check);

var bstop = document.getElementById("stop");
bstop.addEventListener("click", ()=>{stop=true;});


init();
randomize();
