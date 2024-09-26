<?php

namespace MauticPlugin\LenonLeiteCraftingEmailBundle\Helper;

use GuzzleHttp\Client;

class GithubHelper
{
    public const GITHUB_API_URL = 'https://api.github.com';

    public const GITHUB_USERS = [
        'ricfreire',
    ];

    public function __construct(private Client $client)
    {
    }

    /**
     * @return array<string,array<\stdClass>>
     */
    public function getRepos(): array
    {
        $repos = [];
        foreach (self::GITHUB_USERS as $username) {
            $repos[$username] = $this->getRepo($username);
        }

        return $repos;
    }

    /**
     * @return array<\stdClass>
     */
    public function getRepo(string $username): array
    {
        $response = $this->sendRequest(self::GITHUB_API_URL.'/users/'.$username.'/repos')->getBody()->getContents();

        return json_decode($response);
    }

    /**
     * @return array<int,\stdClass>
     */
    public function getTemplatesRepo(): array
    {
        $usersRepository = $this->getRepos();
        $templatesRepo   = [];
        foreach ($usersRepository as $themepository) {
            foreach ($themepository as $theme) {
                if (!str_contains($theme->name, 'mautic-theme')) {
                    continue;
                }
                $templatesRepo[] = $theme;
            }
        }

        return $templatesRepo;
    }

    public function sendRequest(string $url): \Psr\Http\Message\ResponseInterface
    {
        return $this->client->get($url);
    }
}
