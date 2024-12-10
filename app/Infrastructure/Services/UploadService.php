<?php
namespace App\Infrastructure\Services;

class UploadService
{
    public function uploadImage($file, string $folder): string
    {
        if (!$file->isValid()) {
            throw new \RuntimeException($file->getErrorString());
        }

        $newName = uniqid() . '_' . $file->getRandomName();
        $path = WRITEPATH . 'uploads/' . $folder . '/' . $newName;

        if (!$file->move(WRITEPATH . 'uploads/' . $folder, $newName)) {
            throw new \RuntimeException('Error al mover el archivo');
        }

        return $folder . '/' . $newName;
    }

    public function deleteImage(string $path): void
    {
        $fullPath = WRITEPATH . 'uploads/' . $path;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}