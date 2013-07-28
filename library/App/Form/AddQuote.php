<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 07.10.11
 * Time: 04:43
 * To change this template use File | Settings | File Templates.
 */

namespace App\Form;

class AddQuote extends \Twitter_Bootstrap_Form_Horizontal
{
    public function init()
    {
        // $this->setDefaultTranslator(\Zend_Registry::get('Zend_Translate')); ???
        $this->setMethod('POST');
        $this->setAction($this->getView()->baseUrl('/index/add-custom'));
        $this->setAttrib('id', 'addQuote');

        $this->addElement('textarea', 'quote', array(
            'label' => 'Your wise words:',
            'rows' => 4,
            'required' => true
        ));

        $this->addElement('text', 'name', array(
            'label' => 'Your name:',
            'required' => true
        ));

        $this->addElement('submit', 'submit', array(
            'label' => 'List my quote'
        ));
    }

    public function isValid($data)
    {
        if (!is_array($data)) {
            require_once 'Zend/Form/Exception.php';
            throw new \Zend_Form_Exception(__METHOD__ . ' expects an array');
        }
        return parent::isValid($data);
    }
}