<?php declare(strict_types=1);

namespace MultiDescribe;

use MultiDescribe\Service\CustomFieldService;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class MultiDescribe extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);
        $this->getCustomFieldService()->manageCustomFields($installContext->getContext());
    }

    public function activate(ActivateContext $activateContext): void
    {
        parent::activate($activateContext);
        $this->getCustomFieldService()->manageCustomFields($activateContext->getContext());
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        parent::deactivate($deactivateContext);
        $this->getCustomFieldService()->manageCustomFields($deactivateContext->getContext(), false);
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }

        $this->getCustomFieldService()->manageCustomFields($uninstallContext->getContext(), false);
    }

    private function getCustomFieldService(): CustomFieldService
    {
        return $this->container->get(CustomFieldService::class);
    }
}
