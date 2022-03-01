<?php declare(strict_types=1);
namespace Vulcan\test;
use \PHPUnit\Framework\TestCase;

final class APITest extends TestCase
{
    public function testIsSingleton(): void
    {
        $api = \Vulcan\lib\models\api\API::getInstance();
        $this->assertInstanceOf(
            \Vulcan\lib\ASingleton::class,
            $api
        );
    }

}