<?php
/**
 * Aggregator v1.0.0 (last modified: 2017.09.21).
 *
 * Description: A stand-alone class implementation of the IPv4+IPv6 IP+CIDR
 * aggregator from CIDRAM.
 *
 * Homepage: https://cidram.github.io/
 *
 * Documentation: https://github.com/CIDRAM/Aggregator/blob/master/README.md
 *
 * AGGREGATOR COPYRIGHT 2017 and beyond by Caleb Mazalevskis (Maikuolan).
 *
 * License: GNU/GPLv2
 * @see LICENSE.txt
 */

class Aggregator
{

    /** Input. */
    public $Input = '';

    /** Outout. */
    public $Output = '';

    /** Results switch. */
    public $Results = false;

    /** Number of lines for aggregation entered. */
    public $NumberEntered = 0;

    /** Number of lines for aggregation rejected. */
    public $NumberRejected = 0;

    /** Number of lines for aggregation accepted. */
    public $NumberAccepted = 0;

    /** Number of lines aggregated or merged. */
    public $NumberMerged = 0;

    /** Number of lines returned. */
    public $NumberReturned = 0;

    /** Time consumed while aggregating data. */
    public $ProcessingTime = 0;

    /**
     * Tests whether $Addr is an IPv4 address, and if it is, expands its potential
     * factors (i.e., constructs an array containing the CIDRs that contain $Addr).
     * Returns false if $Addr is *not* an IPv4 address, and otherwise, returns the
     * contructed array.
     *
     * Adapted from CIDRAM/CIDRAM->vault/functions.php->$CIDRAM['ExpandIPv4']().
     *
     * @param string $Addr Refer to the description above.
     * @param bool $ValidateOnly If true, just checks if the IP is valid only.
     * @return bool|array Refer to the description above.
     */
    public function ExpandIPv4($Addr, $ValidateOnly = false)
    {
        if (!preg_match(
            '/^([01]?[0-9]{1,2}|2[0-4][0-9]|25[0-5])\.([01]?[0-9]{1,2}|2[0-4][0-' .
            '9]|25[0-5])\.([01]?[0-9]{1,2}|2[0-4][0-9]|25[0-5])\.([01]?[0-9]{1,2' .
            '}|2[0-4][0-9]|25[0-5])$/i',
        $Addr, $Octets)) {
            return false;
        }
        if ($ValidateOnly) {
            return true;
        }
        $CIDRs = array(0 => ($Octets[1] < 128) ? '0.0.0.0/1' : '128.0.0.0/1');
        $CIDRs[1] = (floor($Octets[1] / 64) * 64) . '.0.0.0/2';
        $CIDRs[2] = (floor($Octets[1] / 32) * 32) . '.0.0.0/3';
        $CIDRs[3] = (floor($Octets[1] / 16) * 16) . '.0.0.0/4';
        $CIDRs[4] = (floor($Octets[1] / 8) * 8) . '.0.0.0/5';
        $CIDRs[5] = (floor($Octets[1] / 4) * 4) . '.0.0.0/6';
        $CIDRs[6] = (floor($Octets[1] / 2) * 2) . '.0.0.0/7';
        $CIDRs[7] = $Octets[1] . '.0.0.0/8';
        $CIDRs[8] = $Octets[1] . '.' . (($Octets[2] < 128) ? '0' : '128') . '.0.0/9';
        $CIDRs[9] = $Octets[1] . '.' . (floor($Octets[2] / 64) * 64) . '.0.0/10';
        $CIDRs[10] = $Octets[1] . '.' . (floor($Octets[2] / 32) * 32) . '.0.0/11';
        $CIDRs[11] = $Octets[1] . '.' . (floor($Octets[2] / 16) * 16) . '.0.0/12';
        $CIDRs[12] = $Octets[1] . '.' . (floor($Octets[2] / 8) * 8) . '.0.0/13';
        $CIDRs[13] = $Octets[1] . '.' . (floor($Octets[2] / 4) * 4) . '.0.0/14';
        $CIDRs[14] = $Octets[1] . '.' . (floor($Octets[2] / 2) * 2) . '.0.0/15';
        $CIDRs[15] = $Octets[1] . '.' . $Octets[2] . '.0.0/16';
        $CIDRs[16] = $Octets[1] . '.' . $Octets[2] . '.' . (($Octets[3] < 128) ? '0' : '128') . '.0/17';
        $CIDRs[17] = $Octets[1] . '.' . $Octets[2] . '.' . (floor($Octets[3] / 64) * 64) . '.0/18';
        $CIDRs[18] = $Octets[1] . '.' . $Octets[2] . '.' . (floor($Octets[3] / 32) * 32) . '.0/19';
        $CIDRs[19] = $Octets[1] . '.' . $Octets[2] . '.' . (floor($Octets[3] / 16) * 16) . '.0/20';
        $CIDRs[20] = $Octets[1] . '.' . $Octets[2] . '.' . (floor($Octets[3] / 8) * 8) . '.0/21';
        $CIDRs[21] = $Octets[1] . '.' . $Octets[2] . '.' . (floor($Octets[3] / 4) * 4) . '.0/22';
        $CIDRs[22] = $Octets[1] . '.' . $Octets[2] . '.' . (floor($Octets[3] / 2) * 2) . '.0/23';
        $CIDRs[23] = $Octets[1] . '.' . $Octets[2] . '.' . $Octets[3] . '.0/24';
        $CIDRs[24] = $Octets[1] . '.' . $Octets[2] . '.' . $Octets[3] . '.' . (($Octets[4] < 128) ? '0' : '128') . '/25';
        $CIDRs[25] = $Octets[1] . '.' . $Octets[2] . '.' . $Octets[3] . '.' . (floor($Octets[4] / 64) * 64) . '/26';
        $CIDRs[26] = $Octets[1] . '.' . $Octets[2] . '.' . $Octets[3] . '.' . (floor($Octets[4] / 32) * 32) . '/27';
        $CIDRs[27] = $Octets[1] . '.' . $Octets[2] . '.' . $Octets[3] . '.' . (floor($Octets[4] / 16) * 16) . '/28';
        $CIDRs[28] = $Octets[1] . '.' . $Octets[2] . '.' . $Octets[3] . '.' . (floor($Octets[4] / 8) * 8) . '/29';
        $CIDRs[29] = $Octets[1] . '.' . $Octets[2] . '.' . $Octets[3] . '.' . (floor($Octets[4] / 4) * 4) . '/30';
        $CIDRs[30] = $Octets[1] . '.' . $Octets[2] . '.' . $Octets[3] . '.' . (floor($Octets[4] / 2) * 2) . '/31';
        $CIDRs[31] = $Octets[1] . '.' . $Octets[2] . '.' . $Octets[3] . '.' . $Octets[4] . '/32';
        return $CIDRs;
    }

