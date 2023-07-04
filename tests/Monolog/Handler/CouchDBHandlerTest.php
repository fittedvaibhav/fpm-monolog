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

class CouchDBHandlerTest extends TestCase
{
    public function testHandle()
    {
        $record = $this->getRecord(Logger::WARNING, 'test', ['data' => new \stdClass, 'foo' => 34]);

        $handler = new CouchDBHandler();

        try {
            $handler->handle($record);
        } catch (\RuntimeException $e) {
            $this->markTestSkipped('Could not connect to couchdb server on http://localhost:5984');
        }
    }
}
