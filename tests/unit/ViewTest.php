<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

class ViewTest extends PHPUnit_Framework_TestCase {
    const VIEW_RENDERER_NAME = 'viewRenderer';
    public function test() {
        $this->getViewRenderer();

        $controller = new TestController('test', $this->getApp());
        $this->getApp()->setController($controller);
        $controller->run('index');
    }

    protected function getViewRenderer() {
        if (!$this->getApp()->hasComponent(self::VIEW_RENDERER_NAME)) {
            $this->getApp()->setComponent(self::VIEW_RENDERER_NAME, array(
                'class' => \PetrGrishin\View\ViewRenderer::className(),
            ));
        }
        return $this->getApp()->getComponent(self::VIEW_RENDERER_NAME);
    }

    /**
     * @return CWebApplication
     */
    protected function getApp() {
        return Yii::app();
    }
}

class TestController extends CController {
    public function actionIndex() {
        $this->render('test', array('params' => 1));
    }
}
 