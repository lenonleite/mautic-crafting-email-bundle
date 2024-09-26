<?php

return [
    'name'        => 'Download New Emails From Crafting Email Github',
    'description' => 'This is a plugin to list new emails.',
    'version'     => '1.0.0',
    'author'      => 'Lenon Leite',
    'services'    => [
        'integrations' => [
            'mautic.integration.lenonleitecraftingemail' => [
                'class' => \MauticPlugin\LenonLeiteCraftingEmailBundle\Integration\LenonLeiteCraftingEmailIntegration::class,
                'tags'  => [
                    'mautic.integration',
                    'mautic.basic_integration',
                ],
            ],
            'mautic.integration.lenonleitecraftingemail.configuration' => [
                'class' => \MauticPlugin\LenonLeiteCraftingEmailBundle\Integration\Support\ConfigSupport::class,
                'tags'  => [
                    'mautic.config_integration',
                ],
            ],
            'mautic.integration.lenonleitecraftingemail.config' => [
                'class' => \MauticPlugin\LenonLeiteCraftingEmailBundle\Integration\Config::class,
                'tags'  => [
                    'mautic.integrations.helper',
                ],
            ],
        ],
    ],
];
