<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {}
    public function headerAction()
    {
        $this->_helper->layout->disableLayout();
    }
    public function menuAction()
    {
        $this->_helper->layout->disableLayout();
    }
    public function footerAction()
    {
        $this->_helper->layout->disableLayout();
    }
}

