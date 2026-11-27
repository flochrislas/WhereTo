import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync, readdirSync } from 'node:fs';
import { spawnSync } from 'node:child_process';
import { join } from 'node:path';
import { phpStructure } from '../lib/php-structure.js';
import { DEFAULT_SEEDERS_DIR } from '../lib/seeders.js';

const FIXTURES = new URL('./fixtures/', import.meta.url).pathname;

// The kinds of the braces around the first token of each line that has one.
const kindsByLine = (text) => {
    const structure = phpStructure(text);
    return text.split('\n').map((_, line) => structure.first(line)?.stack.map((entry) => entry.kind).join('>') ?? null);
};

test('the braces of a class, a method, an if and a closure are told apart', () => {
    const text = [
        '<?php',
        'namespace Database\\Seeders;',
        'class S extends Seeder',
        '{',
        '    public function run(): void',
        '    {',
        '        $a = 1;',
        '        if ($a) {',
        '            $b = 2;',
        '        }',
        '        DB::transaction(function () use ($a) {',
        '            $c = 3;',
        '        });',
        '        $d = S::class;',
        '    }',
        '    abstract protected function other();',
        '    private function more() {',
        '        $e = 5;',
        '    }',
        '}',
    ].join('\n');
    const kinds = kindsByLine(text);
    assert.equal(kinds[6], 'class>method');
    assert.equal(kinds[8], 'class>method>other');
    assert.equal(kinds[11], 'class>method>closure');
    assert.equal(kinds[13], 'class>method');
    assert.equal(kinds[17], 'class>method'); // the abstract method above opened nothing
    assert.equal(phpStructure(text).unsupported, null);
});

test('braces in strings, comments and heredocs open nothing; a variable in a string is seen', () => {
    const text = [
        '<?php',
        'function f() {',
        "    $a = 'it\\'s { not code';",
        '    $b = "a { and {$resto->name} and \\" }";',
        '    // } a comment',
        '    # } another',
        '    /* } and',
        '       } again */',
        '    #[Attribute]',
        '    $c = <<<EOT',
        '      } $inHeredoc {',
        '      EOT;',
        "    $d = <<<'RAW'",
        '    } $inNowdoc',
        '    RAW;',
        '    $e = 1;',
        '}',
    ].join('\n');
    const structure = phpStructure(text);
    assert.equal(structure.unsupported, null);
    assert.equal(structure.first(15).stack.length, 1);
    assert.equal(structure.first(15).text, 'e');
    const inStrings = structure.tokens.filter((t) => t.inString).map((t) => `${t.text}@${t.line}`);
    assert.deepEqual(inStrings, ['resto@3', 'inHeredoc@10']);
    assert.equal(structure.first(3).text, 'b'); // a string does not start or end a line of code
});

test('what the lexer does not follow is reported', () => {
    assert.match(phpStructure('<?php\nif ($a):\n$b = 1;\nendif;').unsupported.why, /alternative syntax \(endif\)/);
    assert.match(phpStructure('<?php\n$a = 1;\n?>\n<p>{</p>').unsupported.why, /\?>/);
    assert.match(phpStructure('<?php\nfunction f() {\n').unsupported.why, /never closes/);
    assert.match(phpStructure('<?php\n}\n').unsupported.why, /opens nowhere/);
});

// PHP's own tokenizer, when PHP is installed: the lines of the braces of code must be the same.
const PHP_BRACES = `
$open = []; $out = [];
foreach (PhpToken::tokenize(stream_get_contents(STDIN)) as $t) {
    if ($t->id === T_CURLY_OPEN || $t->id === T_DOLLAR_OPEN_CURLY_BRACES) { $open[] = false; continue; }
    if ($t->text === '{') { $open[] = true; $out[] = '{' . $t->line; continue; }
    if ($t->text === '}' && array_pop($open)) { $out[] = '}' . $t->line; }
}
echo implode(' ', $out);
`;

test("the braces are those of PHP's tokenizer, on the fixtures and on the real seeders", (t) => {
    if (spawnSync('php', ['-v']).error) return t.skip('php is not installed');
    const files = [FIXTURES, DEFAULT_SEEDERS_DIR].flatMap((dir) => readdirSync(dir).filter((f) => f.endsWith('.php')).map((f) => join(dir, f)));
    assert.ok(files.length >= 6);
    for (const file of files) {
        const text = readFileSync(file, 'utf8');
        const structure = phpStructure(text);
        assert.equal(structure.unsupported, null, file);
        const ours = structure.tokens.filter((token) => !token.inString && (token.text === '{' || token.text === '}')).map((token) => token.text + (token.line + 1)).join(' ');
        const php = spawnSync('php', ['-r', PHP_BRACES], { input: text, encoding: 'utf8' });
        assert.equal(php.status, 0, php.stderr);
        assert.equal(ours, php.stdout, file);
    }
});
