<?php

declare(strict_types=1);

namespace Root23\JsonCanonicalizer;

final class JsonCanonicalizer implements JsonCanonicalizerInterface
{
    use ArrayHelperTrait;

    public const JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
    public const ENCODING = 'UTF-16BE';

    /**
     * @throws \JsonException
     */
    public function canonicalize($data): string
    {
        ob_start();
        $this->encode($data);

        return ob_get_clean();
    }

    /**
     * @throws \JsonException
     */
    public function canonicalizeAsHex($data): string
    {
        ob_start();
        $this->encode($data);

        return Converter::toHex(ob_get_clean());
    }

    /**
     * @throws \JsonException
     */
    private function encode($item): void
    {
        if (is_float($item)) {
            echo Converter::toEs6NumberFormat($item);
            return;
        }

        if (is_null($item) || is_scalar($item)) {
            echo json_encode($item, JSON_THROW_ON_ERROR | self::JSON_FLAGS);
            return;
        }

        if (is_array($item) && !self::isArrayAssoc($item)) {
            echo '[';

            $next = false;
            foreach ($item as $element) {
                if ($next) {
                    echo ',';
                }
                $next = true;
                $this->encode($element);
            }
            echo ']';

            return;
        }

        if (is_object($item)) {
            $item = (array)$item;
        }

        uksort($item, static function (string $a, string $b) {
            $a = mb_convert_encoding($a, self::ENCODING);
            $b = mb_convert_encoding($b, self::ENCODING);

            return strcmp($a, $b);
        });

        echo '{';
        $next = false;
        foreach ($item as $key => $value) {
            if ($next) {
                echo ',';
            }
            $next = true;
            $outKey = json_encode((string)$key, JSON_THROW_ON_ERROR | self::JSON_FLAGS);
            echo $outKey, ':', $this->encode($value);
        }
        echo '}';
    }
}
