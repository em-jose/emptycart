<?php
/**
 * 2022
 *
 * This file is part of EmptyCart for prestashop.
 *
 * EmptyCart for prestashop is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EmptyCart for prestashop is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EmptyCart for prestashop.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Jose María Escribano
 * @copyright 2022 Jose María Escribano
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Emptycart extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'emptycart';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Jose María Escribano';
        $this->need_instance = 0;
        $this->controllers = array('cleancart');
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Empty Cart');
        $this->description = $this->l('This module adds a button to remove all the cart products.');

        $this->confirmUninstall = $this->l('');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        Configuration::updateValue('EMPTYCART_SHOW_MODAL', false);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayShoppingCartFooter');
    }

    public function uninstall()
    {
        Configuration::deleteByName('EMPTYCART_SHOW_MODAL');

        return parent::uninstall();
    }

    public function getContent()
    {
        if (((bool)Tools::isSubmit('submitEmptycartModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        return $this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitEmptycartModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show confirmation modal'),
                        'name' => 'EMPTYCART_SHOW_MODAL',
                        'is_bool' => true,
                        'desc' => $this->l('Show warning modal before delete cart products'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('HTML reference'),
                        'name' => 'EMPTYCART_HTML_SELECTOR',
                        'desc' => $this->l('Use an HTML selector to change the button\'s position.
                            The button will be moved after the selected element.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('HTML classes'),
                        'name' => 'EMPTYCART_HTML_CLASSES',
                        'desc' => $this->l('You could add HTML classes to change the button styles.'),
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    protected function getConfigFormValues()
    {
        return array(
            'EMPTYCART_SHOW_MODAL' => Configuration::get('EMPTYCART_SHOW_MODAL', true),
            'EMPTYCART_HTML_SELECTOR' => Configuration::get('EMPTYCART_HTML_SELECTOR', ''),
            'EMPTYCART_HTML_CLASSES' => Configuration::get('EMPTYCART_HTML_CLASSES', ''),
        );
    }

    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    public function hookHeader()
    {
        $page = $this->context->controller->php_self;
        $html_selector = Configuration::get('EMPTYCART_HTML_SELECTOR');
        
        if (!empty($html_selector) && $page == 'cart') {
            Media::addJsDef([
                'rcp_html_selector' => $html_selector
            ]);
            
            $this->context->controller->registerJavascript(
                'front-js',
                'modules/' . $this->name . '/views/js/front.js'
            );
        }
    }

    public function hookDisplayShoppingCartFooter()
    {
        $cart_products = $this->context->cart->getProducts();

        if (empty($cart_products)) {
            return false;
        }

        $cleancart_url = $this->context->link->getModuleLink('emptycart', 'cleancart');
        $show_modal = Configuration::get('EMPTYCART_SHOW_MODAL');
        $html_classes = Configuration::get('EMPTYCART_HTML_CLASSES');
        $html_selector = Configuration::get('EMPTYCART_HTML_SELECTOR');

        $this->smarty->assign(array(
            'cleancart_url' => $cleancart_url,
            'show_modal' => $show_modal,
            'html_classes' => $html_classes,
            'html_selector' => $html_selector
        ));

        return $this->display(__FILE__, 'views/templates/hook/displayShoopingCartFooter.tpl');
    }
}
