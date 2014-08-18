<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

namespace PetrGrishin\View;


use PetrGrishin\ArrayAccess\ArrayAccess;

class Response {
    /** @var  ArrayAccess */
    protected $params;
    protected $content;

    public function __construct($params = null) {
        $this->params = ArrayAccess::create($params);
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function setParam($path, $value) {
        $this->params->setValue($path, $value);
        return $this;
    }

    public function getParam($path) {
        return $this->params->getParam($path);
    }

    public function __toString() {
        return $this->encode(array(
            'content' => $this->content,
            'responseParams' => $this->params,
        ));
    }

    protected function encode($data) {
        return json_encode($data);
    }
}
 