<?php

declare(strict_types=1);

namespace App\Services\Cron;

use App\Services\Cron\CronJson\AbstractBackupCronState;
use PhpZip\ZipFile;
use App\Services\Admin\FTPHandler;

class ZipBackupCron
{
    const MAX_BACKUP_COUNT = 2;

    function __construct(
        private FTPHandler $ftpHandler
    ) {
    }

    /**
     * @throws \RuntimeException
     * @throws ZipException
     */
    function ftpZipBackUp(AbstractBackupCronState $state): string
    {
        if ($state->isActive()) {
            throw new \RuntimeException(getClassSimpleName($state) . ': state is active');
        }

        $state->setActive();

        $remotePath = $state->getRemotePath();

        $backups = $this->deleteOldBackup($state->getBackups(), $remotePath, self::MAX_BACKUP_COUNT);
        $backups[] = $remotePath;

        $this->uploadEachZippedDirectory($state->getTargetLocalPath(), $remotePath, $state->getTempZipPath());

        $state->setBackups($backups);

        return getClassSimpleName($state) . ": done {$remotePath}";
    }

    /**
     * @throws \RuntimeException
     */
    private function deleteOldBackup(array $backups, string $currentRemotePath, int $maxBackupCount): array
    {
        $backupCount = count($backups);
        if ($backupCount > $maxBackupCount) {
            for ($i = 0; $i < ($backupCount - $maxBackupCount); $i++) {
                $dir = array_shift($backups);
                $this->ftpHandler->deleteDirectoryRecursive($dir);
            }
        }

        $result = [];
        foreach ($backups as $dir) {
            if ($dir === $currentRemotePath) {
                $this->ftpHandler->deleteDirectoryRecursive($dir);
            } else {
                $result[] = $dir;
            }
        }

        return $result;
    }

    /**
     * @throws \RuntimeException
     * @throws ZipException
     * @throws \ErrorException
     */
    private function uploadEachZippedDirectory(string $targetLocalPath, string $remotePath, string $tempZipPath)
    {
        $files = glob($targetLocalPath . '/*');
        if (!$files) {
            throw new \RuntimeException('ディレクトリが無効です: ' . $targetLocalPath);
        }

        mkdirIfNotExists($tempZipPath);

        foreach ($files as $file) {
            if (is_dir($file)) {
                $path = basename($file);
                $tempZipFile = "{$tempZipPath}/{$path}.zip";
                $inputPath = "{$targetLocalPath}/{$path}";

                $this->ftpUploadZip($tempZipFile, $remotePath, $inputPath);
                unlink($tempZipFile);
            }
        }

        deleteDirectory($tempZipPath);
    }

    /**
     * @throws \RuntimeException
     * @throws ZipException
     */
    private function ftpUploadZip(string $tempZipFile, string $remotePath, string $inputPath, string $globPattern = '*.*')
    {
        (new ZipFile())
            ->addFilesFromGlobRecursive($inputPath, $globPattern)
            ->saveAsFile($tempZipFile)
            ->close();

        $this->ftpHandler->createDirectoryIfNeeded($remotePath);

        $result = $this->ftpHandler->uploadFile($tempZipFile, $remotePath);
        if (!$result) {
            throw new \RuntimeException("ftpUploadZip Error: {$tempZipFile} {$remotePath}");
        }

        var_dump($inputPath);
    }
}
