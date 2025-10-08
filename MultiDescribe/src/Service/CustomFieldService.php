<?php declare(strict_types=1);

namespace MultiDescribe\Service;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class CustomFieldService
{
    public const CUSTOM_FIELD_SET_NAME = 'multi_describe_set';
    public const CUSTOM_FIELD_PREFIX = 'description_';

    private EntityRepository $customFieldSetRepository;
    private EntityRepository $salesChannelRepository;
    private EntityRepository $languageRepository;

    public function __construct(
        EntityRepository $customFieldSetRepository,
        EntityRepository $salesChannelRepository,
        EntityRepository $languageRepository
    ) {
        $this->customFieldSetRepository = $customFieldSetRepository;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->languageRepository = $languageRepository;
    }

    public function manageCustomFields(Context $context, bool $activate = true): void
    {
        if ($activate) {
            $this->createCustomFields($context);
        } else {
            $this->removeCustomFields($context);
        }
    }

    private function createCustomFields(Context $context): void
    {
        $customFieldSetId = $this->getCustomFieldSetId($context);
        if ($customFieldSetId) {
            return; // Already exists
        }

        $salesChannels = $this->salesChannelRepository->search(new Criteria(), $context)->getEntities();
        $languages = $this->languageRepository->search(new Criteria(), $context)->getEntities();

        $customFields = [];
        foreach ($salesChannels as $salesChannel) {
            foreach ($languages as $language) {
                $langCode = $language->getTranslationCode()->getCode();
                $fieldName = self::CUSTOM_FIELD_PREFIX . strtolower($langCode) . '_' . $salesChannel->getId();

                $customFields[] = [
                    'name' => $fieldName,
                    'type' => CustomFieldTypes::HTML,
                    'config' => [
                        'componentName' => 'sw-text-editor',
                        'customFieldType' => 'text',
                        'label' => [
                            'en-GB' => sprintf('Description for %s (%s)', $salesChannel->getName(), $langCode),
                            'de-DE' => sprintf('Beschreibung für %s (%s)', $salesChannel->getName(), $langCode),
                        ],
                    ],
                ];
            }
        }

        $this->customFieldSetRepository->upsert([
            [
                'name' => self::CUSTOM_FIELD_SET_NAME,
                'config' => [
                    'label' => [
                        'en-GB' => 'Alternative Descriptions',
                        'de-DE' => 'Alternative Beschreibungen',
                    ],
                ],
                'relations' => [[
                    'entityName' => 'product',
                ]],
                'customFields' => $customFields,
            ],
        ], $context);
    }

    private function removeCustomFields(Context $context): void
    {
        $customFieldSetId = $this->getCustomFieldSetId($context);
        if (!$customFieldSetId) {
            return;
        }

        $this->customFieldSetRepository->delete([
            ['id' => $customFieldSetId],
        ], $context);
    }

    private function getCustomFieldSetId(Context $context): ?string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', self::CUSTOM_FIELD_SET_NAME));

        return $this->customFieldSetRepository->searchIds($criteria, $context)->firstId();
    }
}
