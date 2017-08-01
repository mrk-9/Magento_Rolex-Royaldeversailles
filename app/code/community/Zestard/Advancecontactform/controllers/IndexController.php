<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Contacts
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Contacts index controller
 *
 * @category   Mage
 * @package    Mage_Contacts
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Zestard_Advancecontactform_IndexController extends Mage_Core_Controller_Front_Action
{
   public function indexAction()
   {
       $this->loadLayout();
        $this->getLayout()->getBlock('advancecontactForm')
            ->setFormAction( Mage::getUrl('*/*/post') );
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }
    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        //echo "<pre>";
        //print_r($post);
        //echo "</pre>";
        //exit();
        if ( $post ) {
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
            try {
//                $postObject = new Varien_Object();
//                $postObject->setData($post);
                $error = false;
                if (!Zend_Validate::is(trim($post['fname']) , 'NotEmpty')) {
                    $error = true;
                }
                if (!Zend_Validate::is(trim($post['lname']) , 'NotEmpty')) {
                    $error = true;
                }
                if (!Zend_Validate::is(trim($post['telephone']) , 'NotEmpty')) {
                    $error = true;
                }
                if (!Zend_Validate::is(trim($post['contactperson']) , 'NotEmpty')) {
                    $error = true;
                }
                
                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                }
                
                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }
                
                if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }
                if ($error) {
                    throw new Exception();
                }
                $contactPersons = explode(",", $post["contactperson"]);
                foreach ($contactPersons as $key => $contactPerson) 
                {
                    //$mailTemplate = Mage::getModel('core/email_template');
                    $ident = Mage::getStoreConfig('advancecontactformsetting/advancecontactform/sender_email_identity');
                    $fromName = Mage::getStoreConfig('trans_email/ident_'.$ident.'/name');
                    $fromEmail = Mage::getStoreConfig('trans_email/ident_'.$ident.'/email');
                    $emailTemplate = Mage::getModel('core/email_template')->loadDefault(Mage::getStoreConfig('advancecontactformsetting/advancecontactform/advancecontactformtemplate'));
                    $emailTemplateVariables = $post;
                    $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                 
                    $mail = Mage::getModel('core/email')
                            ->setToName($post['fname'])
                            ->setToEmail($contactPerson)
                            ->setBody($processedTemplate)
                            ->setSubject('Contact Details of - '.$post['fname']. ' '.$post['lname'])
                            ->setFromEmail($fromEmail)
                            ->setFromName($fromName)
                            ->setType('html');
                    $mail->send();
                    /* @var $mailTemplate Mage_Core_Model_Email_Template */                }
                $translate->setTranslateInline(true);
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                $this->_redirect('*/*/index');
                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);
                Mage::getSingleton('core/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'
                        ));
                $this->_redirect('*/*/index');
                return;
            }
        } else {
            $this->_redirect('*/*/index');
        }
    }
}
?>
