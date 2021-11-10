<?php

use Mockery as m;
use GetPhoto\Oauth2\Client\Cache\CacherPrefixTrait as CacherPrefixTrait;

class CacherPrefixTraitTest extends PHPUnit_Framework_TestCase
{
    public function testSetAndGetPrefix() {
        $trait = $this->getObjectForTrait(CacherPrefixTrait::class);
        $trait->setPrefix('test');
       	$this->assertEquals('test_', $trait->getPrefix());
    }
}
