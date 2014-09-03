<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

namespace PetrGrishin\View\ViewProcessor;


use PetrGrishin\View\View;

abstract class BaseViewProcessor {
    /** @var  View */
    protected $view;
    protected $isAjaxMode = false;
    protected $params = array();

    public function __construct(View $view) {
        $this->view = $view;
    }

    abstract public function processView();

    public static function className() {
        return get_called_class();
    }

    /**
     * @return boolean
     */
    public function getIsAjaxMode() {
        return $this->isAjaxMode;
    }

    /**
     * @param boolean $isAjaxMode
     * @return $this
     */
    public function setIsAjaxMode($isAjaxMode) {
        $this->isAjaxMode = $isAjaxMode;
        return $this;
    }

    /**
     * @return array
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setParams($params) {
        $this->params = $params;
        return $this;
    }

    public function setParam($name, $value) {
        $this->params[$name] = $value;
        return $this;
    }
}
 