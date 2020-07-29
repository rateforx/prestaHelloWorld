<?php


class HelloWorldRouteModuleFrontController extends ModuleFrontController
{
    public function initContent ()
    {
        parent::initContent();

        $this->context->smarty->assign( [
            'title'       => Configuration::get(
                'HELLOWORLD_TITLE',
                $this->context->language->id
            ),
            'description' => Configuration::get(
                'HELLOWORLD_DESCRIPTION',
                $this->context->language->id
            ),
        ] );

        $this->setTemplate( 'module:helloWorld/views/templates/front/helloWorld.tpl' );
    }
}