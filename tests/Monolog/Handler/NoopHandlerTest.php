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

/**
 * @covers Fitted\ProductManager\Monolog\Handler\NoopHandler::handle
 */
class NoopHandlerTest extends TestCase
{
    /**
     * @dataProvider logLevelsProvider
     */
    public function testIsHandling($level)
    {
        $handler = new NoopHandler();
        $this->assertTrue($handler->isHandling($this->getRecord($level)));
    }

    /**
     * @dataProvider logLevelsProvider
     */
    public function testHandle($level)
    {
        $handler = new NoopHandler();
        $this->assertFalse($handler->handle($this->getRecord($level)));
    }

    public function logLevelsProvider()
    {
        return array_map(
            function ($level) {
                return [$level];
            },
            array_values(Logger::getLevels())
        );
    }
}
