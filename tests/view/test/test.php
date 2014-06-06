<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

/** @var \PetrGrishin\View\View $this */

$this->widget(TestWidget::className(), 'test');
$this->widget(TestWidget::className(), 'test2');

var_export($this->getParams());
var_export($this->getWidgets());