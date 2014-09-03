<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

namespace PetrGrishin\View\ViewProcessor;


use PetrGrishin\View\View;

class HtmlViewProcessor extends BaseViewProcessor {
    const EXTENSION_TEMPLATE = '.php';

    public function processView() {
        $templateFile = sprintf('%s%s', $this->view->getTemplatePath(), self::EXTENSION_TEMPLATE);
        $content = $this->view->provideContext($templateFile);
        $this->setParams(array(
            'content' => $content,
        ));
        return $this;
    }
}
 