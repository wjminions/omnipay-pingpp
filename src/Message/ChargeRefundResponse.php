<?php

namespace Omnipay\Pingpp\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Class ChargeResponse
 * @package Omnipay\Pingpp\Message
 */
class ChargeRefundResponse extends AbstractResponse
{

    public function isPaid()
    {
        return false;
    }


    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return false;
    }


    public function isRedirect()
    {
        return true;
    }


    public function getRedirectMethod()
    {
        return 'GET';
    }


    public function getRedirectUrl()
    {
        if (isset($this->data->failure_msg)) {
            $pattern = "/https.+/";

            preg_match($pattern, $this->data->failure_msg, $url);

            $url = $url[0];
        } else {
            $url = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
        }

        return $url;
    }


    public function getRedirectHtml()
    {

        if (isset($this->data->failure_msg)) {
            $pattern = "/https.+/";

            preg_match($pattern, $this->data->failure_msg, $url);

            $url = $url[0];
        } else {
            $url = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
        }

        $html = "<a href=" . $url . ">Click the jump to Alipay refund</a>";

        return $html;
    }


    public function getTransactionNo()
    {
        return $this->data->id;
    }


    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function getMessage()
    {
        return isset($this->data->failure_msg) ? $this->data->failure_msg : '';
    }
}
