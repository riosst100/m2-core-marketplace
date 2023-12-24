<?php
namespace Lof\SmtpEmail\Model\Config\Source;
class Authtype implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'ssl', 'label' => 'SSL'],
            ['value' => 'tls', 'label' => 'TLS'],
            ['value' => '', 'label' => 'NONE']
        ];
    }
}
