<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

namespace PetrGrishin\View;


class View {
    /** @var string */
    protected $id;
    /** @var array */
    protected $params = array();
    /** @var array */
    protected $jsParams = array();
    /** @var \CBaseController */
    protected $context;
    /** @var array */
    protected $widgets = array();
    /** @var string */
    protected $scriptFile;

    public static function className() {
        return get_called_class();
    }

    public function __construct($id, $context) {
        $this->id = $id;
        $this->context = $context;
    }

    public function getId() {
        return $this->id;
    }

    public function getContext() {
        return $this->context;
    }

    public function getParams() {
        return $this->params;
    }

    public function setParams($values) {
        $this->params = $values;
    }

    public function getJsParams() {
        return $this->jsParams;
    }

    public function setJsParams($jsParams) {
        $this->jsParams = $jsParams;
        return $this;
    }

    public function getScriptFile() {
        return $this->scriptFile;
    }

    public function setScriptFile($scriptFile) {
        $this->scriptFile = $scriptFile;
        return $this;
    }

    public function widget($className, $name, $params = array()) {
        if (!array_key_exists($className, $this->widgets)) {
            $this->widgets[$className] = array();
        }
        /** @var \PetrGrishin\View\Widget $widget */
        $widget = $this->context->createWidget($className, $params);
        $widget->setName($name);
        $this->widgets[$className][] = $widget;
        return $widget;
    }

    public function getWidgets() {
        return $this->widgets;
    }

    public function render($sourceFile, $return) {
        if ($return) {
            ob_start();
            ob_implicit_flush(false);
            require $sourceFile;
            return ob_get_clean();
        }
        require $sourceFile;
        return null;
    }
}
 