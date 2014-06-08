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

    private $registerScript = array();

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
        $this->processorScript($view);
        return $response;
    }

    public function getScriptFile($sourceFile) {
        return $this->getBaseFilename($sourceFile) . $this->fileExtensionJs;
    }

    public function getBaseFilename($sourceFile) {
        return substr($sourceFile, 0, - strlen($this->fileExtension));
    }

    /**
     * @param \PetrGrishin\View\View $view
     */
    public function processorScript($view) {
        $widgetsIds = array();
        foreach (array_reverse($view->getWidgets()) as $widgetClass => $widgets) {
            /** @var \PetrGrishin\View\Widget $widget */
            foreach ($widgets as $widget) {
                printf("App.register('%s', '%s');\n", $widget->getView()->getId(), $widget->getView()->getScriptFile());
                $widgetsIds[$widget->getName()] = $widget->getView()->getId();
            }
        }
        printf("App.register('%s', '%s');\n", $view->getId(), $view->getScriptFile());
        printf("App.do('%s', %s);\n", $view->getId(), json_encode($widgetsIds));
    }

    public function registerScript($id, $fileScript) {
        if (!array_key_exists($id, $this->registerScript)) {
            $this->registerScript[$id] = $fileScript;
            printf("App.register('%s', '%s');\n", $id, $fileScript);
        }
        return $this;
    }

}
