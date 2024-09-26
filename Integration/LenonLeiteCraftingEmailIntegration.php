<?php

declare(strict_types=1);

namespace MauticPlugin\LenonLeiteCraftingEmailBundle\Integration;

use Mautic\IntegrationsBundle\Integration\BasicIntegration;
use Mautic\IntegrationsBundle\Integration\ConfigurationTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\BasicInterface;

class LenonLeiteCraftingEmailIntegration extends BasicIntegration implements BasicInterface
{
    use ConfigurationTrait;

    public const INTEGRATION_NAME = 'lenonleitecraftingemail';
    public const DISPLAY_NAME     = 'Crafting Email List by Lenon Leite';

    public function getName(): string
    {
        return self::INTEGRATION_NAME;
    }

    public function getDisplayName(): string
    {
        return self::DISPLAY_NAME;
    }

    public function getIcon(): string
    {
        return 'plugins/LenonLeiteCraftingEmailBundle/Assets/img/icon.png';
    }
}
