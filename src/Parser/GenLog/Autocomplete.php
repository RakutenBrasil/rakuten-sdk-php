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

namespace GenComm\Parser\GenLog;

use GenComm\Parser\Error;
use GenComm\Service\Http\Webservice;
use GenComm\Parser\GenLog\Transaction\Autocomplete as TransactionAutocomplete;
use GenComm\Parser\Parser;

/**
 * Class Autocomplete
 * @package GenComm\Parser\GenLog
 */
class Autocomplete implements Parser
{
    /**
     * @return TransactionAutocomplete
     */
    protected static function getTransactionAutocomplete()
    {
        return new TransactionAutocomplete();
    }

    /**
     * @param Webservice $webservice
     * @return TransactionAutocomplete
     */
    public static function success(Webservice $webservice)
    {
        $response = self::getTransactionAutocomplete();
        $data = json_decode($webservice->getResponse()->getResult(), true);

        return $response->setStatus($data['status'])
            ->setStreet($data['content']['street'])
            ->setDistrict($data['content']['district'])
            ->setCity($data['content']['city'])
            ->setState($data['content']['state'])
            ->setZipcode($data['content']['zipcode'])
            ->setMessage(implode(' - ', $data['messages']))
            ->setResponse($webservice->getResponse());
    }

    /**
     * @param Webservice $webservice
     * @return Error
     */
    public static function error(Webservice $webservice)
    {
        $error = new Error();
        $newMessages = [];

        $data = json_decode($webservice->getResponse()->getResult(), true);
        $code = $data['status'];
        $messages = $data['messages'];

        foreach ($messages as $message) {
            $newMessages[] = $message['text'];
        }

        $error
            ->setCode($code)
            ->setMessage(implode(' - ', $newMessages))
            ->setResponse($webservice->getResponse());

        return $error;
    }
}
