<?php
/**
 * Aggregator v1.3.4 (last modified: 2022.02.12).
 * @link https://github.com/CIDRAM/Aggregator
 *
 * Description: A stand-alone class implementation of the IPv4+IPv6 IP+CIDR
 * aggregator from CIDRAM.
 *
 * AGGREGATOR COPYRIGHT 2017 and beyond by Caleb Mazalevskis (Maikuolan).
 *
 * License: GNU/GPLv2
 * @see LICENSE.txt
 */

namespace CIDRAM\Aggregator;

class Aggregator
{
    /**
     * @var string|array Output.
     */
    public $Output = '';

    /**
     * @var bool Results switch.
     */
    public $Results = false;

    /**
     * @var int Number of lines for aggregation entered.
     */
    public $NumberEntered = 0;

    /**
     * @var int Number of lines for aggregation rejected.
     */
    public $NumberRejected = 0;

    /**
     * @var int Number of lines for aggregation accepted.
     */
    public $NumberAccepted = 0;

    /**
     * @var int Number of lines aggregated or merged.
     */
    public $NumberMerged = 0;

    /**
     * @var int Number of lines returned.
     */
    public $NumberReturned = 0;

    /**
     * @var int Time consumed while aggregating data.
     */
    public $ProcessingTime = 0;

    /**
     * @var array Optional callbacks.
     */
    public $callbacks = [];

    /**
     * @var array Conversion tables for netmasks to IPv4.
     */
    private $TableNetmaskIPv4 = [];

    /**
     * @var array Conversion tables for netmasks to IPv6.
     */
    private $TableNetmaskIPv6 = [];

    /**
     * @var array Conversion tables for IPv4 to netmasks.
     */
    private $TableIPv4Netmask = [];

    /**
     * @var array Conversion tables for IPv6 to netmasks.
     */
    private $TableIPv6Netmask = [];

