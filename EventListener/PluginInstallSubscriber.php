<?php

namespace MauticPlugin\LenonLeiteCraftingEmailBundle\EventListener;

use GuzzleHttp\Client;
use Mautic\CoreBundle\Helper\Filesystem;
use Mautic\CoreBundle\Helper\PathsHelper;
use Mautic\CoreBundle\Helper\ThemeHelper;
use Mautic\CoreBundle\Service\FlashBag;
use Mautic\PluginBundle\Event\PluginInstallEvent;
use Mautic\PluginBundle\PluginEvents;
use MauticPlugin\LenonLeiteCraftingEmailBundle\Helper\GithubHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PluginInstallSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private GithubHelper $githubHelper,
        private Client $client,
        private ThemeHelper $themeHelper,
        protected PathsHelper $pathsHelper,
        protected Filesystem $filesystem,
        private FlashBag $flashBag,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PluginEvents::ON_PLUGIN_INSTALL => ['onPluginInstall', 0],
        ];
    }

    public function onPluginInstall(PluginInstallEvent $event): void
    {
        $githubRepos  = $this->githubHelper->getTemplatesRepo();
        $installed    = [];
        $errorInstall = [];
        foreach ($githubRepos as $repo) {
            $themePath = $this->copyTemplate($repo);
            $this->reorganizeZipFiles($themePath);
            $result = $this->themeHelper->install($themePath);
            if ($result) {
                $installed[] = basename($themePath);
            } else {
                $errorInstall[] = basename($themePath);
            }
        }

        if (count($installed) > 0) {
            $this->notifySuccess($installed);
        }

        if (count($errorInstall) > 0) {
            $this->notifyError($errorInstall);
        }
    }

    /**
     * @param array<string> $errorInstall
     */
    private function notifyError(array $errorInstall): void
    {
        $message = 'Themes not installed: ';
        $message .= '<ul>';
        foreach ($errorInstall as $theme) {
            $message .= '<li>'.str_replace('.zip', '', $theme).'</li>';
        }
        $message .= '</ul>';
        $this->flashBag->add('Error : '.$message, [], FlashBag::LEVEL_ERROR);
    }

    /**
     * @param array<string> $installed
     */
    private function notifySuccess(array $installed): void
    {
        $message = 'Themes installed successfully: ';
        $message .= '<ul>';
        foreach ($installed as $theme) {
            $message .= '<li>'.str_replace('.zip', '', $theme).'</li>';
        }
        $message .= '</ul>';

        $this->flashBag->add('Success : '.$message, [], FlashBag::LEVEL_NOTICE);
    }

    private function copyTemplate(object $repo): bool|string
    {
        try {
            $zipUrl   = $repo->html_url.'/archive/refs/heads/main.zip';
            $fileName = $repo->name.'.zip';
            $dir      = $this->pathsHelper->getSystemPath('themes', true);
            $zipFile  = $this->client->get($zipUrl);
            $this->filesystem->dumpFile($dir.'/'.$fileName, $zipFile->getBody());

            return $dir.'/'.$fileName;
        } catch (\Exception) {
            return false;
        }
    }

    private function reorganizeZipFiles(?string $themePath): void
    {
        $zip = new \ZipArchive();
        $zip->open($themePath);
        $internalNameFolder = $zip->getNameIndex(0);
        $zip->extractTo($this->pathsHelper->getSystemPath('themes', true));
        $zip->close();
        $themePathFolder = $this->pathsHelper->getSystemPath('themes', true).'/'.$internalNameFolder;
        $directory       = new \RecursiveDirectoryIterator($themePathFolder);
        $iterator        = new \RecursiveIteratorIterator($directory);
        foreach ($iterator as $file) {
            if ('.' == $file->getBasename() || '..' == $file->getBasename()) {
                continue;
            }
            $files[] = $file->getPathname();
        }

        if (true === $zip->open($themePath, \ZipArchive::CREATE)) {
            foreach ($files as $file) {
                $entryName = str_replace($themePathFolder, '', $file);
                $zip->addFile($file, $entryName);
            }
            $zip->close();
        }
        $this->filesystem->remove($themePathFolder);
    }
}
