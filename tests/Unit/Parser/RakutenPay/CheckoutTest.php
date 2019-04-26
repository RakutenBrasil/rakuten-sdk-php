<?php
namespace Rakuten\Tests\Unit\Parser;

use PHPUnit\Framework\TestCase;
use Rakuten\Connector\Enum\Status;
use Rakuten\Connector\Parser\Error;
use Rakuten\Connector\Parser\RakutenPay\Checkout;
use Rakuten\Connector\Service\Http\Webservice;
use Rakuten\Connector\RakutenPay;

class CheckoutTest extends TestCase
{
    /**
     * @var Webservice
     */
    private $webservice;

    public function setUp()
    {
        $stub = $this->createMock(RakutenPay::class);
        $this->webservice = new Webservice($stub);
    }

    public function testShouldSucceedAndReturnTransactionCheckout()
    {
        $this->webservice->setStatus(Status::OK);
        $this->webservice->setResponse($this->getData());

        $response = Checkout::success($this->webservice);

        $this->assertInstanceOf(\Rakuten\Connector\Parser\RakutenPay\Transaction\Checkout::class, $response);
        $this->assertCount(12, $response->getInstallments());
        $this->assertEquals("credit_card", $response->getMethod());
        $this->assertEquals("success", $response->getResult());
    }

    public function testShouldErrorAndReturnErrorClass()
    {
        $this->webservice->setStatus(Status::BAD_REQUEST);
        $this->webservice->setResponse($this->getDataError());

        $response = Checkout::error($this->webservice);

        $this->assertInstanceOf(Error::class, $response);
        $this->assertEquals(Status::BAD_REQUEST, $response->getCode(), "Code Status");
        $this->assertEquals("Amount is missing - Amount must be greater than 0", $response->getMessage(), "Error Message");
    }

    /**
     * @return string
     */
    protected function getData()
    {
        $jsonSuccess = '
        {
           "result":"success",
           "payments":[
              {
                 "method":"credit_card",
                 "installments":[
                    {
                       "total":5149.5,
                       "quantity":1,
                       "interest_percent":2.99,
                       "interest_amount":149.5,
                       "installment_amount":5149.5
                    },
                    {
                       "total":5226.5,
                       "quantity":2,
                       "interest_percent":4.53,
                       "interest_amount":226.5,
                       "installment_amount":2613.25
                    },
                    {
                       "total":5304.51,
                       "quantity":3,
                       "interest_percent":6.09,
                       "interest_amount":304.51,
                       "installment_amount":1768.17
                    },
                    {
                       "total":5385.0,
                       "quantity":4,
                       "interest_percent":7.7,
                       "interest_amount":385.0,
                       "installment_amount":1346.25
                    },
                    {
                       "total":5466.5,
                       "quantity":5,
                       "interest_percent":9.33,
                       "interest_amount":466.5,
                       "installment_amount":1093.3
                    },
                    {
                       "total":5548.02,
                       "quantity":6,
                       "interest_percent":10.96,
                       "interest_amount":548.02,
                       "installment_amount":924.67
                    },
                    {
                       "total":5631.5,
                       "quantity":7,
                       "interest_percent":12.63,
                       "interest_amount":631.5,
                       "installment_amount":804.5
                    },
                    {
                       "total":5722.0,
                       "quantity":8,
                       "interest_percent":14.44,
                       "interest_amount":722.0,
                       "installment_amount":715.25
                    },
                    {
                       "total":5809.5,
                       "quantity":9,
                       "interest_percent":16.19,
                       "interest_amount":809.5,
                       "installment_amount":645.5
                    },
                    {
                       "total":5900.5,
                       "quantity":10,
                       "interest_percent":18.01,
                       "interest_amount":900.5,
                       "installment_amount":590.05
                    },
                    {
                       "total":5983.01,
                       "quantity":11,
                       "interest_percent":19.66,
                       "interest_amount":983.01,
                       "installment_amount":543.91
                    },
                    {
                       "total":6082.56,
                       "quantity":12,
                       "interest_percent":21.65,
                       "interest_amount":1082.56,
                       "installment_amount":506.88
                    }
                 ],
                 "cards":[
        
                 ],
                 "brands":[
                    "visa",
                    "mastercard",
                    "diners",
                    "hipercard",
                    "amex",
                    "elo"
                 ],
                 "amount":5.0e3
              },
              {
                 "method":"billet",
                 "amount":5.0e3
              }
           ]
        }';

        return $jsonSuccess;
    }

    /**
     * @return string
     */
    protected function getDataError()
    {
        return '
        {
            "result_messages": [
            "Amount is missing",
            "Amount must be greater than 0"
            ],
            "result": "failure"
        }';
    }
}