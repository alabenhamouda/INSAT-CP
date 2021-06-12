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
import $ from 'jquery'

function mime(str) {
    return CodeMirror.mimeModes[`text/x-${str}`];
}

function Language(id, name, mime) {
    this.id = id;
    this.name = name;
    this.mime = mime;
}

let languages = [
    new Language(54, 'c++', mime("c++src")),
    new Language(50, 'c', mime("c")),
    new Language(62, 'java', mime("java")),
    new Language(71, 'python', mime("python")),
]
$(function () {
    const langSel = $("#languages");
    for (let lang of languages) {
        let option = $("<option></option>").text(lang.name).val(lang.id);
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
        const lang = languages.find(l => l.id == langSel.val());
        editor.setOption("mode", lang.mime);
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
        $(this).val("");
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
