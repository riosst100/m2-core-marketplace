<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\PageBuilder\Component\Form\Element;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Wysiwyg\ConfigInterface;
use Magento\Catalog\Api\CategoryAttributeRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\PageBuilder\Model\Config as PageBuilderConfig;
use Magento\PageBuilder\Model\State as PageBuilderState;
use Magento\PageBuilder\Model\Stage\Config as Config;
use Magento\Framework\View\ConfigInterface as ViewConfigInterface;

/**
 * Updates wysiwyg element with Page Builder specific config
 *
 * @api
 */
class Wysiwyg extends \Magento\Ui\Component\Form\Element\Wysiwyg
{
    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * WYSIWYG Constructor
     *
     * @param ContextInterface $context
     * @param FormFactory $formFactory
     * @param ConfigInterface $wysiwygConfig
     * @param CategoryAttributeRepositoryInterface $attrRepository
     * @param PageBuilderState $pageBuilderState
     * @param Config $stageConfig
     * @param array $components
     * @param array $data
     * @param array $config
     * @param PageBuilderConfig|null $pageBuilderConfig
     * @param bool $overrideSnapshot
     * @param Repository $assetRepo
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        ContextInterface $context,
        FormFactory $formFactory,
        ConfigInterface $wysiwygConfig,
        CategoryAttributeRepositoryInterface $attrRepository,
        PageBuilderState $pageBuilderState,
        Config $stageConfig,
        array $components = [],
        array $data = [],
        array $config = [],
        PageBuilderConfig $pageBuilderConfig = null,
        bool $overrideSnapshot = false,
        Repository $assetRepo = null
    ) {
        $this->assetRepo = $assetRepo ?: ObjectManager::getInstance()->get(Repository::class);
        $wysiwygConfigData = isset($config['wysiwygConfigData']) ? $config['wysiwygConfigData'] : [];

        // If a dataType is present we're dealing with an attribute
        if (isset($config['dataType'])) {
            try {
                $attribute = $attrRepository->get($data['name']);

                if ($attribute) {
                    $config['wysiwyg'] = (bool)$attribute->getIsWysiwygEnabled();
                }
            } catch (NoSuchEntityException $e) {
                $config['wysiwyg'] = true;
            }
        }
        $isEnablePageBuilder = isset($wysiwygConfigData['is_pagebuilder_enabled'])
            && !$wysiwygConfigData['is_pagebuilder_enabled']
            || false;
        if ($isEnablePageBuilder) {
            $config['wysiwyg'] = true;
        }  
        parent::__construct($context, $formFactory, $wysiwygConfig, $components, $data, $config);
    }

    /**
     * Process viewport icon paths
     *
     * @param array $wysiwygConfigData
     * @return array
     */
    private function processBreakpointsIcons(array $wysiwygConfigData): array
    {
        if ($wysiwygConfigData && isset($wysiwygConfigData['viewports'])) {
            foreach ($wysiwygConfigData['viewports'] as $breakpoint => $attributes) {
                if (isset($attributes['icon'])) {
                    $wysiwygConfigData['viewports'][$breakpoint]['icon'] = $this->assetRepo->getUrl(
                        $attributes['icon']
                    );
                }
            }
        }
        return $wysiwygConfigData;
    }
}
