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
        $widgetsIds = $this->getDependents($view);
        $isAppend = $this->appendScriptFile($view->getId(), $view->getScriptFile());
        $run = !$view->getContext() instanceof Widget;
        $run && $isAppend && $this->runScript($view->getId(), $view->getJsParams() , $widgetsIds);
    }

    public function getDependents(View $view) {
        $widgetsIds = array();
        foreach (array_reverse($view->getWidgets()) as $widgetClass => $widgets) {
            /** @var Widget $widget */
            foreach ($widgets as $widget) {
                $isAppend = $this->appendScriptFile($widget->getView()->getId(), $widget->getView()->getScriptFile());
                $isAppend && $widgetsIds[$widget->getName()] = array(
                    'name' => $widget->getView()->getId(),
                    'params' => $widget->getView()->getJsParams(),
                    'dependents' => $this->getDependents($widget->getView()),
                );
            }
        }
        return $widgetsIds;
    }

    public function appendScriptFile($id, $fileScript) {
        if (!file_exists($fileScript)) {
            return false;
        }
        $script = trim(file_get_contents($fileScript));
        if (!$script) {
            return false;
        }
        $this->getClientScript()->registerScript($id . '_append', sprintf("App.register('%s', %s);", $id, $script), CClientScript::POS_END);
        return true;
    }

    public function runScript($id, $jsParams, $widgetsIds) {
        $script = sprintf("App.run('%s', %s, %s);", $id, json_encode($jsParams), json_encode($widgetsIds));
        $this->getClientScript()->registerScript($id . '_run', $script, CClientScript::POS_END);
    }

    /**
     * @return CClientScript
     */
    protected function getClientScript() {
        return \Yii::app()->getComponent('clientScript');
    }
}
 