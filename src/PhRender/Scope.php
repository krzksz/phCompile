<?php
/*
 * This file is part of the ngPhRender package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhRender;

/**
 * Data container similar to AngularJS' $scope.
 */
class Scope
{
    public function __construct(array $data = array()) {
        $this->setData($data);
    }

    /**
     * Array containing all of the data.
     *
     * @var array
     */
    protected $data = array();

    /**
     * Sets new data.
     * Existing keys will be replaced with new values.
     *
     * @param array $data New data.
     */
    public function setData(array $data)
    {
        $this->data = array_replace($this->data, $data);
    }

    /**
     * Gets data value corresponding to given access string.
     *
     * Access strings works with both PHP and JS styles, meaning that "foo['bar']"
     * and "foo.bar" will both point to the same value that could correspond to
     * following array:
     *
     * array(
     *     'foo' => array(
     *         'bar' => 'baz'
     *     )
     * )
     *
     * @param string $accessString Access string to wanted value.
     * @return mixed Value corresponding to given access string or null.
     */
    public function getData($accessString = null)
    {
        if($accessString === null) {
            return $this->data;
        }

        $dataKeys = array_map(function($dataKey) {
            return trim($dataKey, '.[]');
        }, $this->parseAccessString($accessString));

        $currentData = $this->data;

        for ($keyIndex = 0, $keysCount = count($dataKeys); $keyIndex < $keysCount; $keyIndex++) {
            if (isset($currentData[$dataKeys[$keyIndex]])) {
                $currentData = $currentData[$dataKeys[$keyIndex]];
                if ($keyIndex === $keysCount - 1) {
                    return $currentData;
                }
            }
        }

        return null;
    }

    /**
     * Tells if value corresponding to given access string exists.
     *
     * @param string $accessString Access string to check.
     * @return bool True if exists, false otherwise.
     */
    public function hasData($accessString)
    {
        return $this->getData($accessString) !== null;
    }

    /**
     * Parses access string and divides it into chunks to unify PHP and JS
     * array access styles.
     *
     * @param string $accessString Access string to parse.
     * @return array Access string chunks.
     */
    protected function parseAccessString($accessString)
    {
        return preg_split("/[\[\.]/", $accessString);
    }
}