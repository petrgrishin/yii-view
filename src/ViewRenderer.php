<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

namespace PetrGrishin\View;


use CApplicationComponent;
use IViewRenderer;

class ViewRenderer extends CApplicationComponent implements IViewRenderer {
    public $fileExtension = '.php';

    public static function className() {
        return get_called_class();
    }

    public function renderFile($context, $sourceFile, $data, $return) {

    }
}
