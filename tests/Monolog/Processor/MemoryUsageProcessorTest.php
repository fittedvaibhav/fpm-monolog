<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fitted\ProductManager\Monolog\Processor;

use Fitted\ProductManager\Monolog\Test\TestCase;

class MemoryUsageProcessorTest extends TestCase
{
    /**
     * @covers Fitted\ProductManager\Monolog\Processor\MemoryUsageProcessor::__invoke
     * @covers Fitted\ProductManager\Monolog\Processor\MemoryProcessor::formatBytes
     */
    public function testProcessor()
    {
        $processor = new MemoryUsageProcessor();
        $record = $processor($this->getRecord());
        $this->assertArrayHasKey('memory_usage', $record['extra']);
        $this->assertRegExp('#[0-9.]+ (M|K)?B$#', $record['extra']['memory_usage']);
    }

    /**
     * @covers Fitted\ProductManager\Monolog\Processor\MemoryUsageProcessor::__invoke
     * @covers Fitted\ProductManager\Monolog\Processor\MemoryProcessor::formatBytes
     */
    public function testProcessorWithoutFormatting()
    {
        $processor = new MemoryUsageProcessor(true, false);
        $record = $processor($this->getRecord());
        $this->assertArrayHasKey('memory_usage', $record['extra']);
        $this->assertIsInt($record['extra']['memory_usage']);
        $this->assertGreaterThan(0, $record['extra']['memory_usage']);
    }
}
