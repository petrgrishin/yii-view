<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/widget/TestWidget.php';
require_once __DIR__ . '/widget/InnerWidget.php';

class Yii extends YiiBase {
    /**
     * Автолоадер совместимый с composer
     * @author Petr Grishin <petr.grishin@grishini.ru>
     * @param string $alias
     * @param bool $forceInclude
     * @return mixed
     */
    public static function import($alias, $forceInclude = false) {
        $arguments = func_get_args();
        return @call_user_func_array(array(get_parent_class(), 'import'), $arguments) ? : $arguments[0];
    }
}

Yii::createWebApplication(array(
    'basePath' => './tests',
    'viewPath' => './tests/views',
    'aliases' => array(
        'webroot' => './tests',
    )
));