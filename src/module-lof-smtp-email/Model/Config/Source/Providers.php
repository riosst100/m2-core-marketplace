<?php


namespace Lof\SmtpEmail\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Providers implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => '- Other -'],
            ['value' => '1', 'label' => 'AOL Mail'],
            ['value' => '2', 'label' => 'Comcast'],
            ['value' => '3', 'label' => 'GMX'],
            ['value' => '4', 'label' => 'Gmail'],
            ['value' => '5', 'label' => 'Hotmail'],
            ['value' => '6', 'label' => 'Mail.com'],
            ['value' => '7', 'label' => 'O2 Mail'],
            ['value' => '8', 'label' => 'Office365'],
            ['value' => '9', 'label' => 'Orange'],
            ['value' => '10', 'label' => 'Outlook'],
            ['value' => '11', 'label' => 'Yahoo!'],
            ['value' => '12', 'label' => 'Zoho'],
            ['value' => '13', 'label' => 'Send Grid'],
            ['value' => '14', 'label' => 'Send In Blue'],
            ['value' => '15', 'label' => 'Mandrill'],
            ['value' => '16', 'label' => 'Elastic Email'],
            ['value' => '17', 'label' => 'SparkPost'],
            ['value' => '18', 'label' => 'Mailjet'],
            ['value' => '19', 'label' => 'Mailgun'],
            ['value' => '20', 'label' => 'Postmark'],
            ['value' => '21', 'label' => 'Yahoo Mail Plus'],
            ['value' => '22', 'label' => 'Yahoo AU/NZ'],
            ['value' => '23', 'label' => 'AT&T'],
            ['value' => '24', 'label' => 'NTL @ntlworld.com'],
            ['value' => '25', 'label' => 'BT Connect'],
            ['value' => '26', 'label' => 'Verizon'],
            ['value' => '27', 'label' => 'BT Openworld'],
            ['value' => '28', 'label' => 'O2 Online Deutschland']
        ];
    }

    /**
     * get provider host name
     * @return array
     */
    public function getProviders()
    {
        return [
            '0' => 'localhost'
        ];
    }

    /**
     * get provider name
     *
     * @param int $provider
     * @return string
     */
    public function getProviderName($provider)
    {
        $options = $this->getProviders();
        $providerName = "localhost";
        foreach ($options as $key => $value) {
            if ((int)$key == (int)$provider) {
                $providerName = $value;
                break;
            }
        }
        return $providerName;
    }
}
