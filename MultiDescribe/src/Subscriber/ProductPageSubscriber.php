<?php declare(strict_types=1);

namespace MultiDescribe\Subscriber;

use MultiDescribe\Service\CustomFieldService;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductPageSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageLoadedEvent::class => 'onProductPageLoaded',
        ];
    }

    public function onProductPageLoaded(ProductPageLoadedEvent $event): void
    {
        $product = $event->getPage()->getProduct();
        $context = $event->getSalesChannelContext();
        $customFields = $product->getTranslated()['customFields'] ?? [];

        $fieldName = $this->getFieldName($context);

        if (isset($customFields[$fieldName]) && !empty(trim($customFields[$fieldName]))) {
            $product->getTranslated()['description'] = $customFields[$fieldName];
        }
    }

    private function getFieldName(SalesChannelContext $context): string
    {
        $languageId = $context->getContext()->getLanguageId();
        // We need to get the language code from the ID. This is a simplification.
        // A more robust solution would involve fetching the language entity.
        // For now, let's assume we can get it from the context somehow or construct the name differently.
        // The CustomFieldService uses language code, but here we only have the ID easily.
        // Let's adjust the logic to be consistent. We need the language code.

        // Correct way: get language from context, then its translation code.
        $language = $context->getLanguage();
        $langCode = $language->getTranslationCode()->getCode();
        
        $salesChannelId = $context->getSalesChannel()->getId();

        return CustomFieldService::CUSTOM_FIELD_PREFIX . strtolower($langCode) . '_' . $salesChannelId;
    }
}
