<?php

namespace Omnipay\Pingpp\Message;

use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Pingpp\Helper;

/**
 * Class AbstractChargeRequest
 * @package Omnipay\Pingpp\Message
 */
abstract class AbstractChargeRequest extends AbstractRequest
{
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


    public function setPublicKeyPath($value)
    {
        return $this->setParameter('public_key_path', $value);
    }


    public function getPublicKeyPath()
    {
        return $this->getParameter('public_key_path');
    }


    public function setCurrency($value)
    {
        return $this->setParameter('currency', $value);
    }


    public function getCurrency()
    {
        return $this->getParameter('currency');
    }


    public function setChId($value)
    {
        return $this->setParameter('ch_id', $value);
    }


    public function getChId()
    {
        return $this->getParameter('ch_id');
    }
}
