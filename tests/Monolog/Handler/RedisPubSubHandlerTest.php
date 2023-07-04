<?php

declare(strict_types=1);

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
use Fitted\ProductManager\Monolog\Formatter\LineFormatter;

class RedisPubSubHandlerTest extends TestCase
{
    public function testConstructorShouldThrowExceptionForInvalidRedis()
    {
        $this->expectException(\InvalidArgumentException::class);

        new RedisPubSubHandler(new \stdClass(), 'key');
    }

    public function testConstructorShouldWorkWithPredis()
    {
        $redis = $this->createMock('Predis\Client');
        $this->assertInstanceof('Fitted\ProductManager\Monolog\Handler\RedisPubSubHandler', new RedisPubSubHandler($redis, 'key'));
    }

    public function testConstructorShouldWorkWithRedis()
    {
        if (!class_exists('Redis')) {
            $this->markTestSkipped('The redis ext is required to run this test');
        }

        $redis = $this->createMock('Redis');
        $this->assertInstanceof('Fitted\ProductManager\Monolog\Handler\RedisPubSubHandler', new RedisPubSubHandler($redis, 'key'));
    }

    public function testPredisHandle()
    {
        $redis = $this->prophesize('Predis\Client');
        $redis->publish('key', 'test')->shouldBeCalled();
        $redis = $redis->reveal();

        $record = $this->getRecord(Logger::WARNING, 'test', ['data' => new \stdClass(), 'foo' => 34]);

        $handler = new RedisPubSubHandler($redis, 'key');
        $handler->setFormatter(new LineFormatter("%message%"));
        $handler->handle($record);
    }

    public function testRedisHandle()
    {
        if (!class_exists('Redis')) {
            $this->markTestSkipped('The redis ext is required to run this test');
        }

        $redis = $this->createPartialMock('Redis', ['publish']);

        $redis->expects($this->once())
            ->method('publish')
            ->with('key', 'test');

        $record = $this->getRecord(Logger::WARNING, 'test', ['data' => new \stdClass(), 'foo' => 34]);

        $handler = new RedisPubSubHandler($redis, 'key');
        $handler->setFormatter(new LineFormatter("%message%"));
        $handler->handle($record);
    }
}