    /**
     * Tests whether $Addr is an IPv6 address, and if it is, expands its potential
     * factors (i.e., constructs an array containing the CIDRs that contain $Addr).
     * Returns false if $Addr is *not* an IPv6 address, and otherwise, returns the
     * contructed array.
     *
     * Adapted from CIDRAM/CIDRAM->vault/functions.php->$CIDRAM['ExpandIPv6']().
     *
     * @param string $Addr Refer to the description above.
     * @param bool $ValidateOnly If true, just checks if the IP is valid only.
     * @return bool|array Refer to the description above.
     */
    public function ExpandIPv6($Addr, $ValidateOnly = false)
    {
        /**
         * The REGEX pattern used by this `preg_match` call was adapted from the
         * IPv6 REGEX pattern that can be found at
         * http://sroze.io/2008/10/09/regex-ipv4-et-ipv6/
         */
        if (!preg_match(
            '/^(([0-9a-f]{1,4}\:){7}[0-9a-f]{1,4})|(([0-9a-f]{1,4}\:){6}\:[0-9a-' .
            'f]{1,4})|(([0-9a-f]{1,4}\:){5}\:([0-9a-f]{1,4}\:)?[0-9a-f]{1,4})|((' .
            '[0-9a-f]{1,4}\:){4}\:([0-9a-f]{1,4}\:){0,2}[0-9a-f]{1,4})|(([0-9a-f' .
            ']{1,4}\:){3}\:([0-9a-f]{1,4}\:){0,3}[0-9a-f]{1,4})|(([0-9a-f]{1,4}' .
            '\:){2}\:([0-9a-f]{1,4}\:){0,4}[0-9a-f]{1,4})|(([0-9a-f]{1,4}\:){6}(' .
            '(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b).){3}(\b((25[0-5])|(' .
            '1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9a-f]{1,4}\:){0,5}\:((\b((25' .
            '[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b).){3}(\b((25[0-5])|(1\d{2})' .
            '|(2[0-4]\d)|(\d{1,2}))\b))|(\:\:([0-9a-f]{1,4}\:){0,5}((\b((25[0-5]' .
            ')|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b).){3}(\b((25[0-5])|(1\d{2})|(2[0' .
            '-4]\d)|(\d{1,2}))\b))|([0-9a-f]{1,4}\:\:([0-9a-f]{1,4}\:){0,5}[0-9a' .
            '-f]{1,4})|(\:\:([0-9a-f]{1,4}\:){0,6}[0-9a-f]{1,4})|(([0-9a-f]{1,4}' .
            '\:){1,7}\:)$/i',
        $Addr)) {
            return false;
        }
        if ($ValidateOnly) {
            return true;
        }
        $NAddr = $Addr;
        if (preg_match('/^\:\:/i', $NAddr)) {
            $NAddr = '0' . $NAddr;
        }
        if (preg_match('/\:\:$/i', $NAddr)) {
            $NAddr .= '0';
        }
        if (substr_count($NAddr, '::')) {
            $c = 7 - substr_count($Addr, ':');
            $Arr = array(':0:', ':0:0:', ':0:0:0:', ':0:0:0:0:', ':0:0:0:0:0:', ':0:0:0:0:0:0:');
            if (!isset($Arr[$c])) {
                return false;
            }
            $NAddr = str_replace('::', $Arr[$c], $Addr);
            unset($Arr);
        }
        $NAddr = explode(':', $NAddr);
        if (count($NAddr) !== 8) {
            return false;
        }
        $NAddr[0] = hexdec($NAddr[0]);
        $NAddr[1] = hexdec($NAddr[1]);
        $NAddr[2] = hexdec($NAddr[2]);
        $NAddr[3] = hexdec($NAddr[3]);
        $NAddr[4] = hexdec($NAddr[4]);
        $NAddr[5] = hexdec($NAddr[5]);
        $NAddr[6] = hexdec($NAddr[6]);
        $NAddr[7] = hexdec($NAddr[7]);
        $CIDRs = array(0 => ($NAddr[0] < 32768) ? '0::/1' : '8000::/1');
        $CIDRs[1] = dechex(floor($NAddr[0] / 16384) * 16384) . '::/2';
        $CIDRs[2] = dechex(floor($NAddr[0] / 8192) * 8192) . '::/3';
        $CIDRs[3] = dechex(floor($NAddr[0] / 4096) * 4096) . '::/4';
        $CIDRs[4] = dechex(floor($NAddr[0] / 2048) * 2048) . '::/5';
        $CIDRs[5] = dechex(floor($NAddr[0] / 1024) * 1024) . '::/6';
        $CIDRs[6] = dechex(floor($NAddr[0] / 512) * 512) . '::/7';
        $CIDRs[7] = dechex(floor($NAddr[0] / 256) * 256) . '::/8';
        $CIDRs[8] = dechex(floor($NAddr[0] / 128) * 128) . '::/9';
        $CIDRs[9] = dechex(floor($NAddr[0] / 64) * 64) . '::/10';
        $CIDRs[10] = dechex(floor($NAddr[0] / 32) * 32) . '::/11';
        $CIDRs[11] = dechex(floor($NAddr[0] / 16) * 16) . '::/12';
        $CIDRs[12] = dechex(floor($NAddr[0] / 8) * 8) . '::/13';
        $CIDRs[13] = dechex(floor($NAddr[0] / 4) * 4) . '::/14';
        $CIDRs[14] = dechex(floor($NAddr[0] / 2) * 2) . '::/15';
        $NAddr[0] = dechex($NAddr[0]);
        $CIDRs[15] = $NAddr[0] . '::/16';
        $CIDRs[16] = ($NAddr[1] < 32768) ? $NAddr[0] . '::/17' : $NAddr[0] . ':8000::/17';
        $CIDRs[17] = $NAddr[0] . ':' . dechex(floor($NAddr[1] / 16384) * 16384) . '::/18';
        $CIDRs[18] = $NAddr[0] . ':' . dechex(floor($NAddr[1] / 8192) * 8192) . '::/19';
        $CIDRs[19] = $NAddr[0] . ':' . dechex(floor($NAddr[1] / 4096) * 4096) . '::/20';
        $CIDRs[20] = $NAddr[0] . ':' . dechex(floor($NAddr[1] / 2048) * 2048) . '::/21';
        $CIDRs[21] = $NAddr[0] . ':' . dechex(floor($NAddr[1] / 1024) * 1024) . '::/22';
        $CIDRs[22] = $NAddr[0] . ':' . dechex(floor($NAddr[1] / 512) * 512) . '::/23';
        $CIDRs[23] = $NAddr[0] . ':' . dechex(floor($NAddr[1] / 256) * 256) . '::/24';
        $CIDRs[24] = $NAddr[0] . ':' . dechex(floor($NAddr[1] / 128) * 128) . '::/25';
        $CIDRs[25] = $NAddr[0] . ':' . dechex(floor($NAddr[1] / 64) * 64) . '::/26';
        $CIDRs[26] = $NAddr[0] . ':' . dechex(floor($NAddr[1] / 32) * 32) . '::/27';
        $CIDRs[27] = $NAddr[0] . ':' . dechex(floor($NAddr[1] / 16) * 16) . '::/28';
        $CIDRs[28] = $NAddr[0] . ':' . dechex(floor($NAddr[1] / 8) * 8) . '::/29';
        $CIDRs[29] = $NAddr[0] . ':' . dechex(floor($NAddr[1] / 4) * 4) . '::/30';
        $CIDRs[30] = $NAddr[0] . ':' . dechex(floor($NAddr[1] / 2) * 2) . '::/31';
        $NAddr[1] = dechex($NAddr[1]);
        $CIDRs[31] = $NAddr[0] . ':' . $NAddr[1] . '::/32';
        $CIDRs[32] = ($NAddr[2] < 32768) ?
            $NAddr[0] . ':' . $NAddr[1] . '::/33' :
            $NAddr[0] . ':' . $NAddr[1] . ':8000::/33';
        $CIDRs[33] = $NAddr[0] . ':' . $NAddr[1] . ':' . dechex(floor($NAddr[2] / 16384) * 16384) . '::/34';
        $CIDRs[34] = $NAddr[0] . ':' . $NAddr[1] . ':' . dechex(floor($NAddr[2] / 8192) * 8192) . '::/35';
        $CIDRs[35] = $NAddr[0] . ':' . $NAddr[1] . ':' . dechex(floor($NAddr[2] / 4096) * 4096) . '::/36';
        $CIDRs[36] = $NAddr[0] . ':' . $NAddr[1] . ':' . dechex(floor($NAddr[2] / 2048) * 2048) . '::/37';
        $CIDRs[37] = $NAddr[0] . ':' . $NAddr[1] . ':' . dechex(floor($NAddr[2] / 1024) * 1024) . '::/38';
        $CIDRs[38] = $NAddr[0] . ':' . $NAddr[1] . ':' . dechex(floor($NAddr[2] / 512) * 512) . '::/39';
        $CIDRs[39] = $NAddr[0] . ':' . $NAddr[1] . ':' . dechex(floor($NAddr[2] / 256) * 256) . '::/40';
        $CIDRs[40] = $NAddr[0] . ':' . $NAddr[1] . ':' . dechex(floor($NAddr[2] / 128) * 128) . '::/41';
        $CIDRs[41] = $NAddr[0] . ':' . $NAddr[1] . ':' . dechex(floor($NAddr[2] / 64) * 64) . '::/42';
        $CIDRs[42] = $NAddr[0] . ':' . $NAddr[1] . ':' . dechex(floor($NAddr[2] / 32) * 32) . '::/43';
        $CIDRs[43] = $NAddr[0] . ':' . $NAddr[1] . ':' . dechex(floor($NAddr[2] / 16) * 16) . '::/44';
        $CIDRs[44] = $NAddr[0] . ':' . $NAddr[1] . ':' . dechex(floor($NAddr[2] / 8) * 8) . '::/45';
        $CIDRs[45] = $NAddr[0] . ':' . $NAddr[1] . ':' . dechex(floor($NAddr[2] / 4) * 4) . '::/46';
        $CIDRs[46] = $NAddr[0] . ':' . $NAddr[1] . ':' . dechex(floor($NAddr[2] / 2) * 2) . '::/47';
        $NAddr[2] = dechex($NAddr[2]);
        $CIDRs[47] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . '::/48';
        $CIDRs[48] = ($NAddr[3] < 32768) ?
            $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . '::/49' :
            $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':8000::/49';
        $CIDRs[49] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . dechex(floor($NAddr[3] / 16384) * 16384) . '::/50';
        $CIDRs[50] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . dechex(floor($NAddr[3] / 8192) * 8192) . '::/51';
        $CIDRs[51] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . dechex(floor($NAddr[3] / 4096) * 4096) . '::/52';
        $CIDRs[52] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . dechex(floor($NAddr[3] / 2048) * 2048) . '::/53';
        $CIDRs[53] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . dechex(floor($NAddr[3] / 1024) * 1024) . '::/54';
        $CIDRs[54] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . dechex(floor($NAddr[3] / 512) * 512) . '::/55';
        $CIDRs[55] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . dechex(floor($NAddr[3] / 256) * 256) . '::/56';
        $CIDRs[56] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . dechex(floor($NAddr[3] / 128) * 128) . '::/57';
        $CIDRs[57] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . dechex(floor($NAddr[3] / 64) * 64) . '::/58';
        $CIDRs[58] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . dechex(floor($NAddr[3] / 32) * 32) . '::/59';
        $CIDRs[59] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . dechex(floor($NAddr[3] / 16) * 16) . '::/60';
        $CIDRs[60] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . dechex(floor($NAddr[3] / 8) * 8) . '::/61';
        $CIDRs[61] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . dechex(floor($NAddr[3] / 4) * 4) . '::/62';
        $CIDRs[62] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . dechex(floor($NAddr[3] / 2) * 2) . '::/63';
        $NAddr[3] = dechex($NAddr[3]);
        $CIDRs[63] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . '::/64';
        $CIDRs[64] = ($NAddr[4] < 32768) ?
            $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . '::/65' :
            $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':8000::/65';
        $CIDRs[65] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . dechex(floor($NAddr[4] / 16384) * 16384) . '::/66';
        $CIDRs[66] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . dechex(floor($NAddr[4] / 8192) * 8192) . '::/67';
        $CIDRs[67] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . dechex(floor($NAddr[4] / 4096) * 4096) . '::/68';
        $CIDRs[68] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . dechex(floor($NAddr[4] / 2048) * 2048) . '::/69';
        $CIDRs[69] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . dechex(floor($NAddr[4] / 1024) * 1024) . '::/70';
        $CIDRs[70] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . dechex(floor($NAddr[4] / 512) * 512) . '::/71';
        $CIDRs[71] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . dechex(floor($NAddr[4] / 256) * 256) . '::/72';
        $CIDRs[72] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . dechex(floor($NAddr[4] / 128) * 128) . '::/73';
        $CIDRs[73] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . dechex(floor($NAddr[4] / 64) * 64) . '::/74';
        $CIDRs[74] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . dechex(floor($NAddr[4] / 32) * 32) . '::/75';
        $CIDRs[75] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . dechex(floor($NAddr[4] / 16) * 16) . '::/76';
        $CIDRs[76] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . dechex(floor($NAddr[4] / 8) * 8) . '::/77';
        $CIDRs[77] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . dechex(floor($NAddr[4] / 4) * 4) . '::/78';
        $CIDRs[78] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . dechex(floor($NAddr[4] / 2) * 2) . '::/79';
        $NAddr[4] = dechex($NAddr[4]);
        $CIDRs[79] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . '::/80';
        $CIDRs[80] = ($NAddr[5] < 32768) ?
            $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . '::/81' :
            $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':8000::/81';
        $CIDRs[81] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . dechex(floor($NAddr[5] / 16384) * 16384) . '::/82';
        $CIDRs[82] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . dechex(floor($NAddr[5] / 8192) * 8192) . '::/83';
        $CIDRs[83] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . dechex(floor($NAddr[5] / 4096) * 4096) . '::/84';
        $CIDRs[84] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . dechex(floor($NAddr[5] / 2048) * 2048) . '::/85';
        $CIDRs[85] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . dechex(floor($NAddr[5] / 1024) * 1024) . '::/86';
        $CIDRs[86] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . dechex(floor($NAddr[5] / 512) * 512) . '::/87';
        $CIDRs[87] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . dechex(floor($NAddr[5] / 256) * 256) . '::/88';
        $CIDRs[88] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . dechex(floor($NAddr[5] / 128) * 128) . '::/89';
        $CIDRs[89] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . dechex(floor($NAddr[5] / 64) * 64) . '::/90';
        $CIDRs[90] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . dechex(floor($NAddr[5] / 32) * 32) . '::/91';
        $CIDRs[91] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . dechex(floor($NAddr[5] / 16) * 16) . '::/92';
        $CIDRs[92] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . dechex(floor($NAddr[5] / 8) * 8) . '::/93';
        $CIDRs[93] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . dechex(floor($NAddr[5] / 4) * 4) . '::/94';
        $CIDRs[94] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . dechex(floor($NAddr[5] / 2) * 2) . '::/95';
        $NAddr[5] = dechex($NAddr[5]);
        $CIDRs[95] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . '::/96';
        $CIDRs[96] = ($NAddr[6] < 32768) ?
            $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . '::/97' :
            $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':8000:0/97';
        $CIDRs[97] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . dechex(floor($NAddr[6] / 16384) * 16384) . ':0/98';
        $CIDRs[98] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . dechex(floor($NAddr[6] / 8192) * 8192) . ':0/99';
        $CIDRs[99] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . dechex(floor($NAddr[6] / 4096) * 4096) . ':0/100';
        $CIDRs[100] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . dechex(floor($NAddr[6] / 2048) * 2048) . ':0/101';
        $CIDRs[101] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . dechex(floor($NAddr[6] / 1024) * 1024) . ':0/102';
        $CIDRs[102] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . dechex(floor($NAddr[6] / 512) * 512) . ':0/103';
        $CIDRs[103] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . dechex(floor($NAddr[6] / 256) * 256) . ':0/104';
        $CIDRs[104] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . dechex(floor($NAddr[6] / 128) * 128) . ':0/105';
        $CIDRs[105] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . dechex(floor($NAddr[6] / 64) * 64) . ':0/106';
        $CIDRs[106] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . dechex(floor($NAddr[6] / 32) * 32) . ':0/107';
        $CIDRs[107] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . dechex(floor($NAddr[6] / 16) * 16) . ':0/108';
        $CIDRs[108] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . dechex(floor($NAddr[6] / 8) * 8) . ':0/109';
        $CIDRs[109] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . dechex(floor($NAddr[6] / 4) * 4) . ':0/110';
        $CIDRs[110] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . dechex(floor($NAddr[6] / 2) * 2) . ':0/111';
        $NAddr[6] = dechex($NAddr[6]);
        $CIDRs[111] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . $NAddr[6] . ':0/112';
        $CIDRs[112] = ($NAddr[7] < 32768) ?
            $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . $NAddr[6] . ':0/113' :
            $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . $NAddr[6] . ':8000/113';
        $CIDRs[113] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . $NAddr[6] . ':' . dechex(floor($NAddr[7] / 16384) * 16384) . '/114';
        $CIDRs[114] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . $NAddr[6] . ':' . dechex(floor($NAddr[7] / 8192) * 8192) . '/115';
        $CIDRs[115] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . $NAddr[6] . ':' . dechex(floor($NAddr[7] / 4096) * 4096) . '/116';
        $CIDRs[116] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . $NAddr[6] . ':' . dechex(floor($NAddr[7] / 2048) * 2048) . '/117';
        $CIDRs[117] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . $NAddr[6] . ':' . dechex(floor($NAddr[7] / 1024) * 1024) . '/118';
        $CIDRs[118] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . $NAddr[6] . ':' . dechex(floor($NAddr[7] / 512) * 512) . '/119';
        $CIDRs[119] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . $NAddr[6] . ':' . dechex(floor($NAddr[7] / 256) * 256) . '/120';
        $CIDRs[120] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . $NAddr[6] . ':' . dechex(floor($NAddr[7] / 128) * 128) . '/121';
        $CIDRs[121] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . $NAddr[6] . ':' . dechex(floor($NAddr[7] / 64) * 64) . '/122';
        $CIDRs[122] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . $NAddr[6] . ':' . dechex(floor($NAddr[7] / 32) * 32) . '/123';
        $CIDRs[123] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . $NAddr[6] . ':' . dechex(floor($NAddr[7] / 16) * 16) . '/124';
        $CIDRs[124] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . $NAddr[6] . ':' . dechex(floor($NAddr[7] / 8) * 8) . '/125';
        $CIDRs[125] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . $NAddr[6] . ':' . dechex(floor($NAddr[7] / 4) * 4) . '/126';
        $CIDRs[126] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . $NAddr[6] . ':' . dechex(floor($NAddr[7] / 2) * 2) . '/127';
        $NAddr[7] = dechex($NAddr[7]);
        $CIDRs[127] = $NAddr[0] . ':' . $NAddr[1] . ':' . $NAddr[2] . ':' . $NAddr[3] . ':' . $NAddr[4] . ':' . $NAddr[5] . ':' . $NAddr[6] . ':' . $NAddr[7] . '/128';
        for ($i = 0; $i < 128; $i++) {
            if (strpos($CIDRs[$i], '::') !== false) {
                $CIDRs[$i] = preg_replace('/(\:0)*\:\:(0\:)*/i', '::', $CIDRs[$i], 1);
                $CIDRs[$i] = str_replace('::0/', '::/', $CIDRs[$i]);
                continue;
            }
            if (strpos($CIDRs[$i], ':0:0/') !== false) {
                $CIDRs[$i] = preg_replace('/(\:0){2,}\//i', '::/', $CIDRs[$i], 1);
                continue;
            }
            if (strpos($CIDRs[$i], ':0:0:') !== false) {
                $CIDRs[$i] = preg_replace('/(\:0)+\:(0\:)+/i', '::', $CIDRs[$i], 1);
                $CIDRs[$i] = str_replace('::0/', '::/', $CIDRs[$i]);
                continue;
            }
        }
        return $CIDRs;
    }

