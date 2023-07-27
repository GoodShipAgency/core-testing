<?php

namespace Mashbo\CoreTesting\Support\ElementObjects;

class FormElement
{
    public static function prefixFormValue(string $field, string $prefix): string
    {
        if (str_contains($field, '[')) {
            return $prefix . '[' . preg_replace('/\[/', '][', $field, 1);
        }

        return $prefix . "[$field]";
    }

    /** @param array<string, mixed> $values */
    public static function prefixFormValues(array $values, string $prefix): array
    {
        $prefixed = [];
        foreach ($values as $key => $value) {
            $prefixed[self::prefixFormValue($key, $prefix)] = $value;
        }

        return $prefixed;
    }
}
