<?php

class WishlistShowModuleFrontController extends ModuleFrontController {

    public function initContent() {
        parent::initContent();
        $table = $this->getFullDataTable();
        /*echo '<pre>';
        print_r($table);
        echo '</pre>';*/
        if (Tools::getIsset('sort')) {
            
            switch (Tools::getValue('sort')) {
                case 'title':
                    $table = $this->getFullDataTable('product_title');

                    break;
                case 'lowest':
                    $table = $this->getFullDataTable('price');

                    break;
                case 'highest':
                    $table = $this->getFullDataTable('price DESC');

                    break;
                case 'relevance':
                    $table = $this->getFullDataTable();

                    break;
            }
        }

        $this->context->smarty->assign(
            array(
                'wishList' => $table,
                'currentCartId' => $this->context->cart->id,
                'urlImg' => _PS_BASE_URL_.__PS_BASE_URI__,
                'urlFrontController' => Configuration::get('urlFrontController')
            ));
        return $this->setTemplate('module:'.$this->module->name.'/views/templates/front/show.tpl');
    }

    function getFullDataTable($orderBy='dt') {        
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('wish_list', 'wl');
        $sql->innerJoin('product', 'p', 'wl.product_id = p.id_product');
        $sql->leftOuterJoin('image', 'i', 'wl.product_id = i.id_product');
        $sql->innerJoin('product_lang', 'pl', 'wl.product_id = pl.id_product');
        $sql->leftOuterJoin('cart_product','cp', 'wl.product_id = cp.id_product AND cp.id_cart = '.$this->context->cart->id);
        //$sql->innerJoin('category_product', 'ctp', 'wl.product_id = ctp.id_product');
        $sql->orderBy($orderBy);
        $sql->where('id_lang = 1 AND position = 1');
        //$sql->where('id_lang = 1 AND position = 1');
        return Db::getInstance()->executeS($sql);
    }

    public function postProcess() {
        if (Tools::getIsset('id')) {
            $idArray = explode(',', Tools::getValue('id'));
            foreach ($idArray as $id) {
                Db::getInstance()->delete('wish_list', 'product_id = '.$id , 1);
            }
            //Tools::redirect('/prestashop/ru/cart?action=show');
        }
        if (Tools::getIsset('ids-to-cart')) {
            if (!empty(Tools::getValue('ids-to-cart'))) {
                $idArray = explode(',', Tools::getValue('ids-to-cart'));
                foreach ($idArray as $id_product) {
                    $cart=new Cart($this->context->cart->id);
                    $cart->id_currency=1;
                    $cart->id_lang=1;
                    //$cart>updateQty(1, $id_product, 0, null, 'up', 0, null, false);
                    $cart->updateQty('1', $id_product, 0, null,'up',null, null);
                }
                if (Tools::getIsset('group-add-to-cart')) {
                    Tools::redirect(_PS_BASE_URL_.__PS_BASE_URI__.'cart?action=show');
                }
                header('Location: '.Configuration::get('urlFrontController'));
            }
        }

        parent::postProcess();
    }

}

