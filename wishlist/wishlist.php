<?php

if (!defined('_PS_VERSION_'))
    return false;

require_once(_PS_MODULE_DIR_ . "wishlist/wishlistClass.php");

class Wishlist extends Module implements \PrestaShop\PrestaShop\Core\Module\WidgetInterface {
    private $templateFile;

    public function __construct()  {

        $this->name = 'wishlist';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Sviatoslav Dykyi';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Wish list Module', array(), 'Modules.CommentProduct.Admin');
        $this->description = $this->trans('This module is created for managing wish list products', array(), 'Modules.CommentProduct.Admin');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->templateFile = 'module:wishlist/views/templates/hook/WishList.tpl';

    }
    public function install() {        

        if (!parent::install() ||
            !$this->registerHook('displayProductListReviews') ||
            !$this->registerHook('displayProductActions') ||
            !$this->registerHook('displayHeader') ||
            !$this->registerHook('displayNav2') ||            
            !$this->createDatabaseTable() ||
            !$this->installTab('AdminCatalog', 'AdminWishlist', 'Customer\'s wish list') ||
            !Configuration::updateValue('urlFrontController', _PS_BASE_URL_.__PS_BASE_URI__.'index.php?fc=module&module=wishlist&controller=show')
        ) {
            return false;
        }
        return true;
    }

    public function createDatabaseTable() {
        return Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' ._DB_PREFIX_.'wish_list` (
                `id_wish_list` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` int(10) NOT NULL,
                `product_id` int(10) NOT NULL, 
                `user_name` varchar(64) NOT NULL,  
                `product_title` varchar(64) NOT NULL,
                `product_link` varchar(255) NOT NULL,
                `dt` TIMESTAMP,                                       
                PRIMARY KEY (`id_wish_list`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');
    }

    /**
     * create a new tab for AdminController
     * @param $parent - батьківський таб, н-д Modules , Design, Shopping
     * @param $class_name - вазкує на наш клас AdminMymoduleController
     * @param $name - дитячий таб, н-д Module Manager / Module Catalog
     */
    public function installTab($parent, $class_name, $name) {
        $tab = new Tab();
        $tab->id_parent = (int)Tab::getIdFromClassName($parent);
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab->name[$lang['id_lang']] = $name;
        $tab->class_name = $class_name;
        $tab->module = $this->name;
        $tab->active = 1;
        return $tab->add();
    }

    public function uninstallTab($class_name) {
        // Retrieve Tab Id
        $id_tab = (int)Tab::getIdFromClassName($class_name);
        // Load tab
        $tab = new Tab((int)$id_tab);
        // Delete Tab
        return $tab->delete();
    }

    public function uninstall() {
        if (!parent::uninstall() ||
            !$this->uninstallTab('AdminWishlist') ||
            !Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'wish_list`') ||
            !Configuration::deleteByName('wish-list-user') ||
            !Configuration::deleteByName('urlFrontController')
        ) {
            return false;
        }
        return true;
    }

    public function renderWidget($hookName, array $configuration) {
        $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        return $this->fetch($this->templateFile);
    }


    public function getWidgetVariables($hookName, array $configuration) {
        $inList = false;
        $id = Tools::getValue('id_product');
        $sql = 'SELECT * FROM '._DB_PREFIX_.'wish_list WHERE product_id = '.(int)Tools::getValue('id_product').' AND user_id = '.Configuration::get('wish-list-user');
        $inList = count(Db::getInstance()->executeS($sql)) > 0 ? true : false;

        return array(
            'inList' => $inList,
            'controller' => Tools::getValue('controller'),
            'urlFrontController' => Configuration::get('urlFrontController'),
        );
    }

    public function addToWishList() {
        $id = Tools::getValue('id_product');
        $customerId = Configuration::get('wish-list-user');
        if (Tools::isSubmit('name')) {
            if (Tools::isSubmit('id-to-c')) {
                $id = Tools::getValue('id-to-c');
            }
            if (!in_array($id, $this->getWishListIdArray())) {
                $data = explode('|', Tools::getValue('name'));
                $wishList = new WishlistClass();
                $wishList->user_id = $customerId;
                $customerFirstName = $this->context->customer->firstname ?: 'unknown';
                $customerLastName = $this->context->customer->lastname ?: '';
                $wishList->user_name = $customerFirstName.' '.$customerLastName;
                $wishList->product_id = $id;
                $wishList->dt = date("Y-m-d H:i:s");
                $wishList->product_title = $data[0];
                $wishList->product_link = $data[1];
                $wishList->save();
            }
        }
    }

    public function hookDisplayNav2() {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'wish_list WHERE user_id = '.Configuration::get('wish-list-user');
        $wishList = Db::getInstance()->executeS($sql);
        $wishListIds = '';
        foreach ($wishList as $id) {
            $wishListIds .= $id['product_id'].',';
        }

        $this->context->smarty->assign(array(
            'listProductsCount' => count($wishList),
            'wishListIds' => $wishListIds,
            'controller' => Tools::getValue('controller'),
            'urlFrontController' => Configuration::get('urlFrontController')
        ));
        return $this->display(__FILE__, 'views/templates/hook/WishListNav.tpl');
    }

    public function getWishListIdArray() {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'wish_list WHERE user_id = '.Configuration::get('wish-list-user');
        $rows = Db::getInstance()->executeS($sql);
        $wishListIds = [];
        foreach ($rows as $row) {
            $wishListIds[] = $row['product_id'];
        }
        return $wishListIds;
    }

    public function hookDisplayHeader() {

       if (!$this->context->cart->id) {
           $cart = new Cart();
           $cart->id_customer = (int)($this->context->cookie->id_customer);
           $cart->id_address_delivery = (int)  (Address::getFirstCustomerAddressId($cart->id_customer));
           $cart->id_address_invoice = $cart->id_address_delivery;
           $cart->id_lang = (int)($this->context->cookie->id_lang);
           $cart->id_currency = (int)($this->context->cookie->id_currency);
           $cart->id_carrier = 1;
           $cart->recyclable = 0;
           $cart->gift = 0;
           $cart->add();
           $this->context->cookie->id_cart = (int)($cart->id);
           $cart->update();
       }
        $user = $this->context->customer->id ?: (int)$this->context->cookie->id_cart;
        Configuration::updateValue('wish-list-user', $user);    

        $this->addToWishList();
        $this->context->controller->addCSS($this->_path.'views/css/wishlist.css');
        $this->context->controller->addJS($this->_path.'views/js/wishlist.js');
    }

    public function hookDisplayProductListReviews() {
        $this->context->smarty->assign(array(
            'controller' => Tools::getValue('controller'),
            'urlFrontController' => Configuration::get('urlFrontController')
        ));

        return $this->display(__FILE__, 'views/templates/hook/WishListMini.tpl');
    }

}
