<?php
namespace Lofmp\DeliverySlot\Block;
  
use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
  
class DatePicker extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var  Registry
     */
    protected $_coreRegistry;
  
    /**
     * @param Context  $context
     * @param Registry $coreRegistry
     * @param array    $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }
  
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        //get configuration element
        $html = $element->getElementHtml();
        //check datepicker set or not
        if (!$this->_coreRegistry->registry('datepicker_loaded_'. $element->getHtmlId())) {
            $this->_coreRegistry->registry('datepicker_loaded_'. $element->getHtmlId(), 1);
        }
        //add icon on datepicker
      
        // add datepicker with element by jquery
        $html .= '<script type="text/javascript">
            require(["jquery", "jquery/ui"], function (jq) {
                jq(document).ready(function () {
                    jq("#' . $element->getHtmlId() . '").datepicker( { dateFormat: "dd/mm/yy" } );
                    jq(".ui-datepicker-trigger").removeAttr("style");
                    jq(".ui-datepicker-trigger").click(function(){
                        jq("#' . $element->getHtmlId() . '").focus();
                    });
                });
            });
            </script>';
        // return datepicker element
        return $html;
    }
}
