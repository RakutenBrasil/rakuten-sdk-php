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

namespace GenComm\Tests\Unit\Service\Http;

use PHPUnit\Framework\TestCase;
use GenComm\Enum\Status;
use GenComm\Exception\GenCommException;
use GenComm\Service\Http\Webservice;
use GenComm\GenPay;
use GenComm\Enum\Endpoint;

class WebserviceTest extends TestCase
{
    public function testMethodPostForCreateChargeURLWithReturnSuccess()
    {
        $info = ['http_code' => Status::OK];

        $genPay = new GenPay("fake-document", "fake-apikey", "fake-signature", "sandbox");
        $stubWebservice = $this->getMockBuilder(Webservice::class)
            ->setConstructorArgs([$genPay])
            ->setMethods(['setOption', 'execute', 'getInfo', 'getErrorCode', 'getErrorMessage', 'close'])
            ->getMock();

        $stubWebservice->expects($this->once())
            ->method('setOption');
        $stubWebservice->expects($this->once())
            ->method('close');

        $stubWebservice->expects($this->once())
            ->method('execute')
            ->willReturn($this->getResponseSuccess());
        $stubWebservice->expects($this->once())
            ->method('getInfo')
            ->willReturn($info);

        $stubWebservice->expects($this->once())
            ->method('getErrorCode')
            ->willReturn(0);

        $stubWebservice->expects($this->once())
            ->method('getErrorMessage')
            ->willReturn('');

        $stubWebservice->post(
            Endpoint::createChargeUrl($genPay->getEnvironment()),
            $this->getPayload());
    }

    public function testMethodGetWithReturnSuccessInCheckout()
    {
        $info = ['http_code' => Status::OK];

        $genPay = new GenPay("fake-document", "fake-apikey", "fake-signature", "sandbox");
        $stubWebservice = $this->getMockBuilder(Webservice::class)
            ->setConstructorArgs([$genPay])
            ->setMethods(['setOption', 'execute', 'getInfo', 'getErrorCode', 'getErrorMessage', 'close'])
            ->getMock();

        $stubWebservice->expects($this->once())
            ->method('setOption');
        $stubWebservice->expects($this->once())
            ->method('close');

        $stubWebservice->expects($this->once())
            ->method('execute')
            ->willReturn($this->getResponseSuccess());
        $stubWebservice->expects($this->once())
            ->method('getInfo')
            ->willReturn($info);

        $stubWebservice->expects($this->once())
            ->method('getErrorCode')
            ->willReturn(0);

        $stubWebservice->expects($this->once())
            ->method('getErrorMessage')
            ->willReturn('');

        $url = sprintf(
            "%s?%s",
            Endpoint::buildCheckoutUrl($genPay->getEnvironment()),
            sprintf("%s=%s", "amount", 10000)
        );
        $stubWebservice->get($url);

    }

    public function testMethodPostWithWithDataIsEmptyReturnException()
    {
        $genPay = new GenPay("fake-document", "fake-apikey", "fake-signature", "sandbox");
        $stubWebservice = $this->getMockBuilder(Webservice::class)
            ->setConstructorArgs([$genPay])
            ->setMethods(['setOption', 'execute', 'getInfo', 'getErrorCode', 'getErrorMessage', 'close'])
            ->getMock();

        $stubWebservice->expects($this->never())
            ->method('setOption');
        $stubWebservice->expects($this->never())
            ->method('close');

        $stubWebservice->expects($this->never())
            ->method('execute');
        $stubWebservice->expects($this->never())
            ->method('getInfo');

        $stubWebservice->expects($this->never())
            ->method('getErrorCode');

        $stubWebservice->expects($this->never())
            ->method('getErrorMessage');

        $this->expectException(GenCommException::class);
        $this->expectExceptionMessage("Payload is empty.");

        $stubWebservice->post(
            Endpoint::createChargeUrl($genPay->getEnvironment()),
            '');
    }

