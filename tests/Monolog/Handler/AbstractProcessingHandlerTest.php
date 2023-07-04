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
use Fitted\ProductManager\Monolog\Logger;
use Fitted\ProductManager\Monolog\Processor\WebProcessor;
use Fitted\ProductManager\Monolog\Formatter\LineFormatter;

class AbstractProcessingHandlerTest extends TestCase
{
    /**
     * @covers Fitted\ProductManager\Monolog\Handler\FormattableHandlerTrait::getFormatter
     * @covers Fitted\ProductManager\Monolog\Handler\FormattableHandlerTrait::setFormatter
     */
    public function testConstructAndGetSet()
    {
        $handler = $this->getMockForAbstractClass('Fitted\ProductManager\Monolog\Handler\AbstractProcessingHandler', [Logger::WARNING, false]);
        $handler->setFormatter($formatter = new LineFormatter);
        $this->assertSame($formatter, $handler->getFormatter());
    }

    /**
     * @covers Fitted\ProductManager\Monolog\Handler\AbstractProcessingHandler::handle
     */
    public function testHandleLowerLevelMessage()
    {
        $handler = $this->getMockForAbstractClass('Fitted\ProductManager\Monolog\Handler\AbstractProcessingHandler', [Logger::WARNING, true]);
        $this->assertFalse($handler->handle($this->getRecord(Logger::DEBUG)));
    }

    /**
     * @covers Fitted\ProductManager\Monolog\Handler\AbstractProcessingHandler::handle
     */
    public function testHandleBubbling()
    {
        $handler = $this->getMockForAbstractClass('Fitted\ProductManager\Monolog\Handler\AbstractProcessingHandler', [Logger::DEBUG, true]);
        $this->assertFalse($handler->handle($this->getRecord()));
    }

    /**
     * @covers Fitted\ProductManager\Monolog\Handler\AbstractProcessingHandler::handle
     */
    public function testHandleNotBubbling()
    {
        $handler = $this->getMockForAbstractClass('Fitted\ProductManager\Monolog\Handler\AbstractProcessingHandler', [Logger::DEBUG, false]);
        $this->assertTrue($handler->handle($this->getRecord()));
    }

    /**
     * @covers Fitted\ProductManager\Monolog\Handler\AbstractProcessingHandler::handle
     */
    public function testHandleIsFalseWhenNotHandled()
    {
        $handler = $this->getMockForAbstractClass('Fitted\ProductManager\Monolog\Handler\AbstractProcessingHandler', [Logger::WARNING, false]);
        $this->assertTrue($handler->handle($this->getRecord()));
        $this->assertFalse($handler->handle($this->getRecord(Logger::DEBUG)));
    }

    /**
     * @covers Fitted\ProductManager\Monolog\Handler\AbstractProcessingHandler::processRecord
     */
    public function testProcessRecord()
    {
        $handler = $this->getMockForAbstractClass('Fitted\ProductManager\Monolog\Handler\AbstractProcessingHandler');
        $handler->pushProcessor(new WebProcessor([
            'REQUEST_URI' => '',
            'REQUEST_METHOD' => '',
            'REMOTE_ADDR' => '',
            'SERVER_NAME' => '',
            'UNIQUE_ID' => '',
        ]));
        $handledRecord = null;
        $handler->expects($this->once())
            ->method('write')
            ->will($this->returnCallback(function ($record) use (&$handledRecord) {
                $handledRecord = $record;
            }))
        ;
        $handler->handle($this->getRecord());
        $this->assertEquals(6, count($handledRecord['extra']));
    }

    /**
     * @covers Fitted\ProductManager\Monolog\Handler\ProcessableHandlerTrait::pushProcessor
     * @covers Fitted\ProductManager\Monolog\Handler\ProcessableHandlerTrait::popProcessor
     */
    public function testPushPopProcessor()
    {
        $logger = $this->getMockForAbstractClass('Fitted\ProductManager\Monolog\Handler\AbstractProcessingHandler');
        $processor1 = new WebProcessor;
        $processor2 = new WebProcessor;

        $logger->pushProcessor($processor1);
        $logger->pushProcessor($processor2);

        $this->assertEquals($processor2, $logger->popProcessor());
        $this->assertEquals($processor1, $logger->popProcessor());

        $this->expectException(\LogicException::class);

        $logger->popProcessor();
    }

    /**
     * @covers Fitted\ProductManager\Monolog\Handler\ProcessableHandlerTrait::pushProcessor
     */
    public function testPushProcessorWithNonCallable()
    {
        $handler = $this->getMockForAbstractClass('Fitted\ProductManager\Monolog\Handler\AbstractProcessingHandler');

        $this->expectException(\TypeError::class);

        $handler->pushProcessor(new \stdClass());
    }

    /**
     * @covers Fitted\ProductManager\Monolog\Handler\FormattableHandlerTrait::getFormatter
     * @covers Fitted\ProductManager\Monolog\Handler\FormattableHandlerTrait::getDefaultFormatter
     */
    public function testGetFormatterInitializesDefault()
    {
        $handler = $this->getMockForAbstractClass('Fitted\ProductManager\Monolog\Handler\AbstractProcessingHandler');
        $this->assertInstanceOf(LineFormatter::class, $handler->getFormatter());
    }
}
