<?php

namespace Stegback\Ratepay\app;

class CommonHelper
{
    function getPackageVersion($packageName) {
        $composerLockPath = base_path('composer.lock');
        if (!file_exists($composerLockPath)) {
            return 'N/A';
        }
        $composerLock = json_decode(file_get_contents($composerLockPath), true);
        foreach (['packages', 'packages-dev'] as $section) {
            foreach ($composerLock[$section] as $package) {
                if ($package['name'] === $packageName) {
                    return $package['version'];
                }
            }
        }
        return 'N/A';
    }
}
