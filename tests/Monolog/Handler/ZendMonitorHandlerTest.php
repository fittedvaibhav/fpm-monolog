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

class ZendMonitorHandlerTest extends TestCase
{
    protected $zendMonitorHandler;

    public function setUp(): void
    {
        if (!function_exists('zend_monitor_custom_event')) {
            $this->markTestSkipped('ZendServer is not installed');
        }
    }

    /**
     * @covers  Fitted\ProductManager\Monolog\Handler\ZendMonitorHandler::write
     */
    public function testWrite()
    {
        $record = $this->getRecord();
        $formatterResult = [
            'message' => $record['message'],
        ];

        $zendMonitor = $this->getMockBuilder('Fitted\ProductManager\Monolog\Handler\ZendMonitorHandler')
            ->onlyMethods(['writeZendMonitorCustomEvent', 'getDefaultFormatter'])
            ->getMock();

        $formatterMock = $this->getMockBuilder('Fitted\ProductManager\Monolog\Formatter\NormalizerFormatter')
            ->disableOriginalConstructor()
            ->getMock();

        $formatterMock->expects($this->once())
            ->method('format')
            ->will($this->returnValue($formatterResult));

        $zendMonitor->expects($this->once())
            ->method('getDefaultFormatter')
            ->will($this->returnValue($formatterMock));

        $levelMap = $zendMonitor->getLevelMap();

        $zendMonitor->expects($this->once())
            ->method('writeZendMonitorCustomEvent')
            ->with(
                Logger::getLevelName($record['level']),
                $record['message'],
                $formatterResult,
                $levelMap[$record['level']]
            );

        $zendMonitor->handle($record);
    }

    /**
     * @covers Fitted\ProductManager\Monolog\Handler\ZendMonitorHandler::getDefaultFormatter
     */
    public function testGetDefaultFormatterReturnsNormalizerFormatter()
    {
        $zendMonitor = new ZendMonitorHandler();
        $this->assertInstanceOf('Fitted\ProductManager\Monolog\Formatter\NormalizerFormatter', $zendMonitor->getDefaultFormatter());
    }
}
