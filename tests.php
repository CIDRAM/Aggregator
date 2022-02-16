<?php
/**
 * Aggregator tests (last modified: 2022.02.12).
 * @link https://github.com/CIDRAM/Aggregator
 *
 * AGGREGATOR COPYRIGHT 2017 and beyond by Caleb Mazalevskis (Maikuolan).
 *
 * License: GNU/GPLv2
 * @see LICENSE.txt
 */

/**
 * If this file remains intact after deploying the package to production,
 * preventing it from running outside of Composer may be useful as a means of
 * prevent potential attackers from hammering the file and needlessly wasting
 * cycles at the server.
 */
if (!isset($_SERVER['COMPOSER_BINARY'])) {
    die;
}

require __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Aggregator.php';

$TestInput = '127.0.0.1 Some arbitrary single IPs from here
127.0.0.2
127.0.0.3
1::
1::1
1:2:3:4::
1:2:3:4::1
1:2:3:4::2
1:2:3:4::3
2002::1
127.0.0.4
127.0.0.5
257.0.0.999 Some arbitrary INVALID single IPs from here
555.666.777.888
2002:abcd:efgh::1
10.0.0.0/9 Some arbitrary CIDRs from here
10.128.0.0/9
10.192.0.0/10
11.128.0.0/10
11.192.0.0/10
12.0.0.0/9
12.128.0.0/9
13.0.0.0/9
13.128.0.0/9
192.168.0.0/8 Some arbitrary INVALID CIDRs from here
192.168.0.0/9
192.168.0.0/10
192.168.192.0/10
192.169.0.0/10
192.169.64.0/10
1.2.3.4/255.255.255.254 Some arbitrary netmasks from here
2.3.4.5/255.255.255.255
99.99.99.99/255.255.255.255
99.10.10.0/255.255.255.0
99.10.11.0/255.255.255.0
99.8.0.0/255.252.0.0
11.11.11.11/11.11.11.11 Some arbitrary INVALID netmasks from here
255.255.255.254/1.2.3.4
6.7.8.9/255.255.255.254
88.88.88.88/255.255.254.255
Foobar Some garbage data from here
ASDFQWER!@#$
>>HelloWorld<<
SDFSDFSDF
QWEQWEQWE';

$ExpectedOutput = '1.2.3.4/31
2.3.4.5/32
10.0.0.0/8
11.128.0.0/9
12.0.0.0/7
99.8.0.0/14
99.99.99.99/32
127.0.0.1/32
127.0.0.2/31
127.0.0.4/31
1::/127
1:2:3:4::/126
2002::1/128';

$Aggregator = new \CIDRAM\Aggregator\Aggregator();
$Aggregator->Results = true;
$Aggregated = $Aggregator->aggregate($TestInput);
$ExpectedOutput = str_replace(PHP_EOL, "\n", $ExpectedOutput);

if ($ExpectedOutput !== $Aggregated) {
    echo 'Actual aggregated output does not match expected aggregated output!' . PHP_EOL;
    exit(1);
}

$ExpectedOutput = '1.2.3.4/255.255.255.254
2.3.4.5/255.255.255.255
10.0.0.0/255.0.0.0
11.128.0.0/255.128.0.0
12.0.0.0/254.0.0.0
99.8.0.0/255.252.0.0
99.99.99.99/255.255.255.255
127.0.0.1/255.255.255.255
127.0.0.2/255.255.255.254
127.0.0.4/255.255.255.254
1::/ffff:ffff:ffff:ffff:ffff:ffff:ffff:fffe:0
1:2:3:4::/ffff:ffff:ffff:ffff:ffff:ffff:ffff:fffc:0
2002::1/ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff:0';

$Aggregator = new \CIDRAM\Aggregator\Aggregator(1);
$Aggregator->Results = true;
$Aggregated = $Aggregator->aggregate($TestInput);
$ExpectedOutput = str_replace(PHP_EOL, "\n", $ExpectedOutput);

if ($ExpectedOutput !== $Aggregated) {
    echo 'Actual aggregated output does not match expected aggregated output!' . PHP_EOL;
    exit(2);
}

$Aggregator = new \CIDRAM\Aggregator\Aggregator();

foreach ([
    ['127.0.0.1', 'b86ad9164b62e5a0652a160c98a29d043f68310b'],
    ['192.168.0.1', '7687472bc75a1e51f9d4589d6a1c9499ce2b5f09'],
    ['0.0.0.0', 'c0c337c3cd4f1ae74f6b0c1533b8618b563e04e4'],
    ['255.255.255.255', '235ad902a411ef86475e13d2cfb575385794a627']
] as $Try) {
    $Out = $Aggregator->ExpandIPv4($Try[0]);
    if ($Try[1] !== hash('sha1', serialize($Out))) {
        echo 'ExpandIPv4 output does not match expected output!' . PHP_EOL;
        exit(3);
    }
}

foreach ([
    ['2002::1', 'f15b0da4ec536dd06e1a41f09abe4ee141663bdf'],
    ['1234:5678::89ab:cdef', 'fefa598e549cfe16dee38ca0470d47b8ab0a0104'],
    ['::1', '703648258c947581c381509d717bd4bdaaa87284'],
    ['1::', '59f3adbb5f677d4596b88e766425ef5f6252f1e0']
] as $Try) {
    $Out = $Aggregator->ExpandIPv6($Try[0]);
    if ($Try[1] !== hash('sha1', serialize($Out))) {
        echo 'ExpandIPv6 output does not match expected output!' . PHP_EOL;
        exit(4);
    }
}

$TestInput = '1.0.0.0/32
1.0.0.0';

$ExpectedOutput = '1.0.0.0/32';

$Aggregator = new \CIDRAM\Aggregator\Aggregator();
$Aggregator->Results = true;
$Aggregated = $Aggregator->aggregate($TestInput);

if ($ExpectedOutput !== $Aggregated) {
    echo 'Output is bugged.' . PHP_EOL;
    exit(5);
}

echo 'All tests passed.' . PHP_EOL;
exit(0);
