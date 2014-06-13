<?php
namespace Svea\HostedService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Recur a Card transaction. 
 * 
 * @author Kristian Grossman-Madsen
 */
class RecurTransaction extends HostedRequest {

    protected $subscriptionId;
    protected $amountToLower;
    
    function __construct($config) {
        $this->method = "recur";
        parent::__construct($config);
    }
    
    /**
     * If recur is to an international acquirer the currency for the recurring transaction must be the same as for the registration transaction.
     * 
     * Optional.
     * 
     * @param string $currency
     * @return \Svea\RecurTransaction
     */
    function setCurrency( $currency ) {
        $this->currency = $currency;
        return $this;
    }
    
    /**
     * Note that if subscriptiontype is either RECURRING or RECURRINGCAPTURE, 
     * the amount must be given in the same currency as the initial transaction. 
     * 
     * Required.
     * 
     * @param int $amount  amount in minor currency
     * @return \Svea\RecurTransaction
     */
    function setAmount( $amount ) {
        $this->amount = $amount;
        return $this;
    }

    /**
     * The new unique customer reference number.
     * 
     * Required.
     * 
     * @param string $customerRefNo
     * @return \Svea\RecurTransaction
     */
    function setCustomerRefNo( $customerRefNo ) {
        $this->customerRefNo = $customerRefNo;
        return $this;
    }
    
    /**
     * The subscription id returned with the inital transaction response.
     *
     * Required.
     *  
     * @param int $subscriptionId
     * @return \Svea\RecurTransaction
     */
    function setSubscriptionId( $subscriptionId ) {
        $this->subscriptionId = $subscriptionId;
        return $this;
    }
    
    /**
     * prepares the elements used in the request to svea
     */
    public function prepareRequest() {
        $this->validateRequest();

        $xmlBuilder = new HostedXmlBuilder();
        
        // get our merchantid & secret
        $merchantId = $this->config->getMerchantId( \ConfigurationProvider::HOSTED_TYPE,  $this->countryCode);
        $secret = $this->config->getSecret( \ConfigurationProvider::HOSTED_TYPE, $this->countryCode);
        
        // message contains the confirm request
        $messageContents = array(
            "amount" => $this->amount,
            "customerrefno" => $this->customerRefNo,
            "subscriptionid" => $this->subscriptionId
        ); 
        if( isset( $this->currency ) ) { $messageContents["currency"] = $this->currency; }

        $message = $xmlBuilder->getRecurTransactionXML( $messageContents );

        // calculate mac
        $mac = hash("sha512", base64_encode($message) . $secret);
        
        // encode the request elements
        $request_fields = array( 
            'merchantid' => urlencode($merchantId),
            'message' => urlencode(base64_encode($message)),
            'mac' => urlencode($mac)
        );
        return $request_fields;
    }

    public function validate($self) {
        $errors = array();
        $errors = $this->validateAmount($self, $errors);
        $errors = $this->validateCustomerRefNo($self, $errors);
        $errors = $this->validateSubscriptionId($self, $errors);
        return $errors;
    }
    
    private function validateAmount($self, $errors) {
        if (isset($self->amount) == FALSE) {                                                        
            $errors['missing value'] = "amount is required. Use function setAmount().";
        }
        return $errors;
    }  
    
    private function validateCustomerRefNo($self, $errors) {
        if (isset($self->customerRefNo) == FALSE) {                                                        
            $errors['missing value'] = "customerRefNo is required. Use function setCustomerRefNo().";
        }
        return $errors;
    }  
    
    private function validateSubscriptionId($self, $errors) {
        if (isset($self->subscriptionId) == FALSE) {                                                        
            $errors['missing value'] = "subscriptionId is required. Use function setSubscriptionId() with the subscriptionId from the createOrder response.";
        }
        return $errors;
    }    
}