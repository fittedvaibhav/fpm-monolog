<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fitted\ProductManager\Monolog\Handler;

use Fitted\ProductManager\Monolog\Test\TestCase;

/**
 * @requires extension sockets
 */
class SyslogUdpHandlerTest extends TestCase
{
    public function testWeValidateFacilities()
    {
        $this->expectException(\UnexpectedValueException::class);

        $handler = new SyslogUdpHandler("ip", 514, "invalidFacility");
    }

    public function testWeSplitIntoLines()
    {
        $pid = getmypid();
        $host = gethostname();

        $handler = new \Fitted\ProductManager\Monolog\Handler\SyslogUdpHandler("127.0.0.1", 514, "authpriv");
        $handler->setFormatter(new \Fitted\ProductManager\Monolog\Formatter\ChromePHPFormatter());

        $time = '2014-01-07T12:34:56+00:00';
        $socket = $this->getMockBuilder('Fitted\ProductManager\Monolog\Handler\SyslogUdp\UdpSocket')
            ->onlyMethods(['write'])
            ->setConstructorArgs(['lol'])
            ->getMock();
        $socket->expects($this->at(0))
            ->method('write')
            ->with("lol", "<".(LOG_AUTHPRIV + LOG_WARNING).">1 $time $host php $pid - - ");
        $socket->expects($this->at(1))
            ->method('write')
            ->with("hej", "<".(LOG_AUTHPRIV + LOG_WARNING).">1 $time $host php $pid - - ");

        $handler->setSocket($socket);

        $handler->handle($this->getRecordWithMessage("hej\nlol"));
    }

    public function testSplitWorksOnEmptyMsg()
    {
        $handler = new SyslogUdpHandler("127.0.0.1", 514, "authpriv");
        $handler->setFormatter($this->getIdentityFormatter());

        $socket = $this->getMockBuilder('Fitted\ProductManager\Monolog\Handler\SyslogUdp\UdpSocket')
            ->onlyMethods(['write'])
            ->setConstructorArgs(['lol'])
            ->getMock();
        $socket->expects($this->never())
            ->method('write');

        $handler->setSocket($socket);

        $handler->handle($this->getRecordWithMessage(null));
    }

    public function testRfc()
    {
        $time = 'Jan 07 12:34:56';
        $pid = getmypid();
        $host = gethostname();

        $handler = $this->getMockBuilder('\Fitted\ProductManager\Monolog\Handler\SyslogUdpHandler')
            ->setConstructorArgs(array("127.0.0.1", 514, "authpriv", 'debug', true, "php", \Fitted\ProductManager\Monolog\Handler\SyslogUdpHandler::RFC3164))
            ->onlyMethods([])
            ->getMock();

        $handler->setFormatter(new \Fitted\ProductManager\Monolog\Formatter\ChromePHPFormatter());

        $socket = $this->getMockBuilder('\Fitted\ProductManager\Monolog\Handler\SyslogUdp\UdpSocket')
            ->setConstructorArgs(array('lol', 999))
            ->onlyMethods(array('write'))
            ->getMock();
        $socket->expects($this->at(0))
            ->method('write')
            ->with("lol", "<".(LOG_AUTHPRIV + LOG_WARNING).">$time $host php[$pid]: ");
        $socket->expects($this->at(1))
            ->method('write')
            ->with("hej", "<".(LOG_AUTHPRIV + LOG_WARNING).">$time $host php[$pid]: ");

        $handler->setSocket($socket);

        $handler->handle($this->getRecordWithMessage("hej\nlol"));
    }

    protected function getRecordWithMessage($msg)
    {
        return ['message' => $msg, 'level' => \Fitted\ProductManager\Monolog\Logger::WARNING, 'context' => null, 'extra' => [], 'channel' => 'lol', 'datetime' => new \DateTimeImmutable('2014-01-07 12:34:56')];
    }
}
