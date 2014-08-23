<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

namespace PetrGrishin\View;


class ViewStyleProcessor extends \CApplicationComponent {
    const FILENAME_STYLE = 'style.css';

    private $publicPath;
    private $assertPath;

    public static function className() {
        return get_called_class();
    }

    public function processView(View $view, $ajax = false) {
        $isAppend = $this->appendStyleFile($view->getId(), $view->getStylePath());
        return $this;
    }

    public function getAssertPath() {
        return $this->assertPath ?: 'assets/styles';
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

    protected function generateAbsoluteAssertPath($id) {
        $path = sprintf('%s/%s/%s', $this->getPublicPath(), $this->getAssertPath(), $id);
        if (false === is_dir($path) && false === mkdir($path, 0777, true)) {
            throw new \Exception(sprintf('Do not create directory `%s`', $path));
        }
        if (false === is_dir($path) || false === is_writable($path)) {
            throw new \Exception(sprintf('No write access to directory `%s`', $path));
        }
        return $path;
    }

    protected function generateAssertPath($id) {
        return sprintf('/%s/%s', $this->getAssertPath(), $id);
    }

    protected function appendStyleFile($id, $stylePath) {
        if (!is_dir($stylePath)) {
            return false;
        }
        $assetPath = $this->getAssetManager()->publish($stylePath);
        $script = sprintf("App.registerStyleFile('%s/%s');", $assetPath, self::FILENAME_STYLE);
        $this->getClientScript()->registerScript($id . '_style', $script, \CClientScript::POS_END);
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
 