<?php
/**
 ************************************************************************
 * Copyright [2019] [RakutenConnector]
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

namespace Rakuten\Connector\Parser\RakutenLog;

use Rakuten\Connector\Parser\Error;
use Rakuten\Connector\Service\Http\Webservice;
use Rakuten\Connector\Parser\RakutenLog\Transaction\Batch as TransactionBatch;
use Rakuten\Connector\Parser\Parser;

/**
 * Class Batch
 * @package Rakuten\Connector\Parser\RakutenLog
 */
class Batch implements Parser
{
    /**
     * @return TransactionBatch
     */
    protected static function getTransactionBatch()
    {
        return new TransactionBatch();
    }

    /**
     * @param Webservice $webservice
     * @return TransactionBatch
     */
    public static function success(Webservice $webservice)
    {
        $response = self::getTransactionBatch();
        $data = json_decode($webservice->getResponse()->getResult(), true);
        $content = array_shift($data['content']);
        $trackingObjects = array_shift($content['tracking_objects']);

        return $response->setStatus($data['status'])
            ->setCode($content['code'])
            ->setTrackingUrl($trackingObjects['tracking_url'])
            ->setPrintUrl($trackingObjects['print_url'])
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
