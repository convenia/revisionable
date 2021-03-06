<?php

namespace Convenia\Revisionable;

/*
 * FieldFormatter.
 *
 * Allows formatting of fields
 *
 * (c) Venture Craft <http://www.venturecraft.com.au>
 */

use Carbon\Carbon;

/**
 * Class FieldFormatter.
 */
class FieldFormatter
{
    /**
     * Format the value according to the provided formats.
     *
     * @param  $key
     * @param  $value
     * @param  $formats
     *
     * @return string formatted value
     */
    public static function format($key, $value, $formats)
    {
        foreach ($formats as $pkey => $format) {
            $parts = explode(':', $format);
            if (count($parts) === 1) {
                continue;
            }

            if ($pkey == $key) {
                $method = array_shift($parts);

                if (method_exists(get_class(), $method)) {
                    return self::$method($value, implode(':', $parts));
                }
                break;
            }
        }

        return $value;
    }

    /**
     * Check if a field is empty.
     *
     * @param $value
     * @param array $options
     *
     * @return string
     */
    public static function isEmpty($value, $options = [])
    {
        $valueBool = isset($value) && $value != '';

        return sprintf(self::boolean($valueBool, $options), $value);
    }

    /**
     * Boolean.
     *
     * @param       $value
     * @param array $options The false / true values to return
     *
     * @return string Formatted version of the boolean field
     */
    public static function boolean($value, $options = null)
    {
        if (! is_null($options)) {
            $options = explode('|', $options);
        }

        if (count($options) != 2) {
            $options = ['No', 'Yes'];
        }

        return $options[(bool) $value];
    }

    /**
     * Multiple.
     *
     * @param       $value
     * @param array $options The multiple options values to return
     *
     * @return string Formatted version of the field
     */
    public static function multiple($value, $options = null)
    {
        if ($options !== null) {
            $options = explode('|', $options);
        }

        if (count($options) === 0) {
            return $value;
        }

        $formatMap = collect($options)->mapWithKeys(function ($format) {
            $resultFormat = explode(',', $format);

            return [$resultFormat[1] => $resultFormat[0]];
        })->flip();

        return isset($formatMap[$value]) ? $formatMap[$value] : $value;
    }

    /**
     * Format the string response, default is to just return the string.
     *
     * @param  $value
     * @param  $format
     *
     * @return formatted string
     */
    public static function string($value, $format = null)
    {
        if (is_null($format)) {
            $format = '%s';
        }

        return sprintf($format, $value);
    }

    /**
     * Format the datetime.
     *
     * @param string $value
     * @param string $format
     *
     * @return string|null formatted datetime
     */
    public static function datetime($value, $format = 'Y-m-d H:i:s')
    {
        if (empty($value)) {
            return;
        }

        return (new Carbon($value))->format($format);
    }
}
