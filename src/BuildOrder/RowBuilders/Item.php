<?php
// Item class is not included in Svea namespace, is wrapper for WebPayItem

include_once SVEA_REQUEST_DIR . "/Includes.php";

/**
 * Wraps class WebPayItem, while providing backwards compatibility.
 * 
 * @author Kristian Grossman-Madsen, anne-hal
 * @deprecated 2.0.0 Please use class WebPayItem instead.
 */
class Item {

    /**
     * @deprecated 2.0.0 Please use class WebPayItem instead.
     */
     public static function orderRow() {
         return WebPayItem::orderRow();
    }
    /**
     * @deprecated 2.0.0 Please use class WebPayItem instead.
     */
    public static function shippingFee() {
        return WebPayItem::shippingFee();
    }
    /**
     * @deprecated 2.0.0 Please use class WebPayItem instead.
     */
    public static function invoiceFee() {
        return WebPayItem::invoiceFee();
    }
    /**
     * @deprecated 2.0.0 Please use class WebPayItem instead.
     */
    public static function fixedDiscount() {
        return WebPayItem::fixedDiscount();
    }
    /**
     * @deprecated 2.0.0 Please use class WebPayItem instead.
     */
    public static function relativeDiscount() {
        return WebPayItem::relativeDiscount();
    }
    /**
     * @deprecated 2.0.0 Please use class WebPayItem instead.
     */
    public static function individualCustomer() {
        return WebPayItem::individualCustomer();
    }
    /**
     * @deprecated 2.0.0 Please use class WebPayItem instead.
     */
    public static function companyCustomer() {
        return WebPayItem::companyCustomer();
    }
}
