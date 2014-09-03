<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

namespace PetrGrishin\View;


use CApplicationComponent;
use IViewRenderer;
use PetrGrishin\View\ViewProcessor\HtmlViewProcessor;
use PetrGrishin\View\ViewProcessor\ScriptViewProcessor;
use PetrGrishin\View\ViewProcessor\StyleViewProcessor;
use PetrGrishin\Widget\Widget;

class ViewRenderer extends CApplicationComponent implements IViewRenderer {
    /** @var string */
    public $fileExtension = '.php';
    /** @var  array */
    private $viewProcessorsClasses;

    public static function className() {
        return get_called_class();
    }

    public function renderFile($context, $sourceFile, $params, $isReturn) {
        $templatePath = $this->resolveTemplatePath($sourceFile);
        $viewId = $this->generateViewId($templatePath);
        $view = new View($viewId, $context);
        $view->setTemplatePath($templatePath);
        $view->setParams($params);
        $isContextWidget = $this->isContextWidget($context) && $context->setView($view);
        $isAjaxRequest = !$isContextWidget && $this->isAjaxRequest();
        $responseParams = array();
        foreach ($this->getViewProcessorsClasses() as $viewProcessorClass) {
            /** @var \PetrGrishin\View\ViewProcessor\BaseViewProcessor $viewProcessor */
            $viewProcessor = new $viewProcessorClass($view);
            $viewProcessor->setIsAjaxMode($isAjaxRequest);
            $viewProcessor->processView();
            $responseParams = array_merge($responseParams, $viewProcessor->getParams());
        }
        if ($isReturn) {
             return $this->renderResponse($responseParams, $isAjaxRequest);
        }
        echo $this->renderResponse($responseParams, $isAjaxRequest);
    }

    protected function renderResponse(array $responseParams, $isAjaxRequest) {
        if (!$isAjaxRequest) {
            return $responseParams['content'];
        }
        // todo: load scriptsFiles
        $this->getClientScript()->reset();
        return json_encode($responseParams);
    }

//    public function renderAjax(View $view, $sourceFile) {
//
//        return json_encode(array(
//            'content' => $view->provideContext($sourceFile, true),
//            'name' => $view->getId(),
//            'params' => $view->getJsParams(),
//            'dependents' => $this->getScriptProcessor()->getDependents($view),
//            'styles' => $this->getStyleProcessor()->getDependents($view),
//        ));
//    }

    protected function resolveTemplatePath($sourceFile) {
        return substr($sourceFile, 0, - strlen($this->fileExtension));
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

    /**
     * @return array
     */
    public function getViewProcessorsClasses() {
        return $this->viewProcessorsClasses ?: array(
            HtmlViewProcessor::className(),
            ScriptViewProcessor::className(),
            StyleViewProcessor::className(),
        );
    }

    /**
     * @param array $viewProcessorsClasses
     * @return $this
     */
    public function setViewProcessorsClasses(array $viewProcessorsClasses) {
        $this->viewProcessorsClasses = $viewProcessorsClasses;
        return $this;
    }
}
