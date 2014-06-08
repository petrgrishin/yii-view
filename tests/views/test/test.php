<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

/** @var \PetrGrishin\View\View $this */

$this->widget(TestWidget::className(), 'test')->run();
$this->widget(TestWidget::className(), 'test2')->run();

printf("View template\n");