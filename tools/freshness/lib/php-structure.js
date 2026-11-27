// The structure of a PHP file, for the editor that adds or removes a place: where the braces open and
// close, whatever the indentation says. The reader of seeders.js works line by line, which is enough
// to read and edit the values of a block; moving the boundaries of a block needs to know what code
// encloses it and where its method ends, and only the braces of PHP tell that.
//
// This is a lexer, not a parser: it skips what is not code (comments, strings, heredocs) and follows
// the braces, the parentheses and a few keywords. What it cannot follow is reported (`unsupported`),
// and the editor then refuses to change the structure of the file.

const WORD = /[A-Za-z_\\\u0080-￿][\w\\\u0080-￿]*/y;
const VARIABLE = /\$([A-Za-z_\u0080-￿][\w\u0080-￿]*)/y;
const HEREDOC = /<<<[ \t]*(['"]?)([A-Za-z_][\w]*)\1\r?\n/y;
// A variable named in a string that PHP interpolates: "$resto", "{$resto->name}", "${resto}".
const IN_STRING = /\$\{?([A-Za-z_\u0080-￿][\w\u0080-￿]*)/g;
const ALTERNATIVE = new Set(['endif', 'endwhile', 'endfor', 'endforeach', 'endswitch', 'enddeclare']);
const CLASS_LIKE = new Set(['class', 'interface', 'trait', 'enum']);

// The tokens of the code: { type: 'var' | 'word' | 'punct', text, line } (line: 0-based). A variable
// has its name (without the $) as text; one found inside a string has `inString`.
function lex(text) {
    const tokens = [];
    let unsupported = null;
    let line = 0;
    let i = 0;
    const newlines = (from, to) => {
        for (let k = from; k < to; k++) if (text[k] === '\n') line++;
    };
    // Skips to the end of what starts at i and ends with `closer`, backslash escapes honoured or not.
    const skipTo = (closer, escapes) => {
        const start = i;
        i += 1;
        while (i < text.length && !text.startsWith(closer, i)) i += escapes && text[i] === '\\' ? 2 : 1;
        const end = Math.min(i, text.length);
        i = end + closer.length;
        return [start + 1, end];
    };
    const mentions = (from, to) => {
        const startLine = line;
        for (const found of text.slice(from, to).matchAll(IN_STRING)) {
            const before = text.slice(from, from + found.index);
            tokens.push({ type: 'var', text: found[1], line: startLine + (before.match(/\n/g)?.length ?? 0), inString: true });
        }
    };

    while (i < text.length) {
        const c = text[i];
        if (c === '\n') { line++; i++; continue; }
        if (/\s/.test(c)) { i++; continue; }
        if (text.startsWith('//', i) || (c === '#' && text[i + 1] !== '[')) {
            while (i < text.length && text[i] !== '\n') i++;
            continue;
        }
        if (text.startsWith('/*', i)) {
            const end = text.indexOf('*/', i + 2);
            const stop = end === -1 ? text.length : end + 2;
            newlines(i, stop);
            i = stop;
            continue;
        }
        if (text.startsWith('<?php', i)) { i += 5; continue; }
        if (text.startsWith('?>', i)) { unsupported ??= { line, why: 'a closing ?> tag' }; break; }
        if (c === "'") {
            const from = i;
            skipTo("'", true);
            newlines(from, i);
            continue;
        }
        if (c === '"' || c === '`') {
            const from = i;
            const [start, end] = skipTo(c, true);
            mentions(start, end);
            newlines(from, i);
            continue;
        }
        HEREDOC.lastIndex = i;
        const heredoc = HEREDOC.exec(text);
        if (heredoc) {
            // Ends on the line that starts (after spaces) with the label.
            const close = new RegExp(`^[ \\t]*${heredoc[2]}(?![\\w])`, 'm');
            const bodyStart = i + heredoc[0].length;
            const found = close.exec(text.slice(bodyStart));
            const bodyEnd = found ? bodyStart + found.index : text.length;
            newlines(i, bodyStart);
            if (heredoc[1] !== "'") mentions(bodyStart, bodyEnd);
            const stop = found ? bodyEnd + found[0].length : text.length;
            newlines(bodyStart, stop);
            i = stop;
            continue;
        }
        VARIABLE.lastIndex = i;
        const variable = VARIABLE.exec(text);
        if (variable) {
            tokens.push({ type: 'var', text: variable[1], line });
            i += variable[0].length;
            continue;
        }
        WORD.lastIndex = i;
        const word = WORD.exec(text);
        if (word) {
            tokens.push({ type: 'word', text: word[0], line });
            i += word[0].length;
            continue;
        }
        const two = text.slice(i, i + 2);
        const punct = two === '->' || two === '::' || two === '=>' ? two : c;
        tokens.push({ type: 'punct', text: punct, line });
        i += punct.length;
    }
    return { tokens, unsupported };
}

// Reads `text` and returns { tokens, unsupported, first(line), last(line) }.
// Each token gets: index; depth, the number of open parentheses and brackets before it; stack, the
// braces open around it, outermost first, each { kind: 'namespace' | 'class' | 'method' | 'closure' | 'other',
// open, close } (the indexes of its two braces; `close` is undefined when it never closes); and
// method, the innermost named function among them. Two tokens between which no brace opens or
// closes share the same `stack` array.
// `unsupported` is { line, why } when the file has something the lexer does not follow, else null.
export function phpStructure(text) {
    const { tokens, unsupported: unread } = lex(text);
    let unsupported = unread;
    let stack = [];
    let depth = 0;
    let pending = null; // { kind, depth }: the next `{` at this depth opens a method, a class or a namespace
    const firstOn = new Map();
    const lastOn = new Map();

    tokens.forEach((token, index) => {
        token.index = index;
        token.depth = depth;
        token.stack = stack;
        token.method = stack.findLast((entry) => entry.kind === 'method') ?? null;
        if (!token.inString) {
            if (!firstOn.has(token.line)) firstOn.set(token.line, token);
            lastOn.set(token.line, token);
        }
        if (token.inString) return;

        if (token.type === 'word') {
            const word = token.text.toLowerCase();
            const next = tokens[index + 1];
            const previous = tokens[index - 1];
            if (ALTERNATIVE.has(word)) unsupported ??= { line: token.line, why: `the alternative syntax (${word})` };
            if (word === 'function') {
                const name = next?.text === '&' ? tokens[index + 2] : next;
                // A method has a name; `function () {` is a closure, which is code around what it holds.
                pending = { kind: name?.type === 'word' ? 'method' : 'closure', depth };
            } else if (CLASS_LIKE.has(word) && previous?.text !== '::' && previous?.text !== '->') {
                pending = { kind: 'class', depth };
            } else if (word === 'namespace' && next?.type === 'word') {
                pending = { kind: 'namespace', depth };
            }
            return;
        }
        if (token.type !== 'punct') return;
        switch (token.text) {
            case '(': case '[': depth++; break;
            case ')': case ']': depth--; break;
            case ';':
                if (pending && pending.depth === depth) pending = null; // an abstract method, `namespace X;`
                break;
            case '{': {
                // The body of a closure opens after its parameters and its `use (...)`, at the depth of its keyword.
                const kind = pending && pending.depth === depth ? pending.kind : 'other';
                if (kind !== 'other') pending = null;
                stack = [...stack, { kind, open: index, close: undefined }];
                break;
            }
            case '}':
                if (stack.length === 0) { unsupported ??= { line: token.line, why: 'a closing brace that opens nowhere' }; break; }
                stack.at(-1).close = index;
                stack = stack.slice(0, -1);
                break;
            default:
        }
    });
    if (stack.length > 0 && !unsupported) unsupported = { line: tokens[stack.at(-1).open].line, why: 'a brace that never closes' };

    return { tokens, unsupported, first: (line) => firstOn.get(line), last: (line) => lastOn.get(line) };
}
