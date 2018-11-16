<?php
namespace hannespries\events;

class EventListener{
	private $alias = '';
	private $scope = 'on';
	private $clazz = '';
	private $method = '';
	private $active = true;

    /**
     * EventListener constructor.
     * @param string $alias
     * @param string $scope
     * @param string $clazz
     * @param string $method
     */
	public function __construct(string $alias, string $scope = 'on', string $clazz = '', string $method = ''){
		$this->alias = $alias;
		$this->scope = $scope;
		$this->clazz = $clazz;
		$this->method = $method;
	}
	
	public function getAlias() {
		return $this->alias;
	}
	
	public function setAlias($alias) {
		$this->alias = $alias;
	}
	
	public function getScope() {
		return $this->scope;
	}
	
	public function setScope($scope) {
		$this->scope = $scope;
	}
	
	public function getClazz() {
		return $this->clazz;
	}
	
	public function setClazz($clazz) {
		$this->clazz = $clazz;
	}
	
	public function getMethod() {
		return $this->method;
	}
	
	public function setMethod($method) {
		$this->method = $method;
	}
	
	public function isActive() {
		return $this->active;
	}
	
	public function setActive($active) {
		$this->active = $active;
	}
}