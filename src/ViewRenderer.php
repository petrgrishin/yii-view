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
    public $scriptProcessorClass;
    private $_scriptProcessor;

    public static function className() {
        return get_called_class();
    }

    public function renderFile($context, $sourceFile, $params, $isReturn) {
        $view = new View($context);
        if ($context instanceof Widget) {
            $context->setView($view);
        }
        $view->setParams($params);
        $view->setScriptFile($this->getScriptFile($sourceFile));
        $response = $view->render($sourceFile, $isReturn);
        $this->getScriptProcessor()->processView($view);
        return $response;
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
}
