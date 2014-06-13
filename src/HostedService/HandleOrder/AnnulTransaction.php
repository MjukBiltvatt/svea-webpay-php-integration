<?php
namespace Svea\HostedService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Annul an existing Card transaction.
 * The transaction must have Svea status AUTHORIZED or CONFIRMED. After a 
 * successful request the transaction will get the status ANNULLED.
 * 
 * Note that this only supports Card transactions.
 * 
 * @param ConfigurationProvider $config instance implementing ConfigurationProvider
 * 
 * @author Kristian Grossman-Madsen
 */
class AnnulTransaction extends HostedRequest {

    protected $transactionId;
    
    /**
     * @param ConfigurationProvider $config instance implementing ConfigurationProvider
     */
    function __construct($config) {
        $this->method = "annul";
        parent::__construct($config);
    }

    /**
     * @param string $transactionId
     * @return $this
     */
    function setTransactionId( $transactionId ) {
        $this->transactionId = $transactionId;
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
        
        // message contains the credit request
        $messageContents = array(
            "transactionid" => $this->transactionId
        ); 
        $message = $xmlBuilder->getAnnulTransactionXML( $messageContents );        
        
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
        $errors = $this->validateTransactionId($self, $errors);
        return $errors;
    }
    
    private function validateTransactionId($self, $errors) {
        if (isset($self->transactionId) == FALSE) {                                                        
            $errors['missing value'] = "transactionId is required. Use function setTransactionId() with the SveaOrderId from the createOrder response.";  
        }
        return $errors;
    }       
}