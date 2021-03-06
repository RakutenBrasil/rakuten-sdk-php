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

namespace GenComm\Tests\Unit\Parser\GenPay;

use PHPUnit\Framework\TestCase;
use GenComm\Exception\GenCommException;
use GenComm\Parser\GenPay\Billet;
use GenComm\Parser\GenPay\CreditCard;
use GenComm\Parser\GenPay\Factory;

class FactoryTest extends TestCase
{
    public function testGetClassByNamespace()
    {
        $parserBillet = Factory::create('GenComm\Resource\GenPay\Billet');
        $parserCreditCard = Factory::create('GenComm\Resource\GenPay\CreditCard');

        $this->assertInstanceOf(Billet::class, $parserBillet);
        $this->assertInstanceOf(CreditCard::class, $parserCreditCard);
    }

    public function testGetClassByClassNameAndGenerateException()
    {
        $this->expectException(GenCommException::class);
        $this->expectExceptionMessage("Class not Exists in TransactionFactory");
        Factory::create('Billet');
    }
}
