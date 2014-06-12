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
                $this->appendScriptFile($widget->getView()->getId(), $widget->getView()->getScriptFile());
                $widgetsIds[$widget->getName()] = $widget->getView()->getId();
            }
        }
        $isAppend = $this->appendScriptFile($view->getId(), $view->getScriptFile());
        $isAppend && $this->runScript($view->getId(), $widgetsIds);
    }

    public function appendScriptFile($id, $fileScript) {
        if (!file_exists($fileScript)) {
            return false;
        }
        $script = trim(file_get_contents($fileScript));
        if (!$script) {
            return false;
        }
        $this->getClientScript()->registerScript($id . '_append', sprintf("App.register('%s', %s);", $id, $script), CClientScript::POS_HEAD);
        return true;
    }

    public function runScript($id, $widgetsIds) {
        $script = sprintf("App.run('%s', %s);", $id, json_encode($widgetsIds));
        $this->getClientScript()->registerScript($id . '_run', $script, CClientScript::POS_HEAD);
    }

    /**
     * @return CClientScript
     */
    protected function getClientScript() {
        return \Yii::app()->getComponent('clientScript');
    }
}
 