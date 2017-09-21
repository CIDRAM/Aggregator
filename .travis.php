<?php
require __DIR__ . '/aggregator.php';

$ClassNames = array('\PHPUnit\Framework\TestCase', '\PHPUnit_Framework_TestCase');
if (class_exists($ClassNames[0]) && !class_exists($ClassNames[1])) {
    class_alias($ClassNames[0], $ClassNames[1]);
}
unset($ClassNames);

class Experimental extends \PHPUnit_Framework_TestCase
{

    private $NumberEntered = 0;
    private $NumberRejected = 0;
    private $NumberAccepted = 0;
    private $NumberMerged = 0;
    private $NumberReturned = 0;

    public function testInOut() {
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
Foobar Some garbage data from here
ASDFQWER!@#$
>>HelloWorld<<
SDFSDFSDF
QWEQWEQWE';
        $ExpectedOutput = '1::/127
1:2:3:4::/126
10.0.0.0/8
11.128.0.0/9
12.0.0.0/7
2002::1/128
127.0.0.1/32
127.0.0.2/31
127.0.0.4/31';
        $Aggregator = new Aggregator();
        $Aggregator->Results = true;
        $Aggregated = $Aggregator->aggregate($TestInput);
        $this->NumberEntered = $Aggregator->NumberEntered;
        $this->NumberRejected = $Aggregator->NumberRejected;
        $this->NumberAccepted = $Aggregator->NumberAccepted;
        $this->NumberMerged = $Aggregator->NumberMerged;
        $this->NumberReturned = $Aggregator->NumberReturned;
        $this->assertEquals($ExpectedOutput, $Aggregated, 'Actual aggregated output does not match expected aggregated output!');
    }

    public function testNumberEntered() {
        $Expected = 35;
        $Actual = $this->NumberEntered;
        $this->assertEquals($Expected, $Actual, 'NumberEntered value does not match expected value!');
    }

    public function testNumberRejected() {
        $Expected = 14;
        $Actual = $this->NumberRejected;
        $this->assertEquals($Expected, $Actual, 'NumberRejected value does not match expected value!');
    }

    public function testNumberAccepted() {
        $Expected = 21;
        $Actual = $this->NumberAccepted;
        $this->assertEquals($Expected, $Actual, 'NumberAccepted value does not match expected value!');
    }

    public function testNumberMerged() {
        $Expected = 12;
        $Actual = $this->NumberMerged;
        $this->assertEquals($Expected, $Actual, 'NumberMerged value does not match expected value!');
    }

    public function testNumberReturned() {
        $Expected = 9;
        $Actual = $this->NumberReturned;
        $this->assertEquals($Expected, $Actual, 'NumberReturned value does not match expected value!');
    }

    public function testExpandIPv4() {
        $Aggregator = new Aggregator();
        $Out = $Aggregator->ExpandIPv4('127.0.0.1');
        $Checksum = md5(serialize($Out));
        $this->assertEquals('cd37d1d14133dfd75f9dd13414cdcd76', $Checksum, 'ExpandIPv4 output does not match expected output!');
    }

    public function testExpandIPv6() {
        $Aggregator = new Aggregator();
        $Out = $Aggregator->ExpandIPv6('2002::1');
        $Checksum = md5(serialize($Out));
        $this->assertEquals('149e73862203bf6ae504a2474f7c12a8', $Checksum, 'ExpandIPv6 output does not match expected output!');
    }
}