    /** Aggregate it! */
    public function aggregate($In)
    {
        $Begin = microtime(true);
        $this->Input = $In;
        $this->Output = $In;
        $this->stripInvalidCharactersAndSort($this->Output);
        $this->stripInvalidRangesAndSubs($this->Output);
        $this->mergeRanges($this->Output);
        $this->ProcessingTime = microtime(true) - $Begin;
        return $this->Output;
    }

    /** Strips invalid characters from lines and sorts entries. */
    private function stripInvalidCharactersAndSort(&$In)
    {
        $In = explode("\n", strtolower(trim(str_replace("\r", '', $In))));
        if (!empty($this->Results)) {
            $this->NumberEntered = count($In);
        }
        $In = array_filter(array_unique(array_map(function ($Line) {
            $Line = preg_replace(array('~^[^0-9a-f:./]*~i', '~[ \t].*$~', '~[^0-9a-f:./]*$~i'), '', $Line);
            return (!$Line || !preg_match('~[0-9a-f:./]+~i', $Line) || preg_match('~[^0-9a-f:./]+~i', $Line)) ? '' : $Line;
        }, $In)));
        usort($In, function ($A, $B) {
            if (($Pos = strpos($A, '/')) !== false) {
                $ASize = (int)substr($A, $Pos + 1);
                $A = substr($A, 0, $Pos);
            } else {
                $ASize = 0;
            }
            $A = empty($A) || (
                !$this->ExpandIPv4($A, true) && !$this->ExpandIPv6($A, true)
            ) ? '' : inet_pton($A);
            if (($Pos = strpos($B, '/')) !== false) {
                $BSize = (int)substr($B, $Pos + 1);
                $B = substr($B, 0, $Pos);
            } else {
                $BSize = 0;
            }
            $B = empty($B) || (
                !$this->ExpandIPv4($B, true) && !$this->ExpandIPv6($B, true)
            ) ? '' : inet_pton($B);
            if ($A === false) {
                if ($B === false) {
                    return 0;
                }
                return 1;
            }
            if ($B === false) {
                return -1;
            }
            $Compare = strcmp($A, $B);
            if ($Compare === 0) {
                if ($ASize === $BSize) {
                    return 0;
                }
                return ($ASize > $BSize) ? 1 : -1;
            }
            return ($Compare < 0) ? -1 : 1;
        });
        $In = implode("\n", $In);
    }

