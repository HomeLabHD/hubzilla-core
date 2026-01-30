<?php

namespace Zotlabs\Lib;

require_once('include/html2plain.php');

class MessageFilter
{
    protected $lastMatch = '';
    protected $item = null;
    protected $include = '';
    protected $exclude = '';
    protected $options = [];
    protected $tags = null;
    protected $language = '';
    protected $text = '';
    protected $excludeRules = [];
    protected $includeRules = [];

    public function __construct($item, $include = '', $exclude = '', $options = [])
    {
        $this->item = $item;
        $this->include = $include;
        $this->exclude = $exclude;
        $this->options = $options;
        $this->setup();
    }

    protected function setup()
    {
        // Option: plaintext
        // Improve language detection by providing a plaintext version of $item['body'] which has no markup constructs/tags.

        if (array_key_exists('plaintext', $this->options)) {
            $this->text = $this->options['plaintext'];
        } else {
            $this->text = $this->item['body'];
        }

        $this->language = '';

        // Language matching is a bit tricky, because the language can be ambiguous (detect_language() returns '').
        // If the language is ambiguous, the message will pass (be accepted) regardless of language rules.

        if (str_contains($this->include, 'lang=')
            || str_contains($this->exclude, 'lang=')
            || str_contains($this->include, 'lang!=')
            || str_contains($this->exclude, 'lang!=')) {
            $this->language = detect_language($this->text);
        }

        $this->tags = ((isset($this->item['term']) && is_array($this->item['term'])
            && count($this->item['term'])) ? $this->item['term'] : null);

        $this->excludeRules = $this->parse($this->exclude);
        $this->includeRules = $this->parse($this->include);

    }

    protected function parse($string): array
    {
        $rules = [];
        if (! strlen($string)) {
            return $rules;
        }

        $phrases = preg_split("/(\s\|\|\s|\s&&\s|\n)/", $string, flags: PREG_SPLIT_DELIM_CAPTURE);

        if (!$phrases) {
            return $rules;
        }
        for ($index = 0; $index < count($phrases); $index ++) {
            // Even indices are rules and odd indices are operations, linefeed is an implict OR.
            if (!($index & 1)) {
                $currentRule = ['operation' => '', 'rule' => $phrases[$index]];
                if ($index && isset($phrases[$index - 1])) {
                    $currentRule['operation'] = $phrases[$index - 1];
                    if ($currentRule['operation'] === "\n") {
                        $currentRule['operation'] = ' || ';
                    }
                    $index++;
                }
                $rules[] = $currentRule;
            }
        }
        return $rules;
    }



    public function evaluate(): bool
    {

        $previousResult = $newResult = null;

        // exclude always has priority

        $exclude = $this->excludeRules;
        $include = $this->includeRules;

        if ($exclude) {
            foreach ($exclude as $rule) {
                if (!strlen(trim($rule['rule']))) {
                    continue;
                }
                if (!strlen($this->language) && ((str_starts_with($rule['rule'], 'lang=')) || (str_starts_with($rule['rule'], 'lang!=')))) {
                    continue;
                }

                $result = $this->evaluateRule($rule['rule']);

                switch ($rule['operation']) {
                    case '':
                        $previousResult = $newResult = $result;
                        break;
                    case ' || ':
                        $newResult = $previousResult || $result;
                        break;
                    case ' && ':
                        $newResult = $previousResult && $result;
                        break;
                }
            }
            if ($newResult) {
                return false;
            }
        }

        $previousResult = $newResult = null;

        if ($include) {
            foreach ($include as $rule) {
                if (!strlen(trim($rule['rule']))) {
                    continue;
                }
                if (!strlen($this->language) && ((str_starts_with($rule['rule'], 'lang=')) || (str_starts_with($rule['rule'], 'lang!=')))) {
                    continue;
                }
                $result = $this->evaluateRule($rule['rule']);

                switch ($rule['operation']) {
                    case '':
                        $previousResult = $newResult = $result;
                        break;
                    case ' || ':
                        $newResult = $previousResult || $result;
                        break;
                    case ' && ':
                        $newResult = $previousResult && $result;
                        break;

                }
            }
        }
        return $newResult ?? true;
    }

