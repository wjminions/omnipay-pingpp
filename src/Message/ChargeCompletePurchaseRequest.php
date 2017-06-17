<?php

namespace Omnipay\Pingpp\Message;

use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Pingpp\Helper;

/**
 * Class ChargeCompletePurchaseRequest
 * @package Omnipay\Pingpp\Message
 */
class ChargeCompletePurchaseRequest extends AbstractChargeRequest
{
    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->getRequestParams();
    }


    public function setRequestParams($value)
    {
        $this->setParameter('request_params', $value);
    }


    public function getRequestParams()
    {
        return $this->getParameter('request_params');
    }


    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     *
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        require dirname(__FILE__) . '/../../../../pingplusplus/pingpp-php/init.php';

        $headers = \Pingpp\Util\Util::getRequestHeaders();
        // 签名在头部信息的 x-pingplusplus-signature 字段
        $signature = isset($headers['X-Pingplusplus-Signature']) ? $headers['X-Pingplusplus-Signature'] : NULL;
        // 示例
        // $signature = 'BX5sToHUzPSJvAfXqhtJicsuPjt3yvq804PguzLnMruCSvZ4C7xYS4trdg1blJPh26eeK/P2QfCCHpWKedsRS3bPKkjAvugnMKs+3Zs1k+PshAiZsET4sWPGNnf1E89Kh7/2XMa1mgbXtHt7zPNC4kamTqUL/QmEVI8LJNq7C9P3LR03kK2szJDhPzkWPgRyY2YpD2eq1aCJm0bkX9mBWTZdSYFhKt3vuM1Qjp5PWXk0tN5h9dNFqpisihK7XboB81poER2SmnZ8PIslzWu2iULM7VWxmEDA70JKBJFweqLCFBHRszA8Nt3AXF0z5qe61oH1oSUmtPwNhdQQ2G5X3g==';

        $result = Helper::verify_signature(file_get_contents('php://input'), $signature, $this->getPublicKeyPath());

        if ($result === 1) {
            // 验证通过
            if ($data['type'] == 'charge.succeeded') {
                $data['is_paid'] = true;
            } elseif ($data['type'] == 'refund.succeeded') {
                $data['is_paid'] = true;
            } else {
                $data['is_paid'] = false;
            }

            http_response_code(200);
            echo 'success';
        } elseif ($result === 0) {
            $data['is_paid'] = false;
            http_response_code(400);
            echo 'verification failed';
        } else {
            $data['is_paid'] = false;
            http_response_code(400);
            echo 'verification error';
        }

        return $this->response = new ChargeCompletePurchaseResponse($this, $data);
    }
}