    /** Strips invalid ranges and subordinates. */
    private function stripInvalidRangesAndSubs(&$In)
    {
        $In = $Out = "\n" . $In . "\n";
        $Offset = 0;
        while (($NewLine = strpos($In, "\n", $Offset)) !== false) {
            $Line = substr($In, $Offset, $NewLine - $Offset);
            $Offset = $NewLine + 1;
            if (!$Line) {
                continue;
            }
            if (($RangeSep = strpos($Line, '/')) !== false) {
                $Size = (int)substr($Line, $RangeSep + 1);
                $CIDR = substr($Line, 0, $RangeSep);
            } else {
                $Size = false;
                $CIDR = $Line;
            }
            if (!$CIDRs = $this->ExpandIPv4($CIDR)) {
                if (!$CIDRs = $this->ExpandIPv6($CIDR)) {
                    $Out = str_replace("\n" . $Line . "\n", "\n", $Out);
                    continue;
                }
            }
            $Ranges = count($CIDRs);
            if ($Size === false) {
                $Size = $Ranges;
                $Out = str_replace("\n" . $CIDR . "\n", "\n" . $CIDRs[$Size - 1] . "\n", $Out);
            } elseif (!isset($CIDRs[$Size - 1]) || $Line !== $CIDRs[$Size - 1]) {
                $Out = str_replace("\n" . $Line . "\n", "\n", $Out);
                continue;
            }
            for ($Range = $Size - 2; $Range >= 0; $Range--) {
                if (isset($CIDRs[$Range]) && strpos($Out, "\n" . $CIDRs[$Range] . "\n") !== false) {
                    $Out = str_replace("\n" . $Line . "\n", "\n", $Out);
                    if (!empty($this->Results)) {
                        $this->NumberMerged++;
                    }
                    break;
                }
            }
        }
        $In = trim($Out);
        if (!empty($this->Results)) {
            $this->NumberReturned = empty($In) ? 0 : substr_count($In, "\n") + 1;
            $this->NumberRejected = $this->NumberEntered - $this->NumberReturned - $this->NumberMerged;
            $this->NumberAccepted = $this->NumberEntered - $this->NumberRejected;
        }
    }

