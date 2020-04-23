<?php


class AdminWishlistController extends ModuleAdminController {
    public function __construct() {
        $this->table = 'wish_list';
        $this->className = 'Wishlist';
        $this->fields_list = array(
            'user_name' => array('title' => 'User'),
            'product_title' => array('title' => 'Product title')
        );
        $this->bootstrap = true;

        parent::__construct();
    }


    public function postProcess() {
        return parent::postProcess(); // TODO: Change the autogenerated stub
    }

}