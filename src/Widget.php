<?php
/**
 * @author Petr Grishin <petr.grishin@grishini.ru>
 */

namespace PetrGrishin\View;


class Widget extends \CWidget {
    private $view;
    private $name;

    /**
     * @return mixed
     */
    public function getView() {
        return $this->view;
    }

    /**
     * @param mixed $view
     * @return $this
     */
    public function setView($view) {
        $this->view = $view;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return $this
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
}
 