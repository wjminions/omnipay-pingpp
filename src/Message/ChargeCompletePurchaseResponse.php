<?php

namespace Omnipay\Pingpp\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Class ChargeCompletePurchaseResponse
 * @package Omnipay\Pingpp\Message
 */
class ChargeCompletePurchaseResponse extends AbstractResponse
{

    public function isPaid()
    {
        return $this->data['is_paid'];
    }


    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return $this->data['verify_success'];
    }
}
