import './styles/problem.css'
import ClipboardJS from 'clipboard/dist/clipboard.min';
let cli=new ClipboardJS('.tocopy');
//TODO add a copied message
cli.on('success', function(e) {
    console.info('Action:', e.action);
    console.info('Text:', e.text);
    console.info('Trigger:', e.trigger);

    e.clearSelection();
});

console.log("here");
