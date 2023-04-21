<?php

namespace App\SimplePay\Classes;

use App\Entity\Token;
use Doctrine\Persistence\ManagerRegistry;

/**
 * RecurringStore
 *
 * @category SDK
 * @package  SimplePayV21_SDK
 * @author   SimplePay IT Support <itsupport@otpmobil.com>
 * @license  http://www.gnu.org/licenses/gpl-3.0.html  GNU GENERAL PUBLIC LICENSE (GPL V3.0)
 * @link     http://simplepartner.hu/online_fizetesi_szolgaltatas.html
 */
class RecurringStore
{
    public $storingType = 'file';
    public $request;
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Write tokens into database
     *
     * @return void
     */
    public function storeNewTokens()
    {
        $em = $this->doctrine->getManager();
        if (!isset($this->transaction['tokens']) || count($this->transaction['tokens']) == 0) {
            return false;
        }

        $counter = 1;
        foreach ($this->transaction['tokens'] as $tokenNumber) {
            $counter++;
            $token = new Token();
            $token->setMerchant($this->transaction['merchant']);
            $token->setTransactionId($this->transaction['transactionId']);
            $token->setTokenRegDate(@date("c", time()));
            $token->setCustomerEmail($this->transactionBase['customerEmail']);
            $token->setToken($tokenNumber);
            $token->setUntil($this->transactionBase['recurring']['until']);
            $token->setMaxAmount($this->transactionBase['recurring']['maxAmount']);
            $token->setCurrency($this->transaction['currency']);
            $token->setTokenState('stored');
            $token->setTimesTokenUsed(0);
            $em->persist($token);
        }
        $em->flush();
    }

    /**
     * Get tokens from file
     * 
     * @param string $serverData Data from $_SERVER
     *
     * @return array $results [$tokens, $serverData, $this->request['rContent']['m']]
     */
    public function getTokens($serverData = '')
    {
        $tokenRepository = $this->doctrine->getRepository(Token::class);
        $tokensObj = $tokenRepository->findBy(['transactionId' => $this->request['rContent']['t']]);

        $results = [$tokensObj, $serverData, $this->request['rContent']['m']];
        return $results;
    }

    /**
     * Checks token existance
     *
     * @return boolean
     */
    public function isTokenExists()
    {
        $tokenRepository = $this->doctrine->getRepository(Token::class);
        if (!empty($tokenRepository->findBy(['transactionId' => $this->request['rContent']['t']]))) {
            return true;
        }
        return false;
    }

    /**
     * Convert object to array
     *
     * @param object $obj Object to transform
     *
     * @return array $new Result array
     */
    protected function convertToArray($obj)
    {
        if (is_object($obj)) {
            $obj = (array) $obj;
        }
        $new = $obj;
        if (is_array($obj)) {
            $new = array();
            foreach ($obj as $key => $val) {
                $new[$key] = $this->convertToArray($val);
            }
        }
        return $new;
    }
}
