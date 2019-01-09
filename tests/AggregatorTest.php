<?php
namespace CIDRAM\Aggregator;

use PHPUnit\Framework\TestCase;

class AggregatorTest extends TestCase
{
    public function testInOut()
    {
        $TestInput = '127.0.0.1 Some arbitrary single IPs from here
127.0.0.2
127.0.0.3
1::
1::1
1:2:3:4::0
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
99.10.10.0/23
99.99.99.99/32
127.0.0.1/32
127.0.0.2/31
127.0.0.4/31
1::/127
1:2:3:4::1/128
1:2:3:4::2/127
2002::1/128';
        $Aggregator = new Aggregator();
        $Aggregator->Results = true;
        $Aggregated = $Aggregator->aggregate($TestInput);
        $ExpectedOutput = str_replace(PHP_EOL, "\n", $ExpectedOutput);

        $this->NumberEntered = $Aggregator->NumberEntered;
        $this->NumberRejected = $Aggregator->NumberRejected;
        $this->NumberAccepted = $Aggregator->NumberAccepted;
        $this->NumberMerged = $Aggregator->NumberMerged;
        $this->NumberReturned = $Aggregator->NumberReturned;
        $this->assertEquals($ExpectedOutput, $Aggregated, 'Actual aggregated output does not match expected aggregated output!');
    }

    public function testExpandIPv4()
    {
        $Aggregator = new Aggregator();
        $Out = $Aggregator->ExpandIPv4('127.0.0.1');
        $Checksum = md5(serialize($Out));
        $this->assertEquals('cd37d1d14133dfd75f9dd13414cdcd76', $Checksum, 'ExpandIPv4 output does not match expected output!');
    }

    public function testExpandIPv6()
    {
        $Aggregator = new Aggregator();
        $Out = $Aggregator->ExpandIPv6('2002::1');
        $Checksum = md5(serialize($Out));
        $this->assertEquals('149e73862203bf6ae504a2474f7c12a8', $Checksum, 'ExpandIPv6 output does not match expected output!');
    }
}
