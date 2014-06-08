<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

class InnerWidget extends \PetrGrishin\View\Widget {

    public static function className() {
        return get_called_class();
    }

    public function run() {
        $this->render('inner');
    }
}
 