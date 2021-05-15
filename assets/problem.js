import CodeMirror from 'codemirror/lib/codemirror'
import 'codemirror/mode/clike/clike'
import 'codemirror/mode/python/python'
import 'codemirror/lib/codemirror.css'
import 'codemirror/theme/ayu-mirage.css'
import 'codemirror/addon/hint/show-hint'
import 'codemirror/addon/hint/show-hint.css'
import 'codemirror/addon/edit/closebrackets'
import 'codemirror/addon/edit/matchbrackets'
import 'codemirror/keymap/vim'
import './styles/problem.css'
import ClipboardJS from 'clipboard/dist/clipboard.min';
import $ from 'jquery'
import 'bootstrap'
import 'mathjax/es5/tex-svg'

function mime(str) {
    return CodeMirror.mimeModes[`text/x-${str}`];
}

let cli = new ClipboardJS('.tocopy');
let languages = {
    'c++': mime("c++src"),
    c: mime("c"),
    java: mime("java"),
    python: mime("python"),
}
$(function () {
    $(".tocopy")
        .popover({
            content: "Text copied!",
            placement: "bottom",
            trigger: "manual"
        })
        .on("click", function () {
            $(this).popover("show");
            setTimeout(() => {
                console.log(this);
                $(this).popover("hide");
            }, 1000)
        });
    const langSel = $("#languages");
    for (let lang in languages) {
        let option = $("<option></option>").text(lang);
        langSel.append(option);
    }
    let editor = CodeMirror.fromTextArea(document.getElementById("editor"), {
        mode: CodeMirror.mimeModes['text/x-c++src'],
        theme: "ayu-mirage",
        lineNumbers: true,
        extraKeys: {
            "Ctrl-Space":
                "autocomplete"
        },
        matchBrackets: true,
        autoCloseBrackets: true,
        keyMap: "default",
        styleActiveLine: true
    });
    langSel.on('change', e => {
        editor.setOption("mode", languages[langSel.val()]);
    })
    $('#btn-open-file').click(function () {
        $('#input-open-file').trigger('click');
    });
    $('#input-open-file').on('change', function (e) {
        let fileData = e.target.files[0];
        let reader = new FileReader();
        reader.onload = function () {
            editor.doc.setValue(reader.result);
        };
        reader.readAsText(fileData);
    });
    let vimMode = false;
    $("#vim").on('click', function () {
        vimMode = !vimMode;
        if (vimMode) {
            $(this).addClass('vimMode');
            editor.setOption('keyMap', "vim");
        } else {
            $(this).removeClass('vimMode');
            editor.setOption('keyMap', "default");
        }
    })
})
cli.on('success', function (e) {
    e.clearSelection();
});
// MathJax = {
//     tex: {
//         inlineMath: [['$', '$'], ['\\(', '\\)']]
//     },
//     svg: {
//         fontCache: 'global'
//     }
// };
