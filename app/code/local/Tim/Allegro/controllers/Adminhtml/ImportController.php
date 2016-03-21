<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Allegro
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Allegro_Adminhtml_ImportController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initialize layout.
     * @return $this
     */
    protected function _initAction()
    {
        $this->_title($this->__('Import'))
            ->loadLayout()
            ->_setActiveMenu('system/tim_import_interface');

        return $this;
    }

    /**
     * Index action.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_initAction()
            ->_title($this->__('Import'))
            ->_addBreadcrumb($this->__('Import'), $this->__('Import'));

        $this->renderLayout();
    }

    /**
     * Import xml files
     */
    public function importXmlAction()
    {
        $result = array(
            'message' => '',
            'success' => false,
        );
        //Checking if script still executing
        $isExecuting = shell_exec("ps ax | grep -i 'importXml.php' | wc -l");

        if ($isExecuting != 2) {
            $result['message'] = Mage::helper('tim_allegro')->__('Import is still running! Please wait.');
        } else {
            $path = Mage::getBaseDir() . DS . 'shell/tim_allegro';
            $start = "cd " . $path . "; php -f importXml.php >/dev/null 2>&1 &";
            exec($start);
            $result['message'] = Mage::helper('tim_allegro')->__('Import was executed!');
            $result['success'] = true;
        }

        /* @var $messageBlock Mage_Core_Block_Messages */
        $messageBlock = Mage::getBlockSingleton('core/messages');
        if ($result['success']) {
            $messageBlock->addSuccess($result['message']);
        } else {
            $messageBlock->addError($result['message']);
        }

        $result['html'] = $messageBlock->getGroupedHtml();

        $this->getResponse()->setBody(json_encode($result));
    }
}