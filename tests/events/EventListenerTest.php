<?php

use PHPUnit\Framework\TestCase;

class EventListenerTest extends TestCase{
    public function test_createInstance(){
        $instance = new \hannespries\events\EventListener('example');
        $this->assertNotNull($instance);
    }

    public function test_checkDefaultValues(){
        $instance = new \hannespries\events\EventListener('example');
        $this->assertEquals('on', $instance->getScope());
    }
}