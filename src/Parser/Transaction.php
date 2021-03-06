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

namespace GenComm\Parser;

use GenComm\Service\Http\Response\Response;

/**
 * Interface Transaction
 * @package GenComm\Parser
 */
abstract class Transaction
{
    /**
     * @param $message
     * @return string
     */
    public abstract function setMessage($message);

    /**
     * @return string
     */
    public abstract function getMessage();

    /**
     * @param Response $response
     */
    public abstract function setResponse(Response $response);

    /**
     * @return Response
     */
    public abstract function getResponse();

    /**
     * @return bool
     */
    public function isError()
    {
        return get_called_class() === Error::class;
    }
}
