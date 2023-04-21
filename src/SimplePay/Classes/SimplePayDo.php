<?php

namespace App\SimplePay\Classes;

use App\SimplePay\Traits\Sca;

/**
 * Do
 *
 * @category SDK
 * @package  SimplePayV2_SDK
 * @author   SimplePay IT Support <itsupport@otpmobil.com>
 * @license  http://www.gnu.org/licenses/gpl-3.0.html  GNU GENERAL PUBLIC LICENSE (GPL V3.0)
 * @link     http://simplepartner.hu/online_fizetesi_szolgaltatas.html
 */
class SimplePayDo extends Base
{
    use Sca;

    protected $currentInterface = 'do';
    protected $returnArray = [];
    public $transactionBase = [
        'salt' => '',
        'orderRef' => '',
        'customerEmail' => '',
        'merchant' => '',
        'currency' => '',
        ];

    /**
     * Constructor for do
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->apiInterface['do'] = '/v2/do';
    }

    /**
     * Run Do
     *
     * @return array $result API response
     */
    public function runDo()
    {
        return $this->execApiCall();
    }
}