<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_RequestForQuote
 * @copyright  Copyright (c) 2017 Landofcoder (https://www.landofcoder.com/)
 * @license    https://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\SellerInvoice\Helper;

//use Dompdf\Dompdf as LOFPDF;
use Mpdf\Mpdf as LOFPDF;

class Pdf extends \Magento\Framework\App\Helper\AbstractHelper
{
	public $pdf;

	public $output;

    public $_html = '';

    protected $store_id = 0;

	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        LOFPDF $domPdf
        ) {
        parent::__construct($context);
        $this->pdf = $domPdf;
        $this->pdf->ignore_table_percents = FALSE;
    }

    /**
     * Load html
     *
     * @param $html
     */
    public function setData($html)
    {
        $this->_html = $html;
        if($this->pdf) {
            //$this->pdf->loadHtml($html, 'UTF-8');
            $this->pdf->WriteHTML($html);
        }
        return $this;
    }
    /**
     * Render LOFPDF output
     *
     * @return string
     */
    public function renderOutput($filename = "")
    {
        if($this->output) {
            return $this->output;
        }
        if(!$this->pdf || $this->pdf === null) {
            $this->pdf = new LOFPDF(['mode' => 'BLANK', 'default_font' => 'sans-serif', 'tempDir' => BP . '/var/tmp']);
            $this->pdf->ignore_table_percents = FALSE;
            //$this->pdf->loadHtml($this->_html, 'UTF-8');
            $this->pdf->WriteHTML($this->_html);
        }
        //$this->pdf->render();
        //$this->output = $this->pdf->output();
        if($filename) {
            $this->output = $this->pdf->Output('', 'S');
        }else{
            $this->output = $this->pdf->Output();
        }
        
        return $this->output;
    }
    
    
}