    /**
     * @var int Specifies the format to use for Aggregator output.
     *      0 = CIDR notation [default].
     *      1 = Netmask notation.
     */
    private $Mode = 0;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct($Mode = 0)
    {
        $this->constructTables();
        $this->Mode = $Mode;
    }

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
            '/^([01]?\d{1,2}|2[0-4]\d|25[0-5])\.([01]?\d{1,2}|2[0-4]\d|25[0-5])\.([01]?\d{1,2}|2[0-4]\d|25[0-5])\.([01]?\d{1,2}|2[0-4]\d|25[0-5])$/',
            $Addr,
            $Octets
        )) {
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
         * The pattern used by this `preg_match` call was adapted from the IPv6
         * pattern that can be found at
         * @link https://sroze.io/regex-ip-v4-et-ipv6-6cc005cabe8c
         */
        if (!preg_match(
            '/^((([\da-f]{1,4}:){7}[\da-f]{1,4})|(([\da-f]{1,4}:){6}:[\da-f]{1,4})' .
            '|(([\da-f]{1,4}:){5}:([\da-f]{1,4}:)?[\da-f]{1,4})|(([\da-f]{1,4}:){4' .
            '}:([\da-f]{1,4}:){0,2}[\da-f]{1,4})|(([\da-f]{1,4}:){3}:([\da-f]{1,4}' .
            ':){0,3}[\da-f]{1,4})|(([\da-f]{1,4}:){2}:([\da-f]{1,4}:){0,4}[\da-f]{' .
            '1,4})|(([\da-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2})' .
            ')\b).){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([\da-f]{1' .
            ',4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b).){3}(\b((' .
            '25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([\da-f]{1,4}:){0,5}((' .
            '\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b).){3}(\b((25[0-5])|(1\d' .
            '{2})|(2[0-4]\d)|(\d{1,2}))\b))|([\da-f]{1,4}::([\da-f]{1,4}:){0,5}[\d' .
            'a-f]{1,4})|(::([\da-f]{1,4}:){0,6}[\da-f]{1,4})|(([\da-f]{1,4}:){1,7}' .
            ':))$/i',
            $Addr
        )) {
            return false;
        }
        if ($ValidateOnly) {
            return true;
        }
        $NAddr = $Addr;
        if (substr($NAddr, 0, 2) === '::') {
            $NAddr = '0' . $NAddr;
        }
        if (substr($NAddr, -2) === '::') {
            $NAddr .= '0';
        }
        if (strpos($NAddr, '::') !== false) {
            $Key = 7 - substr_count($Addr, ':');
            $Arr = [':0:', ':0:0:', ':0:0:0:', ':0:0:0:0:', ':0:0:0:0:0:', ':0:0:0:0:0:0:'];
            if (!isset($Arr[$Key])) {
                return false;
            }
            $NAddr = str_replace('::', $Arr[$Key], $Addr);
            unset($Arr, $Key);
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
        foreach ($CIDRs as &$CIDR) {
            if (strpos($CIDR, '::') !== false) {
                $CIDR = preg_replace('~(?::0)*::(?:0:)*~i', '::', $CIDR, 1);
                $CIDR = str_replace('::0/', '::/', $CIDR);
                continue;
            }
            if (strpos($CIDR, ':0:0/') !== false) {
                $CIDR = preg_replace('~(:0){2,}\/~i', '::/', $CIDR, 1);
                continue;
            }
            if (strpos($CIDR, ':0:0:') !== false) {
                $CIDR = preg_replace('~(:0)+:(0:)+~i', '::', $CIDR, 1);
                $CIDR = str_replace('::0/', '::/', $CIDR);
                continue;
            }
        }
        return $CIDRs;
    }

    /**
     * Aggregate it!
     *
     * @param string|array $In The IPs/CIDRs/netmasks to be aggregated. Should
     *          either be a string, with entries separated by lines, or an
     *          array with an entry to each element.
     * @return string The aggregated data.
     */
    public function aggregate($In)
    {
        $Begin = microtime(true);
        $this->Output = $In;
        $this->stripInvalidCharactersAndSort($this->Output);
        $this->stripInvalidRangesAndSubs($this->Output);
        $this->mergeRanges($this->Output);
        if ($this->Mode === 1) {
            $this->convertToNetmasks($this->Output);
        }
        $this->ProcessingTime = microtime(true) - $Begin;
        return $this->Output;
    }

    /**
     * Resets numbers.
     *
     * @return void
     */
    public function resetNumbers()
    {
        $this->NumberEntered = 0;
        $this->NumberRejected = 0;
        $this->NumberAccepted = 0;
        $this->NumberMerged = 0;
        $this->NumberReturned = 0;
        $this->ProcessingTime = 0;
    }

    /**
     * Construct netmask<->CIDR conversion tables.
     *
     * @return void
     */
    private function constructTables()
    {
        $CIDR = 32;
        for ($Octet = 4; $Octet > 0; $Octet--) {
            $Base = str_repeat('255.', $Octet - 1);
            $End = str_repeat('.0', 4 - $Octet);
            for ($Addresses = 1, $Iterate = 0; $Iterate < 8; $Iterate++, $Addresses *= 2, $CIDR--) {
                $Netmask = $Base . (256 - $Addresses) . $End;
                $this->TableNetmaskIPv4[$CIDR] = $Netmask;
                $this->TableIPv4Netmask[$Netmask] = $CIDR;
            }
        }
        $CIDR = 128;
        for ($Octet = 8; $Octet > 0; $Octet--) {
            $Base = str_repeat('ffff:', $Octet - 1);
            $End = ($Octet === 8) ? ':0' : '::';
            for ($Addresses = 1, $Iterate = 0; $Iterate < 16; $Iterate++, $Addresses *= 2, $CIDR--) {
                $Netmask = $Base . (dechex(65536 - $Addresses)) . $End;
                $this->TableNetmaskIPv6[$CIDR] = $Netmask;
                $this->TableIPv6Netmask[$Netmask] = $CIDR;
            }
        }
    }

    /**
     * Strips invalid characters from lines and sorts entries.
     *
     * @param string|array
     * @return void
     */
    private function stripInvalidCharactersAndSort(&$In)
    {
        if (!is_array($In)) {
            $In = explode("\n", strtolower(trim(str_replace("\r", '', $In))));
        }
        $InCount = count($In);
        if (isset($this->callbacks['newParse']) && is_callable($this->callbacks['newParse'])) {
            $this->callbacks['newParse']($InCount);
        }
        if (!empty($this->Results)) {
            $this->NumberEntered = $InCount;
        }
        unset($InCount);
        $In = array_filter(array_unique(array_map(function ($Line) {
            $Line = preg_replace(['~^(?:#| \*|/\*).*~', '~^[^\da-f:./]*~i', '~[ \t].*$~', '~[^\da-f:./]*$~i'], '', $Line);
            if (isset($this->callbacks['newTick']) && is_callable($this->callbacks['newTick'])) {
                $this->callbacks['newTick']();
            }
            return (!$Line || !preg_match('~[\da-f:./]+~i', $Line) || preg_match('~[^\da-f:./]+~i', $Line)) ? '' : $Line;
        }, $In)));
        usort($In, function ($A, $B) {
            if (($Pos = strpos($A, '/')) !== false) {
                $ASize = substr($A, $Pos + 1);
                $A = substr($A, 0, $Pos);
            } else {
                $ASize = 0;
            }
            $AType = 0;
            if ($this->ExpandIPv4($A, true)) {
                $AType = 4;
            } elseif ($this->ExpandIPv6($A, true)) {
                $AType = 6;
            }
            $A = $AType ? inet_pton($A) : false;
            if ($AType === 4 && isset($this->TableIPv4Netmask[$ASize])) {
                $ASize = $this->TableIPv4Netmask[$ASize];
            } elseif ($AType === 6 && isset($this->TableIPv6Netmask[$ASize])) {
                $ASize = $this->TableIPv6Netmask[$ASize];
            } else {
                $ASize = (int)$ASize;
            }
            if ($ASize === 0) {
                $ASize = ($AType === 4) ? 32 : 128;
            }
            if (($Pos = strpos($B, '/')) !== false) {
                $BSize = substr($B, $Pos + 1);
                $B = substr($B, 0, $Pos);
            } else {
                $BSize = 0;
            }
            $BType = 0;
            if ($this->ExpandIPv4($B, true)) {
                $BType = 4;
            } elseif ($this->ExpandIPv6($B, true)) {
                $BType = 6;
            }
            $B = $BType ? inet_pton($B) : false;
            if ($BType === 4 && isset($this->TableIPv4Netmask[$BSize])) {
                $BSize = $this->TableIPv4Netmask[$BSize];
            } elseif ($BType === 6 && isset($this->TableIPv6Netmask[$BSize])) {
                $BSize = $this->TableIPv6Netmask[$BSize];
            } else {
                $BSize = (int)$BSize;
            }
            if ($BSize === 0) {
                $BSize = ($BType === 4) ? 32 : 128;
            }
            if ($A === false) {
                return $B === false ? 0 : 1;
            }
            if ($B === false) {
                return -1;
            }
            if ($AType !== $BType) {
                return $AType < $BType ? -1 : 1;
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

    /**
     * Strips invalid ranges and subordinates.
     *
     * @param string
     * @return void
     */
    private function stripInvalidRangesAndSubs(&$In)
    {
        if (isset($this->callbacks['newParse']) && is_callable($this->callbacks['newParse'])) {
            $this->callbacks['newParse'](substr_count($In, "\n"));
        }
        $In = $Out = "\n" . $In . "\n";
        $Offset = 0;
        $Low = [4 => 1, 6 => 1];
        foreach ([
            [4, '(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])', 33],
            [6,
                '(?:(?:(?:[\da-f]{1,4}:){7}[\da-f]{1,4})|(?:(?:[\da-f]{1,4}:){6}:[\da-f]{1,4})|(?:(?:[\da-f]{1,4}:){5}:(?:[\da-f]{1,4}:)?[\da-f]{1,4}' .
                ')|(?:(?:[\da-f]{1,4}:){4}:(?:[\da-f]{1,4}:){0,2}[\da-f]{1,4})|(?:(?:[\da-f]{1,4}:){3}:(?:[\da-f]{1,4}:){0,3}[\da-f]{1,4})|(?:(?:[\da' .
                '-f]{1,4}:){2}:(?:[\da-f]{1,4}:){0,4}[\da-f]{1,4})|(?:(?:[\da-f]{1,4}:){6}(?:(?:\b(?:(?:25[0-5])|(?:1\d{2})|(?:2[0-4]\d)|(?:\d{1,2}))\b' .
                ').){3}(?:\b(?:(?:25[0-5])|(?:1\d{2})|(?:2[0-4]\d)|(?:\d{1,2}))\b))|(?:(?:[\da-f]{1,4}:){0,5}:(?:(?:\b(?:(?:25[0-5])|(?:1\d{2})|(?:2[0-4]' .
                '\d)|(?:\d{1,2}))\b).){3}(?:\b(?:(?:25[0-5])|(?:1\d{2})|(?:2[0-4]\d)|(?:\d{1,2}))\b))|(?:::(?:[\da-f]{1,4}:){0,5}(?:(?:\b(?:(?:25[0-5])|' .
                '(?:1\d{2})|(?:2[0-4]\d)|(?:\d{1,2}))\b).){3}(?:\b(?:(?:25[0-5])|(?:1\d{2})|(?:2[0-4]\d)|(?:\d{1,2}))\b))|(?:[\da-f]{1,4}::(?:[\da-f]{1,4' .
                '}:){0,5}[\da-f]{1,4})|(?:::(?:[\da-f]{1,4}:){0,6}[\da-f]{1,4})|(?:(?:[\da-f]{1,4}:){1,7}:))',
            129],
        ] as $Lows) {
            for ($Iterant = 1; $Iterant < $Lows[2]; $Iterant++) {
                $Low[$Lows[0]] = $Iterant;
                if (preg_match('~\n' . $Lows[1] . '/' . $Iterant . '(?:$|\D)~i', $In)) {
                    break;
                }
            }
        }
        unset($Lows);
        while (($NewLine = strpos($In, "\n", $Offset)) !== false) {
            if (isset($this->callbacks['newTick']) && is_callable($this->callbacks['newTick'])) {
                $this->callbacks['newTick']();
            }
            $Line = substr($In, $Offset, $NewLine - $Offset);
            $Offset = $NewLine + 1;
            if (!$Line) {
                continue;
            }
            if (($RangeSep = strpos($Line, '/')) !== false) {
                $Size = substr($Line, $RangeSep + 1);
                if (strpos($Size, '.') !== false) {
                    $Size = isset($this->TableIPv4Netmask[$Size]) ? $this->TableIPv4Netmask[$Size] : 0;
                } elseif (strpos($Size, ':') !== false) {
                    $Size = isset($this->TableIPv6Netmask[$Size]) ? $this->TableIPv6Netmask[$Size] : 0;
                } else {
                    $Size = (int)$Size;
                }
                $CIDR = substr($Line, 0, $RangeSep);
            } else {
                if (strpos($Line, '.') !== false) {
                    $Size = 32;
                } elseif (strpos($Line, ':') !== false) {
                    $Size = 128;
                } else {
                    $Size = 0;
                }
                $CIDR = $Line;
            }
            if (($Size > 0 && $Size <= 32) && ($CIDRs = $this->ExpandIPv4($CIDR, false, $Size - 1))) {
                $Type = 4;
            } elseif (($Size > 0 && $Size <= 128) && ($CIDRs = $this->ExpandIPv6($CIDR, false, $Size - 1))) {
                $Type = 6;
            } else {
                $Out = str_replace("\n" . $Line . "\n", "\n", $Out);
                continue;
            }
            if (!isset($CIDRs[$Size - 1]) || $CIDRs[$Size - 1] !== $CIDR . '/' . $Size) {
                $Out = str_replace("\n" . $Line . "\n", "\n", $Out);
                continue;
            }
            $Out = str_replace("\n" . $Line . "\n", "\n" . $CIDRs[$Size - 1] . "\n", $Out);
            $ThisLow = ($Type === 4 ? $Low[4] : $Low[6]) - 1;
            for ($Range = $Size - 2; $Range >= $ThisLow; $Range--) {
                if (isset($CIDRs[$Range]) && strpos($Out, "\n" . $CIDRs[$Range] . "\n") !== false) {
                    $Out = str_replace("\n" . $CIDRs[$Size - 1] . "\n", "\n", $Out);
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

    /**
     * Merges ranges.
     *
     * @param string
     * @return void
     */
    private function mergeRanges(&$In)
    {
        while (true) {
            $Step = $In;
            if (isset($this->callbacks['newParse']) && is_callable($this->callbacks['newParse'])) {
                $this->callbacks['newParse'](substr_count($Step, "\n"));
            }
            $In = $Out = "\n" . $In . "\n";
            $Size = $Offset = 0;
            $Line = '';
            $CIDRs = false;
            while (($NewLine = strpos($In, "\n", $Offset)) !== false) {
                if (isset($this->callbacks['newTick']) && is_callable($this->callbacks['newTick'])) {
                    $this->callbacks['newTick']();
                }
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
                if ($Line === $PrevLine) {
                    $Out = str_replace("\n" . $PrevLine . "\n" . $Line . "\n", "\n" . $Line . "\n", $Out);
                } elseif (
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

    /**
     * Optionally converts output to netmask notation.
     *
     * @param string
     * @return void
     */
    private function convertToNetmasks(&$In)
    {
        if (isset($this->callbacks['newParse']) && is_callable($this->callbacks['newParse'])) {
            $this->callbacks['newParse'](substr_count($In, "\n"));
        }
        $In = $Out = "\n" . $In . "\n";
        $Offset = 0;
        while (($NewLine = strpos($In, "\n", $Offset)) !== false) {
            if (isset($this->callbacks['newTick']) && is_callable($this->callbacks['newTick'])) {
                $this->callbacks['newTick']();
            }
            $Line = substr($In, $Offset, $NewLine - $Offset);
            $Offset = $NewLine + 1;
            if (!$Line || ($RangeSep = strpos($Line, '/')) === false) {
                continue;
            }
            $Size = (int)substr($Line, $RangeSep + 1);
            $CIDR = substr($Line, 0, $RangeSep);
            $Type = ($this->ExpandIPv4($CIDR, true)) ? 4 : 6;
            if ($Type === 4 && isset($this->TableNetmaskIPv4[$Size])) {
                $Size = $this->TableNetmaskIPv4[$Size];
            } elseif ($Type === 6 && isset($this->TableNetmaskIPv6[$Size])) {
                $Size = $this->TableNetmaskIPv6[$Size];
            }
            $Out = str_replace("\n" . $Line . "\n", "\n" . $CIDR . '/' . $Size . "\n", $Out);
        }
        $In = trim($Out);
    }
}
