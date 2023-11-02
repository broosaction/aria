<?php
/**
 * Copyright (c) 2023.  Broos Action
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 *
 * broosaction.com
 * Updater.php created  09-11-2023  08:58
 */


namespace Core\Joi\System\Utils;

use Core\Joi\System\Utils;
use Nette\Utils\FileSystem;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class Updater
{

    public function applyUpdate($zipUrl, $rootFolder) {
        // Ensure the root folder exists
        if (!file_exists($rootFolder)) {
            if (!mkdir($rootFolder, 0755, true) && !is_dir($rootFolder)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $rootFolder));
            }
        }

        // Generate a backup folder name with a timestamp
        $backupFolder = $rootFolder . '/data/update/backup_';
        FileSystem::createDir($backupFolder, 0755);

        // Download the zip file
        $zipFile = $rootFolder . '/data/update/downloaded.zip';

        FileSystem::write($zipFile, Utils::quickGetFileContents($zipUrl));

        // Unzip the downloaded file to a temporary folder
        $tempFolder = $rootFolder . '/data/update/temp';
        FileSystem::createDir($tempFolder, 0755);

        $zip = new ZipArchive;
        if ($zip->open($zipFile) === true) {
            $zip->extractTo($tempFolder);
            $zip->close();

            // Backup and replace existing files
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($tempFolder),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $sourceFile = $file->getPathname();
                    $relativePath = substr($sourceFile, strlen($tempFolder));
                    $targetFile = $rootFolder . $relativePath;
                    $backupFile = $backupFolder . $relativePath;

                    if (file_exists($targetFile)) {
                        // Backup the existing file
                        FileSystem::createDir($concurrentDirectory = dirname($backupFile), 0755);
                        rename($targetFile, $backupFile);
                    }

                    // Replace the existing file with the one from the zip
                    FileSystem::createDir($concurrentDirectory = dirname($targetFile), 0755);
                    FileSystem::copy($sourceFile, $targetFile);
                }
            }

            // Clean up by deleting the downloaded zip file and the temporary folder
            unlink($zipFile);
            $this->deleteDirectory($tempFolder);

            return true;
        } else {
            return false;
        }
    }

// Helper function to delete a directory and its contents
    private function deleteDirectory($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object !== "." && $object !== "..") {
                    if (is_dir($dir . "/" . $object)) {
                        $this->deleteDirectory($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }


}