<?php

class SimpleListener {
    public function onTestEvent($obj, $params = []){
        if(isset($params['test'])){
            $obj .= $params['test'];
        }
        return $obj;
    }
}

class EventHandlerTest extends \PHPUnit\Framework\TestCase{
    public function test_simpleEvent(){
        $handler = new \hannespries\events\EventHandler();
        $handler->addListener('testEvent', 'SimpleListener', 'onTestEvent', 'on');
        $result = $handler->fireFilterEvent('testEvent', 'blubb', []);
        $this->assertEquals('blubb', $result);
    }

    public function test_complexEvent(){
        $handler = new \hannespries\events\EventHandler();
        $handler->addListener('testEvent', 'SimpleListener', 'onTestEvent', 'on');
        $result = $handler->fireFilterEvent('testEvent', 'blubb', ['test' => '_23']);
        $this->assertEquals('blubb_23', $result);
    }

    public function test_defaultScopeEvent(){
        $handler = new \hannespries\events\EventHandler();
        $handler->addListener('testEvent', 'SimpleListener', 'onTestEvent');
        $result = $handler->fireFilterEvent('testEvent', 'blubb', ['test' => '_23']);
        $this->assertEquals('blubb_23', $result);
    }

    public function test_defaultMethodEvent(){
        $handler = new \hannespries\events\EventHandler();
        $handler->addListener('testEvent', 'SimpleListener');
        $result = $handler->fireFilterEvent('testEvent', 'blubb', ['test' => '_23']);
        $this->assertEquals('blubb_23', $result);
    }
}