    protected function evaluateRule($ruleText): bool
    {
        $ruleText = trim($ruleText);

        if (($this->language) && ((str_starts_with($ruleText, 'lang=')) || (str_starts_with($ruleText, 'lang!=')))) {
            if (str_starts_with($ruleText, 'lang=') && strcasecmp($this->language, trim(substr($ruleText, 5))) == 0) {
                $this->lastMatch = $ruleText;
                return true;
            } elseif (str_starts_with($ruleText, 'lang!=') && strcasecmp($this->language, trim(substr($ruleText, 6))) != 0) {
                $this->lastMatch = $ruleText;
                return true;
            }
        } elseif (str_starts_with($ruleText, 'until=')) {
            $until = strtotime(trim(substr($ruleText, 6)));

            if ($until > strtotime($this->item['created'] . ' UTC')) {
                $this->lastMatch = $ruleText;
                return true;
            }
        } elseif (str_starts_with($ruleText, '#') && $this->tags) {
            // #hashtag match
            foreach ($this->tags as $t) {
                if ((($t['ttype'] == TERM_HASHTAG) || ($t['ttype'] == TERM_COMMUNITYTAG)) && (!strcasecmp($t['term'], substr($ruleText, 1)) || (substr($ruleText, 1) === '*'))) {
                    $this->lastMatch = $ruleText;
                    return true;
                }
            }
            // hashtag count match
            if (substr($ruleText, 1, 1) === '>') {
                $hashtagLimit = (int)substr($ruleText, 2);
                $hashtagCount = 0;
                foreach ($this->tags as $t) {
                    if ($t['ttype'] == TERM_HASHTAG || $t['ttype'] == TERM_COMMUNITYTAG) {
                        $hashtagCount++;
                    }
                }
                if ($hashtagLimit && $hashtagCount > $hashtagLimit) {
                    $this->lastMatch = $ruleText;
                    return true;
                }
            }
        } elseif (str_starts_with($ruleText, '@') && $this->tags) {
            // @mention match
            foreach ($this->tags as $t) {
                if ((($t['ttype'] == TERM_MENTION && (!strcasecmp($t['term'], substr($ruleText, 1)))) || (substr($ruleText, 1) === '*'))) {
                    $this->lastMatch = $ruleText;
                    return true;
                }
            }
            // mention count match
            if (substr($ruleText, 1, 1) === '>') {
                $mentionLimit = (int)substr($ruleText, 2);
                $mentionCount = 0;
                foreach ($this->tags as $t) {
                    if ($t['ttype'] == TERM_MENTION) {
                        $mentionCount++;
                    }
                }
                if ($mentionLimit && $mentionCount > $mentionLimit) {
                    $this->lastMatch = $ruleText;
                    return true;
                }
            }
        } elseif (str_starts_with($ruleText, '$') && $this->tags) {
            foreach ($this->tags as $t) {
                if (($t['ttype'] == TERM_CATEGORY) && (($t['term'] === substr($ruleText, 1)) || (substr($ruleText, 1) === '*'))) {
                    $this->lastMatch = $ruleText;
                    return true;
                }
            }
        } elseif (str_starts_with($ruleText, '?+') && is_array($this->item['obj'])) {
            if ($this->test_condition(substr($ruleText, 2), $this->item['obj'])) {
                $this->lastMatch = $ruleText;
                return true;
            }
        } elseif (str_starts_with($ruleText, '?')) {
            $this->item['ua'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
            if (empty($this->item['app'])) {
                $author_app = $this->item['author']['site_project'] ?? '';
                if (!$author_app && isset($this->item['author'])) {
                    if (str_contains($this->item['author']['xchan_hash'], 'threads.net') || str_contains($this->item['author']['xchan_hash'], 'threads.com')) {
                        $author_app = 'threads';
                    }
                }
                $this->item['app'] = $author_app;
            }
            if ($this->test_condition(substr($ruleText, 1), $this->item)) {
                unset($this->item['ua']);
                $this->lastMatch = $ruleText;
                return true;
            }
            unset($this->item['ua']);
        } elseif ((str_starts_with($ruleText, '/')) && preg_match($ruleText, $this->item['body'])) {
            $this->lastMatch = $ruleText;
            return true;
        } elseif (stristr($this->item['body'], $ruleText) !== false) {
            $this->lastMatch = $ruleText;
            return true;
        }
        return false;
    }


    public function getLastMatch(): string
    {
        return $this->lastMatch;
    }
    public function setLastMatch($string): MessageFilter
    {
        $this->lastMatch = $string;
        return $this;
    }
    /**
     * @brief Test for Conditional Execution conditions. Shamelessly ripped off from src/Render/Comanche
     *
     * This is extensible. The first version of variable testing supports tests of the forms:
     *
     * - ?foo ~= baz will check if item.foo contains the string 'baz';
     * - ?foo == baz will check if item.foo is the string 'baz';
     * - ?foo != baz will check if item.foo is not the string 'baz';
     * - ?foo // baz will check if item.foo matches the regular expression 'baz';
     * - ?foo >= 3 will check if item.foo is greater than or equal to 3;
     * - ?foo > 3 will check if item.foo is greater than 3;
     * - ?foo <= 3 will check if item.foo is less than or equal to 3;
     * - ?foo < 3 will check if item.foo is less than 3;
     * - ?foo & 2 will check if item.foo has the second bit set.
     * - ?foo !& 2 will check if item.foo does not have the second bit set.
     *
     * - ?foo {} baz which will check if 'baz' is an array element in item.foo
     * - ?foo {*} baz which will check if 'baz' is an array key in item.foo
     * - ?foo which will check for a return of a true condition for item.foo;
     * - ?!foo which will check for a return of a false condition for item.foo;
     *
     * The values 0, '', an empty array, and an unset value will all evaluate to false.
     *
     * @param string $s
     * @param array $item
     * @return bool
     */
    protected function test_condition($s,$item)
    {
        $s = trim($s);

        if (preg_match('/(.*?)\s&\s(.*?)$/', $s, $matches)) {
            $x = ((array_key_exists(trim($matches[1]),$item)) ? $item[trim($matches[1])] : EMPTY_STR);
            if ($x & (int) trim($matches[2])) {
                return true;
            }
            return false;
        }

        if (preg_match('/(.*?)\s!&\s(.*?)$/', $s, $matches)) {
            $x = ((array_key_exists(trim($matches[1]),$item)) ? $item[trim($matches[1])] : EMPTY_STR);
            if (!($x & (int) trim($matches[2]))) {
                return true;
            }
            return false;
        }

        if (preg_match('/(.*?)\s~=\s(.*?)$/', $s, $matches)) {
            $x = ((array_key_exists(trim($matches[1]),$item)) ? $item[trim($matches[1])] : EMPTY_STR);
            if (stripos($x, trim($matches[2])) !== false) {
                return true;
            }
            return false;
        }

        if (preg_match('/(.*?)\s==\s(.*?)$/', $s, $matches)) {
            $x = ((array_key_exists(trim($matches[1]),$item)) ? $item[trim($matches[1])] : EMPTY_STR);
            if ($x == trim($matches[2])) {
                return true;
            }
            return false;
        }

        if (preg_match('/(.*?)\s!=\s(.*?)$/', $s, $matches)) {
            $x = ((array_key_exists(trim($matches[1]),$item)) ? $item[trim($matches[1])] : EMPTY_STR);
            if ($x != trim($matches[2])) {
                return true;
            }
            return false;
        }

        if (preg_match('/(.*?)\s\/\/\s(.*?)$/', $s, $matches)) {
            $x = ((array_key_exists(trim($matches[1]),$item)) ? $item[trim($matches[1])] : EMPTY_STR);
            if (substr(trim($matches[2]),0,1) !== substr(trim($matches[2]),-1)) {
                $matches[2] = '/' . trim($matches[2]) . '/';
            }
            if (preg_match(trim($matches[2]), $x)) {
                return true;
            }
            return false;
        }

        if (preg_match('/(.*?)\s>=\s(.*?)$/', $s, $matches)) {
            $x = ((array_key_exists(trim($matches[1]),$item)) ? $item[trim($matches[1])] : EMPTY_STR);
            if ($x >= trim($matches[2])) {
                return true;
            }
            return false;
        }

        if (preg_match('/(.*?)\s<=\s(.*?)$/', $s, $matches)) {
            $x = ((array_key_exists(trim($matches[1]),$item)) ? $item[trim($matches[1])] : EMPTY_STR);
            if ($x <= trim($matches[2])) {
                return true;
            }
            return false;
        }

        if (preg_match('/(.*?)\s>\s(.*?)$/', $s, $matches)) {
            $x = ((array_key_exists(trim($matches[1]),$item)) ? $item[trim($matches[1])] : EMPTY_STR);
            if ($x > trim($matches[2])) {
                return true;
            }
            return false;
        }

        if (preg_match('/(.*?)\s<\s(.*?)$/', $s, $matches)) {
            $x = ((array_key_exists(trim($matches[1]),$item)) ? $item[trim($matches[1])] : EMPTY_STR);
            if ($x < trim($matches[2])) {
                return true;
            }
            return false;
        }

        // Array contains value
        if (preg_match('/(.*?)\s\{\}\s(.*?)$/', $s, $matches)) {
            $x = ((array_key_exists(trim($matches[1]),$item)) ? $item[trim($matches[1])] : EMPTY_STR);
            if (is_array($x) && in_array(trim($matches[2]), $x)) {
                return true;
            }
            return false;
        }

        // Array contains key
        if (preg_match('/(.*?)\s\{\*}\s(.*?)$/', $s, $matches)) {
            $x = ((array_key_exists(trim($matches[1]),$item)) ? $item[trim($matches[1])] : EMPTY_STR);
            if (is_array($x) && array_key_exists(trim($matches[2]), $x)) {
                return true;
            }
            return false;
        }

        // Ordering of this check (for falseness) with relation to the following one (check for truthiness) is important.
        if (preg_match('/!(.*?)$/', $s, $matches)) {
            $x = ((array_key_exists(trim($matches[1]),$item)) ? $item[trim($matches[1])] : EMPTY_STR);
            if (!$x) {
                return true;
            }
            return false;
        }

        if (preg_match('/(.*?)$/', $s, $matches)) {
            $x = ((array_key_exists(trim($matches[1]),$item)) ? $item[trim($matches[1])] : EMPTY_STR);
            if ($x) {
                return true;
            }
            return false;
        }
        return false;
    }
}
