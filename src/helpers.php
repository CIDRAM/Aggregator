<?php
/**
 * Aggregator helpers file (last modified: 2022.02.12).
 * @link https://github.com/CIDRAM/Aggregator
 *
 * AGGREGATOR COPYRIGHT 2017 and beyond by Caleb Mazalevskis (Maikuolan).
 *
 * License: GNU/GPLv2
 * @see LICENSE.txt
 */

if (!function_exists('aggregate')) {
    /**
     * Aggregate it!
     *
     * @param string $input
     * @return string
     */
    function aggregate($input)
    {
        return (new \CIDRAM\Aggregator\Aggregator())->aggregate($input);
    }
}
