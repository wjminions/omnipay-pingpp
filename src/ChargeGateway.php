<?php

namespace Omnipay\Pingpp;

use Omnipay\Common\AbstractGateway;

/**
 * Class ChargeGateway
 * @package Omnipay\Pingpp
 */
class ChargeGateway extends AbstractGateway
{

    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name for each gateway.
     */
    public function getName()
    {
        return 'Pingpp_Charge';
    }


    public function setOrderNo($value)
    {
        return $this->setParameter('order_no', $value);
    }


    public function getOrderNo()
    {
        return $this->getParameter('order_no');
    }


    public function setAmount($value)
    {
        return $this->setParameter('amount', $value);
    }


    public function getAmount()
    {
        return $this->getParameter('amount');
    }


    public function setSubject($value)
    {
        return $this->setParameter('subject', $value);
    }


    public function getSubject()
    {
        return $this->getParameter('subject');
    }


    public function setBody($value)
    {
        return $this->setParameter('body', $value);
    }


    public function getBody()
    {
        return $this->getParameter('body');
    }


    public function setApp($value)
    {
        return $this->setParameter('app', $value);
    }


    public function getApp()
    {
        return $this->getParameter('app');
    }


    public function setChannel($value)
    {
        return $this->setParameter('channel', $value);
    }


    public function getChannel()
    {
        return $this->getParameter('channel');
    }


    public function setCallback($value)
    {
        return $this->setParameter('callback', $value);
    }


    public function getCallback()
    {
        return $this->getParameter('callback');
    }


    public function setAppKey($value)
    {
        return $this->setParameter('app_key', $value);
    }


    public function getAppKey()
    {
        return $this->getParameter('app_key');
    }


    public function setPrivateKeyPath($value)
    {
        return $this->setParameter('private_key_path', $value);
    }


    public function getPrivateKeyPath()
    {
        return $this->getParameter('private_key_path');
    }


    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Pingpp\Message\ChargePurchaseRequest', $parameters);
    }


    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Pingpp\Message\ChargeCompletePurchaseRequest', $parameters);
    }


    public function query(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Pingpp\Message\ChargeQueryRequest', $parameters);
    }


    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Pingpp\Message\ChargeRefundRequest', $parameters);
    }
}
