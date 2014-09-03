<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

namespace PetrGrishin\View\ViewProcessor;


use PetrGrishin\View\View;
use PetrGrishin\Widget\Widget;

class ScriptViewProcessor extends BaseViewProcessor {
    const EXTENSION_SCRIPT = '.js';
    const MARKER_ID = '{{id}}';

    private $assertPath;
    private $publicPath;

    public function processView() {
        $scriptFile = sprintf('%s%s', $this->view->getTemplatePath(), self::EXTENSION_SCRIPT);
        $widgetsIds = $this->getDependents($this->view);
        $this->setParam('name', $this->view->getId());
        $this->setParam('params', $this->view->getJsParams());
        $this->setParam('dependents', $widgetsIds);
        $isAppend = $this->appendScriptFile($this->view->getId(), $scriptFile);
        if ($this->getIsAjaxMode()) {
            return $this;
        }
        $run = !$this->view->getContext() instanceof Widget;
        $run && $isAppend && $this->runScript($this->view->getId(), $this->view->getJsParams() , $widgetsIds);
        return $this;
    }

    protected function getDependents(View $view) {
        $widgetsIds = array();
        foreach (array_reverse($view->getWidgets()) as $widgetClass => $widgets) {
            /** @var Widget $widget */
            foreach ($widgets as $widget) {
                $scriptFile = sprintf('%s%s', $widget->getView()->getTemplatePath(), self::EXTENSION_SCRIPT);
                $isAppend = $this->appendScriptFile($widget->getView()->getId(), $scriptFile);
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
        $prepareScriptFile = $this->prepareScriptFile($id, $fileScript);
        if (false === $prepareScriptFile) {
            return false;
        }
        return true;
    }

    public function runScript($id, $jsParams, $widgetsIds) {
        $script = sprintf("App.run('%s', %s, %s);", $id, json_encode($jsParams), json_encode($widgetsIds));
        $this->getClientScript()->registerScript($id . '_run', $script, \CClientScript::POS_END);
    }

    protected function prepareScriptFile($id, $fileScript) {
        $script = trim(file_get_contents($fileScript));
        if (!$script) {
            return false;
        }
        $tempFile = $this->generateAbsoluteAssertPath($id);
        $content = str_replace(self::MARKER_ID, $id, $script);
        if (false === file_put_contents($tempFile, $content)) {
            throw new \Exception('File `%s` do not save', $tempFile);
        }
        return $this->generateAssertPath($id);
    }

    public function getAssertPath() {
        return $this->assertPath ?: 'assets/scripts';
    }

    public function setAssertPath($assertPath) {
        $this->assertPath = $assertPath;
        return $this;
    }

    /**
     * @return string
     */
    public function getPublicPath() {
        return $this->publicPath ?: \Yii::getPathOfAlias('webroot');
    }

    /**
     * @param string $publicPath
     * @return $this
     */
    public function setPublicPath($publicPath) {
        $this->publicPath = $publicPath;
        return $this;
    }

    protected function getAbsoluteAssertPath() {
        $path = sprintf('%s/%s', $this->getPublicPath(), $this->getAssertPath());
        if (false === is_dir($path) && false === mkdir($path, 0777, true)) {
            throw new \Exception(sprintf('Do not create directory `%s`', $path));
        }
        if (false === is_dir($path) || false === is_writable($path)) {
            throw new \Exception(sprintf('No write access to directory `%s`', $path));
        }
        return $path;
    }

    /**
     * @return \CClientScript
     */
    protected function getClientScript() {
        return \Yii::app()->getComponent('clientScript');
    }

    /**
     * @param $id
     * @return string
     */
    protected function generateAbsoluteAssertPath($id) {
        return sprintf('%s/%s', $this->getAbsoluteAssertPath(), $this->generateAssertScriptFileName($id));
    }

    protected function generateAssertPath($id) {
        return sprintf('/%s/%s', $this->getAssertPath(), $this->generateAssertScriptFileName($id));
    }

    protected function generateAssertScriptFileName($id) {
        return sprintf('%s.js', $id);
    }
}
 