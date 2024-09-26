<?php

declare(strict_types=1);

namespace MauticPlugin\LenonLeiteCraftingEmailBundle\Integration\Support;

use Mautic\IntegrationsBundle\Integration\DefaultConfigFormTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormInterface;
use MauticPlugin\LenonLeiteCraftingEmailBundle\Integration\LenonLeiteCraftingEmailIntegration;

class ConfigSupport extends LenonLeiteCraftingEmailIntegration implements ConfigFormInterface
{
    use DefaultConfigFormTrait;
}
