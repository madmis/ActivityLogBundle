<?php

namespace ActivityLogBundle\Service\ActivityLog\EntityFormatter;


/**
 * Class AbstractFormatter
 * @package ActivityLogBundle\Service\ActivityLog\EntityFormatter
 */
abstract class AbstractFormatter
{
    /**
     * @param string $field
     * @param mixed $value
     * @return string|bool|int
     */
    public function normalizeValue($field, $value)
    {
        if (method_exists($this, $field)) {
            return $this->$field($value);
        }

        if (is_array($value)) {
            $value = $this->toComment($value);
        }

        return $value;
    }

    /**
     * Convert assoc array to comment style
     *
     * @param array $data
     * @return string
     */
    public function toComment(array $data)
    {
        $result = [];
        foreach ($data as $key => $value) {
            $result[] = $key . ': ' . $value . ';';
        }

        return implode(PHP_EOL, $result);
    }
}
