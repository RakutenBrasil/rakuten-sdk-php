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

namespace GenComm\Parser\GenPay;

use GenComm\Parser\Error;
use GenComm\Service\Http\Webservice;
use GenComm\Parser\GenPay\Transaction\Checkout as TransactionCheckout;
use GenComm\Parser\Parser;

/**
 * Class Checkout
 * @package GenComm\Parser\GenPay
 */
class Checkout implements Parser
{
    /**
     * @return TransactionCheckout
     */
    protected static function getTransactionCheckout()
    {
        return new TransactionCheckout();
    }

    /**
     * @param Webservice $webservice
     * @return mixed|Error|TransactionCheckout
     */
    public static function success(Webservice $webservice)
    {
        $response = self::getTransactionCheckout();
        $data = json_decode($webservice->getResponse()->getResult(), true);
        $installments = array_column($data['payments'], 'installments');

        $response->setResult($data['result'])
            ->setInstallments(array_shift($installments))
            ->setMessage('')
            ->setResponse($webservice->getResponse());

        return $response;
    }

    /**
     * @param Webservice $webservice
     * @return Error
     */
    public static function error(Webservice $webservice)
    {
        $error = new Error();
        $data = json_decode($webservice->getResponse()->getResult(), true);

        $error
            ->setCode($webservice->getResponse()->getStatus())
            ->setMessage(implode(' - ', $data['result_messages']))
            ->setResponse($webservice->getResponse());

        return $error;
    }
}
