<?php
namespace Kristuff\Miniweb\Tests\Core;

use Kristuff\Miniweb\Core\Json;

class FormatTest extends \PHPUnit\Framework\TestCase
{
    public function testLoadJson()
    {
       // $expected = '[{"name": "color", "value": "red", "exists":true},{"name": "color", "value": "abcd","exists":false}]';
       $json = Json::FromFile(__DIR__ . '/../_data/simple.json');
       
       $this->assertEquals('red', $json[0]['value']);
       $this->assertTrue($json[0]['exists']);
    }
}