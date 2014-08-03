<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

namespace PetrGrishin\View;


use CApplicationComponent;
use IViewRenderer;
use PetrGrishin\Widget\Widget;

class ViewRenderer extends CApplicationComponent implements IViewRenderer {
    public $fileExtension = '.php';
    public $fileExtensionJs = '.js';
    public $scriptProcessorClass;
    private $_scriptProcessor;

    public static function className() {
        return get_called_class();
    }

    public function renderFile($context, $sourceFile, $params, $isReturn) {
        $viewId = $this->generateViewId($sourceFile);
        $view = new View($viewId, $context);
        $isContextWidget = $this->isContextWidget($context) && $context->setView($view);
        $view->setParams($params);
        $view->setScriptFile($this->getScriptFile($sourceFile));
        if (!$isContextWidget && $this->isAjaxRequest()) {
            $response = $this->renderAjax($view, $sourceFile);
            $this->getScriptProcessor()->processView($view, true);
        } else {
            $response = $view->render($sourceFile, $isReturn);
            $this->getScriptProcessor()->processView($view);
        }
        return $response;
    }

    public function renderAjax(View $view, $sourceFile) {
        // todo: load scriptsFiles
        $this->getClientScript()->reset();
        return json_encode(array(
            'content' => $view->render($sourceFile, true),
            'name' => $view->getId(),
            'params' => $view->getJsParams(),
            'dependents' => $this->getScriptProcessor()->getDependents($view),
        ));
    }

    public function getScriptFile($sourceFile) {
        return $this->getBaseFilename($sourceFile) . $this->fileExtensionJs;
    }

    public function getBaseFilename($sourceFile) {
        return substr($sourceFile, 0, - strlen($this->fileExtension));
    }

    /**
     * @return \PetrGrishin\View\ViewScriptProcessor
     */
    public function getScriptProcessor() {
        if (empty($this->_scriptProcessor)) {
            $scriptProcessorClass = $this->scriptProcessorClass ?: ViewScriptProcessor::className();
            $this->_scriptProcessor = new $scriptProcessorClass();
        }
        return $this->_scriptProcessor;
    }

    /**
     * @return \CApplication
     */
    protected function getApp() {
        return \Yii::app();
    }

    /**
     * @return \CHttpRequest
     */
    protected function getRequest() {
        return $this->getApp()->getRequest();
    }

    /**
     * @return \CClientScript
     */
    protected function getClientScript() {
        return $this->getApp()->getComponent('clientScript');
    }

    /**
     * @return bool
     */
    protected function isAjaxRequest() {
        return $this->getRequest()->getIsAjaxRequest();
    }

    protected function isContextWidget($context) {
        return $context instanceof Widget;
    }

    /**
     * @param $sourceFile
     * @return string
     */
    protected function generateViewId($sourceFile) {
        return sprintf('%s', sha1($sourceFile));
    }
}
