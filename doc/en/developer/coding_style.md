### Coding style 

In the interest of consistency, we use the following code style. We also accept patches that use other styles, but please try to use a consistent coding style if possible. We will not argue or discuss the merits of this style, and it is irrelevant what project ‘xyz’ uses. This is not project ‘xyz’. This is a baseline to try to keep the code readable now and in the future. 

- All comments should be in English.
- We use Doxygen to create documentation. This has not been used consistently, but it is highly recommended to learn and use it.
- Indentation is mainly done by tabs with a tab width of 4.
- String concatenations and operators should be separated by spaces. e.g. `$foo = $bar . ‘abc’;` instead of `$foo=$bar.”abc’;`
- In general, we use single quotes for string variables and double quotes for SQL statements. ‘Here documents’ should be avoided. Sometimes using strings in double quotes with variable substitution is the most efficient way to create the string. In most cases, you should use single quotes.
- Use spaces liberally to improve readability. When creating arrays with many elements, a key/value pair is often set per line, which is indented accordingly from the parent line. Lining up the assignment operators requires a little more work, but also increases readability.
- In general, opening curly brackets are placed on the same line as what the bracket opens. They are the last character in the line. Closing brackets are placed on a separate line.
- Some functions take arguments in argc/argv style, such as main() in C or function args in Bash or Perl. Urls are split within a module. e.g. for `http://example.com/module/arg1/arg2` $this->argc is 3 (integer) and $this->argv contains: [0] => ‘module’, [1] => ‘arg1’, [2] => ‘arg2’. Only one argument is ever specified. If a naked domain URL is specified, $this->argv[0] is set to ‘home’.