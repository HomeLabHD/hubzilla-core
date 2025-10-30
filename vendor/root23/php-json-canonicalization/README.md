# php-json-canonicalistaion
Serialize data into canonical way, based on RFC-8785.

RFC-8785 - https://tools.ietf.org/html/rfc8785

Inspired by https://github.com/aywan/php-json-canonicalization & https://github.com/cyberphone

## Installation
```code
composer require root23/php-json-canonicalizator
```

## Usage
```code
$canonicalizator = JsonCanonicalizatorFactory::getInstance();
$canonicalizedJsonString = $canonicalizator->canonicalize($input, false);
$canonicalizedJsonString = $canonicalizator->canonicalize($input, true); // hex
```

## Example input:
```code
{
  "numbers": [333333333.33333329, 1E30, 4.50, 2e-3, 0.000000000000000000000000001],
  "string": "\u20ac$\u000F\u000aA'\u0042\u0022\u005c\\\"\/",
  "literals": [null, true, false]
}
```

## Output:
```code
{"literals":[null,true,false],"numbers":[333333333.3333333,1e+30,4.5,0.002,1e-27],"string":"€$\u000f\nA'B\"\\\\\"/"}
```

## Hexademical Output:
```code
7b 22 6c 69 74 65 72 61 6c 73 22 3a 5b 6e 75 6c 6c 2c 74 72 75 65 2c 66 61 6c 73 65 5d 2c 22 6e
75 6d 62 65 72 73 22 3a 5b 33 33 33 33 33 33 33 33 33 2e 33 33 33 33 33 33 33 2c 31 65 2b 33 30
2c 34 2e 35 2c 30 2e 30 30 32 2c 31 65 2d 32 37 5d 2c 22 73 74 72 69 6e 67 22 3a 22 e2 82 ac 24
5c 75 30 30 30 66 5c 6e 41 27 42 5c 22 5c 5c 5c 5c 5c 22 2f 22 7d

```
