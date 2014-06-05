<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

namespace PetrGrishin\View;


class View {
    /** @var array() */
    protected $params;

    public static function className() {
        return get_called_class();
    }

    public function getParams() {
        return $this->params;
    }

    public function setParams($values) {
        $this->params = $values;
    }

    public function render($sourceFile, $return) {
        printf($sourceFile);
        var_export($this->getParams());
        return true;
    }
}
 