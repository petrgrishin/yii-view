<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

namespace PetrGrishin\View;


use CApplicationComponent;
use IViewRenderer;

class ViewRenderer extends CApplicationComponent implements IViewRenderer {
    public $fileExtension = '.php';
    public $fileExtensionJs = '.js';

    public static function className() {
        return get_called_class();
    }

    public function renderFile($context, $sourceFile, $params, $isReturn) {
        $view = new View($context);
        if ($context instanceof \PetrGrishin\View\Widget) {
//            $context->setView($view);
        }
        $view->setParams($params);
        $response = $view->render($sourceFile, $isReturn);
//        $dependsWidgets = $view->getWidgets();
        return $response;
    }

    public function getJsFile($sourceFile) {
        return $this->getBaseFilename($sourceFile) . $this->fileExtensionJs;
    }

    public function getBaseFilename($sourceFile) {
        return substr($sourceFile, 0, - strlen($this->fileExtension));
    }
}
