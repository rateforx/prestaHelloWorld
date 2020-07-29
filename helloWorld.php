<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if ( !defined( '_PS_VERSION_' ) ) {
    exit;
}

class HelloWorld extends Module
{
    protected $config_form = false;

    private $configKeys = [
        'HELLOWORLD_TITLE',
        'HELLOWORLD_DESCRIPTION',
        'HELLOWORLD_URL',
    ];

    public function __construct ()
    {
        $this->name = 'helloWorld';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'rateforx';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l( 'Hello World' );
        $this->description = $this->l( 'Hello World Hello World ' );

        $this->ps_versions_compliancy = [ 'min' => '1.7', 'max' => _PS_VERSION_ ];
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install ()
    {
        Configuration::updateValue( 'HELLOWORLD_TITLE', [
            Language::getIdByIso( 'pl' ) => 'Cześć, X13!',
            Language::getIdByIso( 'en' ) => 'Hello, X13!',
        ] );
        Configuration::updateValue( 'HELLOWORLD_DESCRIPTION', '' );
        Configuration::updateValue( 'HELLOWORLD_URL', [
            Language::getIdByIso( 'pl' ) => 'cześć',
            Language::getIdByIso( 'en' ) => 'hello-world',
        ] );

        return parent::install() &&
            $this->registerHook( 'header' ) &&
            $this->registerHook( 'backOfficeHeader' ) &&
            $this->registerHook( 'moduleRoutes' );
    }

    public function uninstall ()
    {
        foreach ( $this->configKeys as $key ) {
            Configuration::deleteByName( $key );
        }
        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent ()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if ( ( (bool)Tools::isSubmit( 'submitHelloWorldModule' ) ) == true ) {
            $this->postProcess();
        }

        $this->context->smarty->assign( 'module_dir', $this->_path );

        $output = $this->context->smarty->fetch( $this->local_path . 'views/templates/admin/configure.tpl' );

        return $output . $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm ()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get( 'PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0 );

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitHelloWorldModule';
        $helper->currentIndex = $this->context->link->getAdminLink( 'AdminModules', false )
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite( 'AdminModules' );

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages'    => $this->context->controller->getLanguages(),
            'id_language'  => $this->context->language->id,
        );

        return $helper->generateForm( array( $this->getConfigForm() ) );
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm ()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l( 'Settings' ),
                    'icon'  => 'icon-cogs',
                ],
                'input'  => [
                    [
                        'type'     => 'text',
                        'name'     => 'HELLOWORLD_TITLE',
                        'label'    => $this->l( 'Title' ),
                        'lang'     => true,
                        'required' => true,
                    ],
                    [
                        'type'  => 'text',
                        'name'  => 'HELLOWORLD_DESCRIPTION',
                        'label' => $this->l( 'Description' ),
                        'lang'  => true,
                    ],
                    [
                        'type'     => 'text',
                        'name'     => 'HELLOWORLD_URL',
                        'label'    => $this->l( 'URL' ),
                        'lang'     => true,
                        'required' => true,
                    ],
                ],
                'submit' => [
                    'title' => $this->l( 'Save' ),
                ],
            ],
        ];
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues ()
    {
        $values = [];
        $languages = Language::getLanguages( false );

        foreach ( $this->configKeys as $key ) {
            $values[ $key ] = [];
            foreach ( $languages as $language ) {
                $values[ $key ][ $language[ 'id_lang' ] ] = Configuration::get( $key, $language[ 'id_lang' ] );
            }
        }

        return $values;
    }

    /**
     * Save form data.
     */
    protected function postProcess ()
    {
        $languages = Language::getLanguages( false );

        foreach ( $this->configKeys as $key ) {
            $values = [];
            foreach ( $languages as $language ) {
                $values[ $language[ 'id_lang' ] ] = Tools::getValue( $key . '_' . $language[ 'id_lang' ] );
            }
            Configuration::updateValue( $key, $values );
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader ()
    {
        if ( Tools::getValue( 'module_name' ) == $this->name ) {
            $this->context->controller->addJS( $this->_path . 'views/js/back.js' );
            $this->context->controller->addCSS( $this->_path . 'views/css/back.css' );
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader ()
    {
        $this->context->controller->addJS( $this->_path . '/views/js/front.js' );
        $this->context->controller->addCSS( $this->_path . '/views/css/front.css' );
    }

    public function hookModuleRoutes ( $params )
    {
        $url = Configuration::get( 'HELLOWORLD_URL', $this->context->language->id );

        return [
            'module-helloWorld-route' => [
                'controller' => 'route',
                'rule'       => $url,
                'params'     => [
                    'fc'     => 'module',
                    'module' => $this->name,
                ],
                'keywords' => [],
            ]
        ];
    }
}
