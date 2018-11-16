<?php
namespace hannespries\events;

class EventHandler {
    protected $listeners = [];

    protected static $instance = null;
    protected static $cache = [];

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     *
     */
    public function clear()
    {
        $this->listeners = [];
    }

    /**
     * "listeners": [
     *      {
     *          "alias": "testEvent",
     *          "scope": "on",
     *          "class": "\\example\\classes\\ExampleListener",
     *          "method": "onTestEvent"
     *      }
     * ]
     *
     * scope default is on
     * method default is on + usfirst(alias)
     *
     * @param \DirectoryIterator $list
     * @param string $deploymentFilePath
     */
    public function readByFolderList(\DirectoryIterator $list, string $deploymentFilePath = 'deploy/listeners.json')
    {
        foreach ($list as $folder) {
            if (!preg_match('/\/$/', $folder)) {
                $folder .= '/';
            }
            if (is_file($folder . $deploymentFilePath)) {
                $json = json_decode(file_get_contents($folder . $deploymentFilePath), true);
                if (isset($json['listeners']) && is_array($json['listeners'])) {
                    foreach ($json['listeners'] as $listener) {
                        if (!isset($listener['scope'])) {
                            $listener['scope'] = 'on';
                        }

                        if (!isset($listener['method'])) {
                            $listener['method'] = 'on' . ucfirst($listener['alias']);
                        }

                        //{alias:,scope:,class:,method:}
                        $this->addListener($listener['alias'], $listener['class'], $listener['method'], $listener['scope']);
                    }
                }
            }
        }
    }

    /**
     * @param $alias
     * @param $scope
     * @param $clazz
     * @param $method
     */
    public function addListener($alias, $clazz, $method = null, $scope = 'on')
    {
        if($method === null){
            $method = 'on' . ucfirst($alias);
        }

        $listener = new EventListener($alias, $scope, $clazz, $method);
        if (!isset($this->listeners[$alias . ':' . $scope])) {
            $this->listeners[$alias . ':' . $scope] = [];
        }
        $this->listeners[$alias . ':' . $scope][] = $listener;
    }

    /**
     * @param string $alias
     * @param $returnValue
     * @param array $args
     * @return mixed
     */
    public function fireFilterEvent(string $alias, $returnValue, array $args = [])
    {
        return $this->call($alias, $returnValue, 'on', $args);
    }

    /**
     * @param string $alias
     * @param $obj
     * @param string $scope
     * @param array $args
     * @return mixed
     */
    protected function call(string $alias, $obj, string $scope, array $args = [])
    {
        if (isset($this->listeners[$alias . ':' . $scope]) && is_array($this->listeners[$alias . ':' . $scope])) {
            foreach ($this->listeners[$alias . ':' . $scope] as $listener) {
                /** @var EventListener $listener */
                try {
                    if ($listener->isActive()) {
                        $ref = new \ReflectionClass($listener->getClazz());
                        if ($ref->hasMethod($listener->getMethod())) {
                            $m = $ref->getMethod($listener->getMethod());
                            $instance = null;
                            if (!isset(self::$cache[$listener->getClazz()])) {
                                $instance = $ref->newInstance();
                                self::$cache[$listener->getClazz()] = $instance;
                            } else {
                                $instance = self::$cache[$listener->getClazz()];
                            }
                            $objTmp = $m->invoke($instance, $obj, $args);
                            if ($objTmp) {
                                $obj = $objTmp;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }
        }
        return $obj;
    }
}