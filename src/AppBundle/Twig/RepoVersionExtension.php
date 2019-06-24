<?php

namespace AppBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Took a repo with version information to display a link to that version on Github.
 */
class RepoVersionExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('link_to_version', [$this, 'linkToVersion']),
        ];
    }

    public function linkToVersion(array $repo)
    {
        if (!isset($repo['fullName']) || !isset($repo['tagName'])) {
            return;
        }

        return 'https://github.com/' . $repo['fullName'] . '/releases/' . urlencode($repo['tagName']);
    }

    public function getName()
    {
        return 'repo_version_extension';
    }
}
