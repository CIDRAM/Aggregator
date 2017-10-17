<?php
namespace CIDRAM\Aggregator;

/**
 * Aggregator v1.1.0-DEV (last modified: 2017.10.17).
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
 *
 * @see LICENSE.txt
 */
class Aggregator
{

    /** Input. */
    public $Input = '';

    /** Output. */
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
     * @param string $Addr         Refer to the description above.
     * @param bool   $ValidateOnly If true, just checks if the IP is valid only.
     * @param int    $FactorLimit  Maximum number of CIDRs to return (default: 32).
     * @return bool|array Refer to the description above.
     */
    public function ExpandIPv4($Addr, $ValidateOnly = false, $FactorLimit = 32)
    {
        if (!preg_match(
            '/^([01]?[0-9]{1,2}|2[0-4][0-9]|25[0-5])\.([01]?[0-9]{1,2}|2[0-4][0-' .
            '9]|25[0-5])\.([01]?[0-9]{1,2}|2[0-4][0-9]|25[0-5])\.([01]?[0-9]{1,2' .
            '}|2[0-4][0-9]|25[0-5])$/i',
            $Addr, $Octets)
        ) {
            return false;
        }
        if ($ValidateOnly) {
            return true;
        }
        $CIDRs = [];
        $Base = [0, 0, 0, 0];
        for ($Cycle = 0; $Cycle < 4; $Cycle++) {
            for ($Size = 128, $Step = 0; $Step < 8; $Step++, $Size /= 2) {
                $CIDR = $Step + ($Cycle * 8);
                $Base[$Cycle] = floor($Octets[$Cycle + 1] / $Size) * $Size;
                $CIDRs[$CIDR] = $Base[0] . '.' . $Base[1] . '.' . $Base[2] . '.' . $Base[3] . '/' . ($CIDR + 1);
                if ($CIDR >= $FactorLimit) {
                    break 2;
                }
            }
        }
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
     * @param string $Addr         Refer to the description above.
     * @param bool   $ValidateOnly If true, just checks if the IP is valid only.
     * @param int    $FactorLimit  Maximum number of CIDRs to return (default: 128).
     * @return bool|array Refer to the description above.
     */
    public function ExpandIPv6($Addr, $ValidateOnly = false, $FactorLimit = 128)
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
            $Addr)
        ) {
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
            $Arr = [':0:', ':0:0:', ':0:0:0:', ':0:0:0:0:', ':0:0:0:0:0:', ':0:0:0:0:0:0:'];
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
        for ($i = 0; $i < 8; $i++) {
            $NAddr[$i] = hexdec($NAddr[$i]);
        }
        $CIDRs = [];
        $Base = [0, 0, 0, 0, 0, 0, 0, 0];
        for ($Cycle = 0; $Cycle < 8; $Cycle++) {
            for ($Size = 32768, $Step = 0; $Step < 16; $Step++, $Size /= 2) {
                $CIDR = $Step + ($Cycle * 16);
                $Base[$Cycle] = dechex(floor($NAddr[$Cycle] / $Size) * $Size);
                $CIDRs[$CIDR] = $Base[0] . ':' . $Base[1] . ':' . $Base[2] . ':' . $Base[3] . ':' . $Base[4] . ':' . $Base[5] . ':' . $Base[6] . ':' . $Base[7] . '/' . ($CIDR + 1);
                if ($CIDR >= $FactorLimit) {
                    break 2;
                }
            }
        }
        if ($FactorLimit > 128) {
            $FactorLimit = 128;
        }
        for ($CIDR = 0; $CIDR < $FactorLimit; $CIDR++) {
            if (strpos($CIDRs[$CIDR], '::') !== false) {
                $CIDRs[$CIDR] = preg_replace('/(\:0)*\:\:(0\:)*/i', '::', $CIDRs[$CIDR], 1);
                $CIDRs[$CIDR] = str_replace('::0/', '::/', $CIDRs[$CIDR]);
                continue;
            }
            if (strpos($CIDRs[$CIDR], ':0:0/') !== false) {
                $CIDRs[$CIDR] = preg_replace('/(\:0){2,}\//i', '::/', $CIDRs[$CIDR], 1);
                continue;
            }
            if (strpos($CIDRs[$CIDR], ':0:0:') !== false) {
                $CIDRs[$CIDR] = preg_replace('/(\:0)+\:(0\:)+/i', '::', $CIDRs[$CIDR], 1);
                $CIDRs[$CIDR] = str_replace('::0/', '::/', $CIDRs[$CIDR]);
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
            $Line = preg_replace(['~^[^0-9a-f:./]*~i', '~[ \t].*$~', '~[^0-9a-f:./]*$~i'], '', $Line);
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
                return $B === false ? 0 : 1;
            }
            if ($B === false) {
                return -1;
            }
            $Compare = strcmp($A, $B);
            if ($Compare === 0) {
                if ($ASize === $BSize) {
                    return 0;
                }
                return $ASize > $BSize ? 1 : -1;
            }
            return $Compare < 0 ? -1 : 1;
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
                if (!$CIDRs = $this->ExpandIPv4($CIDR, false, $Size - 1)) {
                    $CIDRs = $this->ExpandIPv6($CIDR, false, $Size - 1);
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
