<?php

class WishlistClass extends ObjectModel {

    public $user_id;
    public $product_id;
    public $user_name;
    public $product_title;
    public $dt;
    public $product_link;
    public static $definition = array(
        'table' => 'wish_list',
        'primary' => 'id_list',
        'multilang' => false,
        'fields' => array(
            'user_id' => array('type' => self::TYPE_INT, 'required' => true),
            'product_id' => array('type' => self::TYPE_INT, 'required' => true),
            'user_name' => array('type' => self::TYPE_STRING, 'required' => true),
            'product_title' => array('type' => self::TYPE_STRING, 'required' => true),
            'dt' => array('type' => self::TYPE_DATE, 'required' => true),
            'product_link' => array('type' => self::TYPE_STRING, 'required' => true),
        )
    );
}
