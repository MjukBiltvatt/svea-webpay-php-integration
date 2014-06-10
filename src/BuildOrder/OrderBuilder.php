<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * OrderBuilder collects and prepares order data to be sent to Svea. It is the
 * parent of CreateOrderBuilder and DeliverOrderBuilder.
 * 
 * Set all required order attributes in CreateOrderBuilder instance by using the 
 * instance setAttribute() methods. Instance methods can be chained together, as 
 * they return the instance itself in a fluent fashion.
 * 
 * @author Kristian Grossman-Madsen, Anneli Halld'n, Daniel Brolund for Svea WebPay
 */
class OrderBuilder {

    /** @var boolean  true indicates test mode, false indicates production mode */
    public $testmode = false;
    
    /** @var \ConfigurationProvider $conf */
    public $conf;
    
    /** @var \Svea\IndividualCustomer|\Svea\CompanyCustomer */
    public $customerIdentity;
    
    /** @var \Svea\OrderRow []  array of OrderRow */
    public $orderRows = array();
    
    /** @var \Svea\ShippingFee []  array of ShippingFee */
    public $shippingFeeRows = array();
    
    /** @var \Svea\InvoiceFee []  array of InvoiceFee */
    public $invoiceFeeRows = array();
    
    /** @var \Svea\FixedDiscount []  array of FixedDiscount*/
    public $fixedDiscountRows = array();

    /** @var \Svea\RelativeDiscount []  array of RelativeDiscount */
    public $relativeDiscountRows = array(); 
    
    /** @var string Country code as described by ISO 3166-1: "SE", "NO", "DK", "FI","DE", "NL" */
    public $countryCode;
    
    /** @var string Currency in ISO 4217 three-letter format, ex. "SEK", "EUR" */
    public $currency;

    /** @var string ISO 8601 date, as produced by php date('c'): "2004-02-12T15:19:21+00:00", also accepts dates like "2004-02-12" */
    public $orderDate;

    /** @var string your customer Reference number */
    public $customerReference;
    
    /** @var string order number given by client side, should uniquely identify order at client */
    public $clientOrderNumber;
    
    /**  
     * @param \ConfigurationProvider $config 
     */
    public function __construct($config) {
        $this->conf = $config;
    }
    
    /**
     * Required. Add customer information to the order. 
     * 
     * @param \Svea\IndividualCustomer|\Svea\CompanyCustomer $itemCustomerObject
     * @return $this
     */
     public function addCustomerDetails($itemCustomerObject) {
        $this->customerIdentity = $itemCustomerObject;
        return $this;
    }

    /**
     * @param \Svea\OrderRow $itemOrderRowObject
     * @return $this
     */
    public function addOrderRow($itemOrderRowObject) {
        if (is_array($itemOrderRowObject)) {
            foreach ($itemOrderRowObject as $row) {
                array_push($this->orderRows, $row);
            }
        } else {
             array_push($this->orderRows, $itemOrderRowObject);
        }
       return $this;
    }
  
    /**
     * Adds a shipping fee or invoice fee to the order
     * 
     * @param \Svea\InvoiceFee|\Svea\ShippingFee $itemFeeObject
     * @return $this
     */
    public function addFee($itemFeeObject) {
         if (is_array($itemFeeObject)) {
            foreach ($itemFeeObject as $row) {
                if (get_class($row) == "Svea\ShippingFee") {
                     array_push($this->shippingFeeRows, $row);
                }
                if (get_class($row) == "Svea\InvoiceFee") {
                     array_push($this->invoiceFeeRows, $row);
                }
            }
        } else {
             if (get_class($itemFeeObject) == "Svea\ShippingFee") {
                     array_push($this->shippingFeeRows, $itemFeeObject);
            }
             if (get_class($itemFeeObject) == "Svea\InvoiceFee") {
                 array_push($this->invoiceFeeRows, $itemFeeObject);
            }
        }
        return $this;
    }

    /**
     * Adds a fixed amount discount or an order total percent discount to the order
     * 
     * @param \Svea\FixedDiscount|\Svea\RelativeDiscount $itemDiscountObject
     * @return $this
     */
    public function addDiscount($itemDiscountObject) {
        if (is_array($itemDiscountObject)) {
            foreach ($itemDiscountObject as $row) {
                if (get_class($row) == "Svea\FixedDiscount") {
                    array_push($this->fixedDiscountRows, $row);
                }
                if (get_class($row) == "Svea\RelativeDiscount") {
                    array_push($this->relativeDiscountRows, $row);
                }
            }
        }
        else {
            if (get_class($itemDiscountObject) == "Svea\FixedDiscount") {
                array_push($this->fixedDiscountRows, $itemDiscountObject);
            }
            if (get_class($itemDiscountObject) == "Svea\RelativeDiscount") {
                array_push($this->relativeDiscountRows, $itemDiscountObject);
            }
       }
       return $this;
    }
  
    /**
     * @param string $countryCodeAsString Country code as described by ISO 3166-1: "SE", "NO", "DK", "FI", "DE", "NL"
     * @return $this
     */
    public function setCountryCode($countryCodeAsString) {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }

    /**
     * @param string $currencyAsString in ISO 4217 three-letter format, ex. "SEK", "EUR"
     * @return $this
     */
    public function setCurrency($currencyAsString) {
        $currency = strtoupper( trim($currencyAsString) );
        $this->currency = $currency;
        return $this;
    }

    /**
     * Client customer reference
     * 
     * @param string  $customerReferenceAsString needs to be unique to the order for card and direct bank orders
     * @return $this
     */
    public function setCustomerReference($customerReferenceAsString) {
        $this->customerReference = $customerReferenceAsString;
        return $this;
    }

    /**
     * Client order number
     * 
     * @param string  $clientOrderNumberAsString
     * @return $this
     */
    public function setClientOrderNumber($clientOrderNumberAsString) {
        $this->clientOrderNumber = $clientOrderNumberAsString;
        return $this;
    }

    /**
     * @param string $orderDateAsString  ISO 8601 date, as produced by php date('c'): "2004-02-12T15:19:21+00:00", also accepts dates like "2004-02-12"
     * @return $this
     */
    public function setOrderDate($orderDateAsString) {
        $this->orderDate = $orderDateAsString;
        return $this;
    }

   /**
     * @internal for testfunctions
     * @param type $func
     * @return $this
     */
    public function run($func) {
        $func($this);
        return $this;
    }
}
