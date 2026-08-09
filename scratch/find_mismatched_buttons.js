const fs = require('fs');

const content = fs.readFileSync('/Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-workspace/views/view-canvas.php', 'utf8');

// Parse the whole content across lines.
// We find all `<button` and `</button>` matches and record their indices and lines.
const buttonRegex = /<button\b[\s\S]*?>|<\/button>/g;
let match;
const stack = [];

// Helper to get line number from character index
function getLineNumber(index) {
    return content.substring(0, index).split('\n').length;
}

while ((match = buttonRegex.exec(content)) !== null) {
    const tag = match[0];
    const isClosing = tag.startsWith('</');
    const lineNum = getLineNumber(match.index);
    if (!isClosing) {
        stack.push({ lineNum, tag });
    } else {
        if (stack.length === 0) {
            console.log(`Error: Closing button tag without opening tag on line ${lineNum}: ${tag}`);
        } else {
            stack.pop();
        }
    }
}

while (stack.length > 0) {
    const unclosed = stack.pop();
    console.log(`Error: Unclosed button tag opened on line ${unclosed.lineNum}: ${unclosed.tag.substring(0, 100)}...`);
}
console.log('Button tag analysis complete.');