    /** Merges ranges. */
    private function mergeRanges(&$In)
    {
        while (true) {
            $Step = $In;
            $In = $Out = "\n" . $In . "\n";
            $Size = $Offset = 0;
            $CIDR = $Line = '';
            $CIDRs = false;
            while (($NewLine = strpos($In, "\n", $Offset)) !== false) {
                $PrevLine = $Line;
                $PrevSize = $Size;
                $PrevCIDRs = $CIDRs;
                $Line = substr($In, $Offset, $NewLine - $Offset);
                $Offset = $NewLine + 1;
                $RangeSep = strpos($Line, '/');
                $Size = (int)substr($Line, $RangeSep + 1);
                $CIDR = substr($Line, 0, $RangeSep);
                if (!$CIDRs = $this->ExpandIPv4($CIDR)) {
                    $CIDRs = $this->ExpandIPv6($CIDR);
                }
                if (
                    !empty($CIDRs[$Size - 1]) &&
                    !empty($PrevCIDRs[$PrevSize - 1]) &&
                    !empty($CIDRs[$Size - 2]) &&
                    !empty($PrevCIDRs[$PrevSize - 2]) &&
                    $CIDRs[$Size - 2] === $PrevCIDRs[$PrevSize - 2]
                ) {
                    $Out = str_replace("\n" . $PrevLine . "\n" . $Line . "\n", "\n" . $CIDRs[$Size - 2] . "\n", $Out);
                    $Line = $CIDRs[$Size - 2];
                    $Size--;
                    if (!empty($this->Results)) {
                        $this->NumberMerged++;
                        $this->NumberReturned--;
                    }
                }
            }
            $In = trim($Out);
            if ($Step === $In) {
                break;
            }
        }
    }

    /** Resets numbers. */
    public function resetNumbers()
    {
        $this->NumberEntered = 0;
        $this->NumberRejected = 0;
        $this->NumberAccepted = 0;
        $this->NumberMerged = 0;
        $this->NumberReturned = 0;
        $this->ProcessingTime = 0;
    }

}
