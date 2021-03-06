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
use GenComm\Enum\Status;
use GenComm\Service\Http\Response\Response;

/**
 * Class Responsibility
 * @package GenComm\Service\Http
 */
class Responsibility
{
    public static function http(Webservice $webservice, $class)
    {
        switch ($webservice->getResponse()->getStatus()) {
            case Status::OK:
                return $class::success($webservice);
            case Status::UNPROCESSABLE:
                /** returns success because only a few parameters  */
                return $class::error($webservice);
            case Status::BAD_REQUEST:
                /** returns success because only a few parameters  */
                return $class::error($webservice);
            case Status::INTERNAL_SERVER_ERROR:
                return $class::error($webservice);
            case Status::NOT_FOUND:
                $error = $class::error($webservice);
                throw new GenCommException($error->getMessage(), $error->getCode());
            case Status::UNAUTHORIZED:
                $error = $class::error($webservice);
                throw new GenCommException($error->getMessage(), $error->getCode());
            case Status::BAD_GATEWAY:
                $error = $class::error($webservice);
                throw new GenCommException($error->getMessage(), $error->getCode());
            default:
                unset($class);
                throw new GenCommException(sprintf("Unknown Error in Responsibility: %s - Status: %s", $webservice->getResponse()->getResult(), $webservice->getResponse()->getStatus()));
        }
    }
}
