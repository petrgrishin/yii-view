<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

namespace PetrGrishin\View\ViewProcessor;


use PetrGrishin\View\View;
use PetrGrishin\Widget\Widget;

class StyleViewProcessor extends BaseViewProcessor {
    const PATH_STYLE = 'style';
    const FILENAME_STYLE = 'style.css';

    public function processView() {
        $styleFiles = array();
        if ($styleFile = $this->resolveStyleFile($this->view)) {
            $styleFiles[] = $styleFile;
        }
        $styleFiles = array_merge($styleFiles, $this->getDependents($this->view));
        $this->setParam('styles', $styleFiles);
        if ($this->getIsAjaxMode()) {
            return $this;
        }
        foreach ($styleFiles as $styleFile) {
            $this->appendStyleFile($styleFile);
        }
        return $this;
    }

    public function getDependents(View $view) {
        $widgetsStyles = array();
        foreach (array_reverse($view->getWidgets()) as $widgetClass => $widgets) {
            /** @var Widget $widget */
            foreach ($widgets as $widget) {
                $widgetsStyles[] = $this->resolveStyleFile($widget->getView());
                $widgetsStyles = array_merge($widgetsStyles, $this->getDependents($widget->getView()));
            }
        }
        return array_unique($widgetsStyles);
    }

    protected function resolveStyleFile(View $view) {
        $stylePath = sprintf('%s/%s', $view->getTemplatePath(), self::PATH_STYLE);
        if (!is_dir($stylePath)) {
            return false;
        }
        $assetPath = $this->publishStyle($stylePath);
        $assetStyleFile = sprintf('%s/%s', $assetPath, self::FILENAME_STYLE);
        return $assetStyleFile;
    }

    protected function publishStyle($stylePath) {
        $assetPath = $this->getAssetManager()->publish($stylePath);
        return $assetPath;
    }

    protected function appendStyleFile($assetFile) {
        $script = sprintf("App.registerStyleFile('%s');", $assetFile);
        $this->getClientScript()->registerScript($assetFile, $script, \CClientScript::POS_END);
        return true;
    }

    /**
     * @return \CAssetManager
     */
    protected function getAssetManager() {
        return $this->getApp()->getComponent('assetManager');
    }

    /**
     * @return \CClientScript
     */
    protected function getClientScript() {
        return $this->getApp()->getComponent('clientScript');
    }

    protected function getApp() {
        return \Yii::app();
    }
}
 