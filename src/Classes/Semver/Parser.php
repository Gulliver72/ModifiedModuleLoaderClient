<?php
declare(strict_types=1);

/*
 * This file is part of MMLC - ModifiedModuleLoaderClient.
 *
 * (c) Robin Wieschendorf <mail@robinwieschendorf.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RobinTheHood\ModifiedModuleLoaderClient\Semver;

use RobinTheHood\ModifiedModuleLoaderClient\Semver\ParseErrorException;
use RobinTheHood\ModifiedModuleLoaderClient\Helpers\IntegerHelper;

class Parser
{
    public function parse(string $string): Version
    {
        $string = $this->deletePrefix($string);

        $baseParts = explode('-', $string);
        
        if (count($baseParts) == 2) {
            $versionString = $baseParts[0];
            $tagString = $baseParts[1];

            if (!$tagString) {
                throw new ParseErrorException('Can not parse string to version: tag expected');
            }
        } else {
            $versionString = $string;
            $tagString = '';
        }

        // Parse VersionString
        $versionArray = $this->parseVersion($versionString);

        $version = new Version(
            (int) $versionArray['major'],
            (int) $versionArray['minor'],
            (int) $versionArray['patch'],
            $tagString
        );

        return $version;
    }

    public function parseVersion($string ): array
    {
        $parts = explode('.', $string);

        if (count($parts) != 3) {
            throw new ParseErrorException('Can not parse string to version array');
        }

        if ($parts[0] == '' || $parts[1] == '' || $parts[2] == '') {
            throw new ParseErrorException('Some part of version string is empty');
        }

        if (!IntegerHelper::isInteger($parts[0])) {
            throw new ParseErrorException('Major part is not a number');
        }

        if (!IntegerHelper::isInteger($parts[1])) {
            throw new ParseErrorException('Minor part is not a number');
        }

        if (!IntegerHelper::isInteger($parts[2])) {
            throw new ParseErrorException('Patch part is not a number');
        }

        return [
            'major' => (int) $parts[0],
            'minor' => (int) $parts[1],
            'patch' => (int) $parts[2]
        ];
    }

    public function deletePrefix(string $string, array $prefixes = ['v']): string
    {
        foreach ($prefixes as $prefix) {
            if (substr($string, 0, strlen($prefix)) !== $prefix) {
                return $string;
            }

            return substr($string, strlen($prefix));
        }
    }

    public function isVersion(string $string): bool
    {
        try {
            $version = $this->parse($string);
            return true;
        } catch(ParseErrorException $e) {}
        return false;
    }
}