    public function testMethodPostWithErrorInCurlReturnException()
    {
        $info = ['http_code' => Status::OK];
        $errorCode = Status::INTERNAL_SERVER_ERROR;

        $genPay = new GenPay("fake-document", "fake-apikey", "fake-signature", "sandbox");
        $stubWebservice = $this->getMockBuilder(Webservice::class)
            ->setConstructorArgs([$genPay])
            ->setMethods(['setOption', 'execute', 'getInfo', 'getErrorCode', 'getErrorMessage', 'close'])
            ->getMock();

        $stubWebservice->expects($this->once())
            ->method('setOption');
        $stubWebservice->expects($this->once())
            ->method('close');

        $stubWebservice->expects($this->once())
            ->method('execute')
            ->willReturn($this->getResponseSuccess());
        $stubWebservice->expects($this->once())
            ->method('getInfo')
            ->willReturn($info);

        $stubWebservice->expects($this->once())
            ->method('getErrorCode')
            ->willReturn($errorCode);

        $stubWebservice->expects($this->once())
            ->method('getErrorMessage')
            ->willReturn('Server not Found');

        $this->expectException(GenCommException::class);
        $this->expectExceptionMessage("CURL can't connect: Server not Found");

        $stubWebservice->post(
            Endpoint::createChargeUrl($genPay->getEnvironment()),
            $this->getPayload());
    }

    /**
     * @return string
     */
    protected function getResponseSuccess()
    {
        $jsonSuccess = '
        {
          "shipping": {
            "time": null,
            "kind": null,
            "company": null,
            "amount": 0.0,
            "adjustments": []
          },
          "result_messages": [],
          "result": "success",
          "reference": "160",
          "payments": [
            {
              "status": "authorized",
              "result_messages": [],
              "result": "success",
              "refundable_amount": 100.0,
              "reference": "1",
              "method": "credit_card",
              "id": "SDG-DSG-DS-G-DS",
              "credit_card": {
                "tid": "11111111111111",
                "processor": "cielo",
                "number": "411111******1111",
                "nsu": "492734",
                "authorization_message": "Transaction Approved",
                "authorization_code": "123456"
              },
              "amount": 100.0
            }
          ],
          "fingerprint": "fake-fingerprint",
          "commissionings": [],
          "charge_uuid": "fake-charge-uuid"
        }';

        return $jsonSuccess;
    }

    /**
     * @return string
     */
    protected function getPayload()
    {
        $json = '
        {
          "webhook_url": "http://localhost/teste/teste/sdk/",
          "reference": "SDK-1",
          "payments": [
            {
              "method": "billet",
              "expires_on": "3",
              "amount": 200.0
            }
          ],
          "order": {
            "taxes_amount": 0.0,
            "shipping_amount": 0.0,
            "reference": "SDK-1",
            "payer_ip": "127.0.0.1",
            "items_amount": 200.0,
            "items": [
              {
                "total_amount": 200.0,
                "reference": "SDK-10",
                "quantity": 1,
                "description": "NIKE TENIS",
                "categories": [
                  {
                    "name": "Outros",
                    "id": "99"
                  }
                ],
                "amount": 200.0
              }
            ],
            "discount_amount": 0.0
          },
          "fingerprint": "fake-fingerprint",
          "customer": {
            "phones": [
              {
                "reference": "others",
                "number": {
                  "number": "999999998",
                  "country_code": "55",
                  "area_code": "11"
                },
                "kind": "shipping"
              },
              {
                "reference": "others",
                "number": {
                  "number": "999999998",
                  "country_code": "55",
                  "area_code": "11"
                },
                "kind": "billing"
              }
            ],
            "name": "Alex",
            "kind": "personal",
            "email": "teste@teste.com.br",
            "document": "12345678909",
            "business_name": "DR. Alex",
            "birth_date": "1985-04-16",
            "addresses": [
              {
                "zipcode": "09840-500",
                "street": "Rua Dos Morros",
                "state": "SP",
                "number": "1000",
                "kind": "shipping",
                "district": "ABC",
                "country": "BRA",
                "contact": "Maria",
                "complement": "",
                "city": "Maua"
              },
              {
                "zipcode": "09840-500",
                "street": "Rua Dos Morros",
                "state": "SP",
                "number": "1000",
                "kind": "billing",
                "district": "ABC",
                "country": "BRA",
                "contact": "Maria",
                "complement": "",
                "city": "Maua"
              }
            ]
          },
          "currency": "BRL",
          "amount": 200.0
        }';

        return $json;
    }
}
