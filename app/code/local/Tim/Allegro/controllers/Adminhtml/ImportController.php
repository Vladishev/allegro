<?php


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
     * Import products
     */
    public function importCsvAction()
    {
        $parsedSku = Mage::getModel('tim_allegro/importFiles')->parseCsv();
//        $result = array(
//            'message' => '',
//            'success' => false,
//        );
//        $importFilePath = Mage::getBaseDir('var') . '/import/';
//        $fileName = Mage::getStoreConfig('tim_export_import/import_from_xml/import');
//        $file = $importFilePath . $fileName;
//        $finfo = new finfo(FILEINFO_MIME);
//        $info = $finfo->file($file);
//        $pos = strpos($info, 'application/xml', 0);
//        $type = ($pos !== false) ? 'xml' : null;
//        $allowedExtensions = Mage::getModel('tim_export_import/system_config_backend_xml')->_getAllowedExtensions();
//        if (is_file($file)) {
//            if (in_array($type, $allowedExtensions)) {
//                if (shell_exec("ps ax | grep -i 'importXml.php' | wc -l") != 2) {
//                    $result['message'] = Mage::helper('adminhtml')->__('Import is still running! Please wait.');
//                } else {
//                    $path = Mage::getBaseDir() . DS . 'tools/importProducts';
//                    $start = "cd $path; php importXml.php";
//                    exec($start);
//                    $result['message'] = Mage::helper('adminhtml')->__('Import was executed!');
//                    $result['success'] = true;
//                }
//            } else {
//                $result['message'] = Mage::helper('adminhtml')->__('Wrong file format');
//            }
//        } else {
//            $result['message'] = Mage::helper('adminhtml')->__('No file to import data, please upload file and try again.');
//        }
//
//        /* @var $messageBlock Mage_Core_Block_Messages */
//        $messageBlock = Mage::getBlockSingleton('core/messages');
//        if ($result['success']) {
//            $messageBlock->addSuccess($result['message']);
//        } else {
//            $messageBlock->addError($result['message']);
//        }
//
//        $result['html'] = $messageBlock->getGroupedHtml();
//
//        $this->getResponse()->setBody(json_encode($result));
    }
}