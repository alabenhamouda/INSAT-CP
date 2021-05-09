import CodeMirror from 'codemirror/lib/codemirror'
import   'codemirror/mode/clike/clike'
import 'codemirror/lib/codemirror.css'
import 'codemirror/theme/duotone-dark.css'
import 'codemirror/addon/hint/show-hint'
import 'codemirror/addon/hint/show-hint.css'
import 'codemirror/addon/edit/closebrackets'
import 'codemirror/addon/edit/matchbrackets'
import './styles/problem.css'
import ClipboardJS from 'clipboard/dist/clipboard.min';
import $ from 'jquery'
import 'bootstrap'
import 'mathjax/es5/tex-svg'
let cli=new ClipboardJS('.tocopy');
//TODO add a copied message
$(function(){
    $(".tocopy")
        .popover({
            content: "Text copied!",
            placement: "bottom",
            trigger: "manual"
        })
        .on("click", function() {
            $(this).popover("show");
            setTimeout(() => {
                console.log(this);
                $(this).popover("hide");
            }, 1000)
        })
})
cli.on('success', function(e) {
    // console.info('Action:', e.action);
    // console.info('Text:', e.text);
    // console.info('Trigger:', e.trigger);

    e.clearSelection();
});

console.log("here");
var edtior = CodeMirror.fromTextArea(document.getElementById('editor'),{
    mode : "clike",
    theme:"duotone-dark",
    lineNumbers : true,
    extraKeys: {"Ctrl-Space": "autocomplete"},
    matchBrackets:true,
    autoCloseBrackets: true,
    styleActiveLine: true
});




// MathJax = {
//     tex: {
//         inlineMath: [['$', '$'], ['\\(', '\\)']]
//     },
//     svg: {
//         fontCache: 'global'
//     }
// };