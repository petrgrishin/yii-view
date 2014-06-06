<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

class TestWidget extends CWidget {

    public static function className() {
        return get_called_class();
    }

    public function run() {
        return printf("Test widget\n");
    }
}
 