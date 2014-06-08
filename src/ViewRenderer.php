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
            $context->setView($view);
        }
        $view->setParams($params);
        $view->setScriptFile($this->getScriptFile($sourceFile));
        $response = $view->render($sourceFile, $isReturn);
        $this->processorWidgetScript($view->getWidgets());
        return $response;
    }

    public function getScriptFile($sourceFile) {
        return $this->getBaseFilename($sourceFile) . $this->fileExtensionJs;
    }

    public function getBaseFilename($sourceFile) {
        return substr($sourceFile, 0, - strlen($this->fileExtension));
    }

    public function processorWidgetScript($widgetsPoll) {
        foreach ($widgetsPoll as $widgetClass => $widgets) {
            /** @var \PetrGrishin\View\Widget $widget */
            foreach ($widgets as $widget) {
                printf("Script file: %s\n", $widget->getView()->getScriptFile());
            }
        }
    }

}
