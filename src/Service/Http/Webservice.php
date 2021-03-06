<?php
/**
 ************************************************************************
 * Copyright [2019] [GenComm]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 ************************************************************************
 */

namespace GenComm\Service\Http;

use GenComm\Exception\GenCommException;

/**
 * Class Webservice
 * @package GenComm\Service\Http\GenPay
 */
class Webservice extends CurlRequest
{
    /**
     * @param $method
     * @param $url
     * @param $timeout
     * @param $charset
     * @param string $jsonData
     * @param bool $secureGet
     * @return bool|mixed
     * @throws GenCommException
     */
    protected function curlConnection($method, $url, $timeout, $charset, $jsonData = '', $secureGet = true)
    {
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CONNECTTIMEOUT => $timeout,
        ];

        $methodOptions = $this->getCurlHeader($method, $jsonData);
        $options = ($options + $methodOptions);

        $this->setOption($options);
        $response = $this->execute();

        $info = $this->getInfo();

        $error = $this->getErrorCode();
        $errorMessage = $this->getErrorMessage();
        $this->close();

        if ($error) {
            throw new GenCommException("CURL can't connect: $errorMessage");
        }

        $this->response->setStatus($info['http_code']);
        $this->response->setResult($response);

        return true;
    }

    /**
     * @param $method
     * @param string $jsonData
     * @return array
     * @throws GenCommException
     */
    protected function getCurlHeader($method, $jsonData = '')
    {
        $auth = $this->genComm->getDocument() . ':' . $this->genComm->getApiKey();
        $authBase64 = base64_encode($auth);

        if (strtoupper($method) === 'POST') {
            if (empty($jsonData)) {
                throw new GenCommException("Payload is empty.");
            }

            $signature = hash_hmac('sha256', $jsonData, $this->genComm->getSignature(), true);
            $signatureBase64 = base64_encode($signature);

            return [
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Signature: ' . $signatureBase64,
                    'Authorization: Basic ' . $authBase64,
                    'Cache-Control: no-cache'
                ],
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $jsonData,
            ];
        }

        return [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Basic ' . $authBase64,
                'Cache-Control: no-cache'
            ],
            CURLOPT_HTTPGET => true
        ];
    }
}
