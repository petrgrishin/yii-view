<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

namespace PetrGrishin\View;


use CClientScript;

class ViewScriptProcessor extends \CApplicationComponent {

    public static function className() {
        return get_called_class();
    }

    public function processView(View $view) {
        $widgetsIds = array();
        foreach (array_reverse($view->getWidgets()) as $widgetClass => $widgets) {
            /** @var \PetrGrishin\View\Widget $widget */
            foreach ($widgets as $widget) {
                $this->appendScriptFile($widget->getView()->getScriptFile());
                printf("App.register('%s', '%s');\n", $widget->getView()->getId(), $widget->getView()->getScriptFile());
                $widgetsIds[$widget->getName()] = $widget->getView()->getId();
            }
        }
        printf("App.register('%s', '%s');\n", $view->getId(), $view->getScriptFile());
        printf("App.do('%s', %s);\n", $view->getId(), json_encode($widgetsIds));
    }

    public function appendScriptFile($fileScript) {
        $this->getClientScript()->registerScriptFile($fileScript, CClientScript::POS_HEAD);
        return $this;
    }

    /**
     * @return CClientScript
     */
    protected function getClientScript() {
        return \Yii::app()->getComponent('clientScript');
    }
}
 