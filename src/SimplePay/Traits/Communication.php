<?php 

namespace App\SimplePay\Traits;

use Exception;

/**
  * Communication
  *
  * @category SDK
  * @package  SimplePayV2_SDK
  * @author   SimplePay IT Support <itsupport@otpmobil.com>
  * @license  http://www.gnu.org/licenses/gpl-3.0.html  GNU GENERAL PUBLIC LICENSE (GPL V3.0)
  * @link     http://simplepartner.hu/online_fizetesi_szolgaltatas.html
  */
  trait Communication
  {
  
      /**
       * Handler for cURL communication
       *
       * @param string $url     URL
       * @param string $data    Sending data to URL
       * @param string $headers Header information for POST
       *
       * @return array Result of cURL communication
       */
      public function runCommunication($url = '', $data = '', $headers = [])
      {
          $result = '';
          $curlData = curl_init();
          curl_setopt($curlData, CURLOPT_URL, $url);
          curl_setopt($curlData, CURLOPT_POST, true);
          curl_setopt($curlData, CURLOPT_POSTFIELDS, $data);
          curl_setopt($curlData, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($curlData, CURLOPT_USERAGENT, 'curl');
          curl_setopt($curlData, CURLOPT_TIMEOUT, 60);
          curl_setopt($curlData, CURLOPT_FOLLOWLOCATION, true);
          curl_setopt($curlData, CURLOPT_HTTPHEADER, $headers);
          curl_setopt($curlData, CURLOPT_HEADER, true);
          //cURL + SSL
          //curl_setopt($curlData, CURLOPT_SSL_VERIFYPEER, false);
          //curl_setopt($curlData, CURLOPT_SSL_VERIFYHOST, false);
          $result = curl_exec($curlData);
          $this->result = $result;
          $this->curlInfo = curl_getinfo($curlData);
          try {
              if (curl_errno($curlData)) {
                  throw new Exception(curl_error($curlData));
              }
          } catch (Exception $e) {
              $this->logContent['runCommunicationException'] = $e->getMessage();
          }
          curl_close($curlData);
          return $result;
      }
  }
  
  
   /**
    * Views
    *
    * @category SDK
    * @package  SimplePayV2_SDK
    * @author   SimplePay IT Support <itsupport@otpmobil.com>
    * @license  http://www.gnu.org/licenses/gpl-3.0.html  GNU GENERAL PUBLIC LICENSE (GPL V3.0)
    * @link     http://simplepartner.hu/online_fizetesi_szolgaltatas.html
    */
  trait Views
  {
      public $formDetails = [
          'id' => 'SimplePayForm',
          'name' => 'SimplePayForm',
          'element' => 'button',
          'elementText' => 'Start SimplePay Payment',
      ];
  
      /**
       * Generates HTML submit element
       *
       * @param string $formName          The ID parameter of the form
       * @param string $submitElement     The type of the submit element ('button' or 'link' or 'auto')
       * @param string $submitElementText The label for the submit element
       *
       * @return string HTML submit
       */
      protected function formSubmitElement($formName = '', $submitElement = 'button', $submitElementText = '')
      {
          switch ($submitElement) {
          case 'link':
              $element = "\n<a href='javascript:document.getElementById(\"" . $formName ."\").submit()'>".addslashes($submitElementText)."</a>";
              break;
          case 'button':
              $element = "\n<button type='submit'>".addslashes($submitElementText)."</button>";
              break;
          case 'auto':
              $element = "\n<button type='submit'>".addslashes($submitElementText)."</button>";
              $element .= "\n<script language=\"javascript\" type=\"text/javascript\">document.getElementById(\"" . $formName . "\").submit();</script>";
              break;
          default :
              $element = "\n<button type='submit'>".addslashes($submitElementText)."</button>";
              break;
          }
          return $element;
      }
  
      /**
       * HTML form creation for redirect to payment page
       *
       * @return void
       */
      public function getHtmlForm()
      {
          $this->returnData['form'] = 'Transaction start was failed!';
          if (isset($this->returnData['paymentUrl']) && $this->returnData['paymentUrl'] != '') {
              $this->returnData['form'] = '<form action="' . $this->returnData['paymentUrl'] . '" method="GET" id="' . $this->formDetails['id'] . '" accept-charset="UTF-8">';
              $this->returnData['form'] .= $this->formSubmitElement($this->formDetails['name'], $this->formDetails['element'], $this->formDetails['elementText']);
              $this->returnData['form'] .= '</form>';
          }
      }
  
      /**
       * Notification based on back data
       *
       * @return void
       */
      protected function backNotification()
      {
          $this->notificationFormated = '<div>';
          $this->notificationFormated .= '<b>Sikertelen fizetés!</b>';
          if ($this->request['rContent']['e'] == 'SUCCESS') {
              $this->notificationFormated = '<div>';
              $this->notificationFormated .= '<b>Sikeres fizetés</b>';
          }
          $this->notificationFormated .= '<b>SimplePay tranzakció azonosító:</b> ' . $this->request['rContent']['t'] . '</br>';
          $this->notificationFormated .= '<b>Kereskedői referencia szám:</b> ' . $this->request['rContent']['o'] . '</br>';
          $this->notificationFormated .= '</div>';
      }
  }