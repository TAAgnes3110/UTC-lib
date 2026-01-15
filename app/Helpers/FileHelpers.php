<?php

namespace App\Helpers;

use App\Helpers\Helpers;
use App\Models\FileUpload;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use NcJoes\OfficeConverter\OfficeConverter;
use setasign\Fpdi\Fpdi;
use ZipArchive;

class FileHelpers
{
    public static $_instance = null;
    public static array $thumb_size = ['width' => 480, 'height' => 270];
    public static array $xs_size = ['width' => 320, 'height' => null];
    public static array $md_size = ['width' => 480, 'height' => null];
    public static array $lg_size = ['width' => 680, 'height' => null];
    public string $taxonomy = '';
    private $disk;
    public int $id = 0;
    public int $customer_id = 0;
    public string $file_ext = '';
    public string $file_name = '';
    public string $full_name = '';
    public array $image_size = [];
    public int $file_size = 0;
    public string $file_mime = '';
    public string $file_path = '';
    public string $file_url = '';
    public string $download_url = '';
    public int $ordering = 1;
    public bool $status = false;
    public $image = null;
    function __construct($taxonomy = 'files', $subPath = '', $global = false)
    {
        $this->taxonomy = $taxonomy;

        $user = Auth::user();
        if ($user && $user->customer) {
            $this->customer_id = $user->customer->id;
        }

        if (!$subPath)
            $subPath = date('Y/m') . '/' . $this->taxonomy;

        if ($global) {
            $this->file_path = $subPath;
        } else {
            $prefix = $user ? 'user_' . $user->id : 'public';
            $this->file_path = $prefix . '/' . $subPath;
        }

        if (!Storage::disk()->exists($this->file_path . '/')) {
            Storage::disk()->makeDirectory($this->file_path . '/');
        }
        if (!Storage::disk()->exists('tmp/')) {
            Storage::disk()->makeDirectory('tmp/');
        }
    }
    public function store($related_id = 0): static
    {
        if ($this->status) {
            $this->ordering = DB::table(FileUpload::$tableName)->max('ordering') + 1;
            $user = Auth::user();
            $data = [
                'customer_id' => $this->customer_id,
                'taxonomy' => $this->taxonomy,
                'user_id' => $user ? $user->id : 0,
                'related_id' => $related_id,
                'file_name' => $this->file_name,
                'file_ext' => $this->file_ext,
                'file_size' => $this->file_size,
                'file_mime' => $this->file_mime,
                'file_path' => $this->file_path,
                'file_url' => $this->file_url,
                'download_count' => 0,
                'ordering' => $this->ordering,
            ];
            if ($id = DB::table(FileUpload::$tableName)->insertGetId($data)) {
                $this->id = $id;
            }
        }
        return $this;
    }
    public static function removeFolder($subPath): void
    {
        if (Storage::disk()->exists($subPath)) {
            Storage::disk()->deleteDirectory($subPath);
        }
    }
    public static function createFolder($subPath): void
    {
        if (!Storage::disk()->exists($subPath)) {
            Storage::disk()->makeDirectory($subPath);
        }
    }
    public static function renameFolder($oldName, $newName): void
    {
        if (Storage::disk()->exists($oldName)) {
            Storage::disk()->move($oldName, $newName);
        }
    }
    public static function init($taxonomy = 'editor', $subPath = ''): ?FileHelpers
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($taxonomy, $subPath);
        }
        return self::$_instance;
    }
    public function getId(): int
    {
        return $this->id;
    }
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function setFileName(string $file_name, $prefix = ''): void
    {
        if ($file_name) {
            $this->file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            if ($prefix == 'OriginalName') {
                $prefix = Helpers::toAccount(strtolower(pathinfo($file_name, PATHINFO_FILENAME)), '-') . '_';
            }
            $this->file_name = $prefix . Helpers::generateRandomOnlyString(4) . '-' . time();
            $this->full_name = $this->file_name . '.' . $this->file_ext;
        }
    }
    public function setFileNameBySuffix(string $file_name, $suffix = ''): void
    {
        if ($file_name) {
            $this->file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            if ($suffix == 'OriginalName') {
                $suffix = Helpers::toAccount(strtolower(pathinfo($file_name, PATHINFO_FILENAME)), '_') . '_';
            }
            $this->file_name = Helpers::generateRandomOnlyString(4) . '-' . time() . $suffix;
            $this->full_name = $this->file_name . '.' . $this->file_ext;
        }
    }
    public function getFilePath(): string
    {
        return $this->file_path;
    }
    public function createZip($files, $delete = false)
    {
        $zip = new ZipArchive;
        $this->file_ext = 'zip';
        $this->file_name = time();
        $this->full_name = $this->file_name . '.' . $this->file_ext;
        $this->file_mime = 'application/zip';
        if ($zip->open(Storage::path($this->file_path . '/' . $this->full_name), ZipArchive::CREATE) === TRUE) {
            foreach ($files as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
            $this->file_size = Storage::size($this->file_path . '/' . $this->full_name);
            if ($delete) {
                foreach ($files as $file) {
                    Storage::disk()->delete($file);
                }
            }
            return $this;
        } else {
            return false;
        }
    }
    public function setFilePath(string $file_path): void
    {
        $this->file_path = $file_path;
    }
    public function getFileType(): string
    {
        switch ($this->file_ext) {
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'webp':
                return 'image';
            case 'doc':
            case 'docx':
                return 'doc';
            case 'xls':
            case 'xlsx':
                return 'xls';
            case 'mp4':
                return 'video';
            case 'pdf':
                return 'pdf';
            default:
                return 'file';
        }
    }
    public function getDownloadPathByItem($item): bool|string
    {
        $full_path = $item->file_path . '/' . $item->file_name . '.' . $item->file_ext;
        if (Storage::disk()->exists($full_path)) {
            return Storage::disk()->url($full_path);
        }
        return false;
    }
    public function getDocumentDownload($item): bool
    {
        $full_path = $item->file_path . '/' . $item->full_name;
        if (Storage::disk()->exists($full_path)) {
            $this->download_url = Storage::disk()->path($full_path);
            return true;
        }
        return false;
    }
    public function hasDocumentDownload($item): bool
    {
        if ($item->status) {
            if (Storage::disk()->exists($item->file_path . '/' . $item->full_name)) {
                return true;
            }
        }
        return false;
    }

    public function hasZipDownload($files, $path, $file_name): bool|string
    {
        if ($files) {
            $zip = new ZipArchive;
            $zipPath = $path . $file_name;
            if ($zip->open(Storage::path($zipPath), ZipArchive::CREATE) === TRUE) {
                foreach ($files as $file) {
                    if (Storage::disk()->exists($file)) {
                        $zip->addFile(Storage::path($file), basename($file));
                    }
                }
                $zip->close();
                return $zipPath;
            } else {
                return false;
            }
        }
        return false;
    }
    public function folderToZip($folder, &$zipFile, $exclusiveLength): void
    {
        $handle = opendir($folder);
        while (false !== $f = readdir($handle)) {
            if ($f != '.' && $f != '..') {
                $filePath = "$folder/$f";
                $localPath = substr($filePath, $exclusiveLength);
                if (is_file($filePath)) {
                    $zipFile->addFile($filePath, $localPath);
                } elseif (is_dir($filePath)) {
                    $zipFile->addEmptyDir($localPath);
                    $this->folderToZip($filePath, $zipFile, $exclusiveLength);
                }
            }
        }
        closedir($handle);
    }
    public function hasZipDownloadFolder($folder, $folderName, $path, $file_name): bool|string
    {
        if ($folder && Storage::disk()->exists($folder)) {
            $zip = new ZipArchive;
            $zipPath = $path . $file_name;
            if ($zip->open(Storage::path($zipPath), ZipArchive::CREATE) === TRUE) {
                $folder = Storage::path($folder);
                $pathInfo = pathInfo($folder);
                $parentPath = $pathInfo['dirname'];
                $zip->addEmptyDir($folderName);
                $this->folderToZip($folder . '/', $zip, strlen("$parentPath/"));
                //$zip->addGlob(Storage::path($folder.'/').'*.*', GLOB_BRACE, ['add_path' => $folderName.'/', 'remove_all_path' => TRUE]);
                $zip->close();
                return $zipPath;
            } else {
                return false;
            }
        }
        return false;
    }
    public function hasZipDownloadFolders($folders, $path, $file_name): bool|string
    {
        if ($folders) {
            $zip = new ZipArchive;
            $zipPath = $path . $file_name;
            if ($zip->open(Storage::path($zipPath), ZipArchive::CREATE) === TRUE) {
                foreach ($folders as $folderName => $folderPath) {
                    if (Storage::disk()->exists($folderPath)) {
                        $folder = Storage::path($folderPath);
                        $pathInfo = pathInfo($folder);
                        $parentPath = $pathInfo['dirname'];
                        $zip->addEmptyDir($folderName);
                        $this->folderToZip($folder . '/', $zip, strlen("$parentPath/"));

                        //$zip->addGlob(Storage::path($folderPath.'/').'*.*', GLOB_BRACE, ['add_path' => $folderName.'/', 'remove_all_path' => TRUE]);
                    }
                }
                $zip->close();
                return $zipPath;
            } else {
                return false;
            }
        }
        return false;
    }
    public function imageUploader($file, bool $checkPermission = true): static
    {
        if ($checkPermission && !Helpers::canUploadFileByTaxonomy($this->taxonomy)) {
            throw new \Exception('Bạn không có quyền upload file loại này.');
        }

        if (!empty($file)) {
            $this->setFileName($file->getClientOriginalName());
            if (in_array($this->file_ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $this->file_size = $file->getSize();
                $this->setFileMime();
                if (Storage::disk()->putFileAs($this->file_path, $file, $this->full_name)) {
                    $this->file_url = Storage::disk()->url($this->file_path);
                    $this->status = true;
                }
            }
        }

        return $this;
    }
    public function setFileMime(): void
    {
        $mime_types = array(
            'doc' => 'application/msword',
            'dot' => 'application/msword',
            'pdf' => 'application/pdf',
            'xls' => 'application/vnd.ms-excel',
            'xlm' => 'application/vnd.ms-excel',
            'xla' => 'application/vnd.ms-excel',
            'xlc' => 'application/vnd.ms-excel',
            'xlt' => 'application/vnd.ms-excel',
            'xlw' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pps' => 'application/vnd.ms-powerpoint',
            'pot' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
            '7z' => 'application/x-7z-compressed',
            'rar' => 'application/x-rar-compressed',
            'zip' => 'application/zip',
            'm4a' => 'audio/mp4',
            'mp4a' => 'audio/mp4',
            'mp2' => 'audio/mpeg',
            'mp2a' => 'audio/mpeg',
            'mp3' => 'audio/mpeg',
            'm2a' => 'audio/mpeg',
            'm3a' => 'audio/mpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'jpe' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'txt' => 'text/plain',
            'text' => 'text/plain',
            'mp4' => 'video/mp4',
            'mp4v' => 'video/mp4',
            'mpg4' => 'video/mp4',
            'mpeg' => 'video/mpeg',
            'mpg' => 'video/mpeg',
            'mpe' => 'video/mpeg',
            'm1v' => 'video/mpeg',
            'm2v' => 'video/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            'avi' => 'video/x-msvideo',
            'movie' => 'video/x-sgi-movie'
        );
        if (array_key_exists($this->file_ext, $mime_types)) {
            $this->file_mime = $mime_types[$this->file_ext];
        } else {
            $this->file_mime = ''; //'application/octet-stream';
        }
    }
    public function imageUploaderByFile($file, $prefix = ''): static
    {
        if (!empty($file)) {
            $this->setFileName($file['name'], $prefix);
            if (in_array($this->file_ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $this->file_size = $file['size'];
                $this->setFileMime();
                if (Storage::disk()->putFileAs($this->file_path, $file['tmp_name'], $this->full_name)) {
                    $this->file_url = Storage::disk()->url($this->file_path);
                    $this->image = Image::read(Storage::disk()->path($this->file_path . "/" . $this->full_name));
                    $this->image_size = ['width' => $this->image->width(), 'height' => $this->image->height()];
                    $this->status = true;
                }
            }
        }
        return $this;
    }
    public function editorImageUploaderByFile($file, $prefix = ''): static
    {
        if (!empty($file)) {
            $this->setFileName($file['name'], $prefix);
            if (in_array($this->file_ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $this->file_size = $file['size'];
                $this->setFileMime();
                if (Storage::disk()->putFileAs($this->file_path, $file['tmp_name'], $this->full_name)) {
                    $this->file_url = Storage::disk()->url($this->file_path);
                    $this->image = Image::read(Storage::disk()->path($this->file_path . "/" . $this->full_name));
                    $this->status = true;
                    if ($this->image->width() > 1600) {
                        $this->image->scaleDown(1600)->save();
                    }
                    $this->image_size = ['width' => $this->image->width(), 'height' => $this->image->width()];
                }
            }
        }
        return $this;
    }
    public function imageUploaderByBase64($code, $type, $prefix = ''): static
    {
        $this->status = false;
        if (!empty($code) && in_array($type, ['data:image/png', 'image/png', 'image/jpeg', 'data:image/jpeg'])) {
            $this->file_ext = 'png';
            $this->file_name = $prefix . Helpers::generateRandomOnlyString(4) . '-' . time();
            $this->full_name = $this->file_name . '.' . $this->file_ext;
            $this->setFileMime();
            if (Storage::disk()->put($this->file_path . '/' . $this->full_name, base64_decode($code))) {
                $this->file_url = Storage::disk()->url($this->file_path);
                $img = Image::read(Storage::disk()->path($this->file_path . "/" . $this->full_name));
                $this->image_size = ['width' => $img->width(), 'height' => $img->height()];
                $this->status = true;
            }
        }
        return $this;
    }
    public function imageDownloadFromUrl($url, $suffix = '', $context = ''): static
    {
        $this->status = false;
        if (!empty($url)) {
            try {
                if ($context) {
                    $content = file_get_contents($url, null, $context);
                } else {
                    $content = file_get_contents($url);
                }
                if ($content) {
                    $this->setFileName($url, $suffix);
                    if (in_array($this->file_ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                        $this->setFileMime();
                        if (Storage::disk()->put($this->file_path . '/' . $this->full_name, $content)) {
                            $this->file_url = Storage::disk()->url($this->file_path);
                            $this->image = Image::read(Storage::disk()->path($this->file_path . "/" . $this->full_name));
                            $this->status = true;
                            if ($this->image->width() > 1600) {
                                $this->image->scaleDown(1600)->save();
                            }
                            $this->image_size = ['width' => $this->image->width(), 'height' => $this->image->width()];
                        }
                    }
                }
            } catch (Exception $error) {
                return $this;
            }
        }
        return $this;
    }
    public function imageDownloadFromUrl2($url, $suffix = ''): static
    {
        $this->status = false;
        if (!empty($url)) {
            try {
                $context = stream_context_create(
                    array(
                        'http' => array(
                            'follow_location' => false
                        ),
                        'ssl' => array(
                            "verify_peer" => false,
                            "verify_peer_name" => false,
                        ),
                    )
                );
                $content = file_get_contents($url, null, $context);
                if ($content) {
                    $this->setFileName($url, $suffix);
                    if (in_array($this->file_ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                        $this->setFileMime();
                        if (Storage::disk()->put($this->file_path . '/' . $this->full_name, $content)) {
                            $this->file_size = Storage::size($this->file_path . '/' . $this->full_name);
                            $this->file_url = Storage::disk()->url($this->file_path);
                            $this->status = true;
                        }
                    }
                }
            } catch (Exception $error) {
                return $this;
            }
        }
        return $this;
    }
    public function fileDownloadFromUrl($url, $suffix = '', $context = ''): static
    {
        $this->status = false;
        if (!empty($url)) {
            try {
                if ($context) {
                    $content = @file_get_contents($url, null, $context);
                } else {
                    $content = @file_get_contents($url);
                }
                if ($content) {
                    $this->setFileName($url, $suffix);
                    $this->setFileMime();
                    if ($this->file_mime) {
                        if (Storage::disk()->put($this->file_path . '/' . $this->full_name, $content)) {
                            $this->file_size = Storage::size($this->file_path . '/' . $this->full_name);
                            $this->file_url = Storage::disk()->url($this->file_path);
                            $this->status = true;
                        }
                    }
                }
            } catch (Exception $error) {
                return $this;
            }
        }
        return $this;
    }
    public function resize($width = null, $height = null): static
    {
        if ($this->status && in_array($this->file_ext, ['jpg', 'png', 'jpeg'])) {
            if (!$this->image) {
                $this->image = Image::read(Storage::disk()->path($this->file_path . '/' . $this->full_name));
            }
            if ($this->image) {
                $original_with = $this->image->width();
                $original_height = $this->image->height();
                if ($width && $height) {
                    $original_ratio = floatval($original_with / $original_height);
                    $ratio = floatval($width / $height);
                    if ($ratio > $original_ratio) {
                        $this->image->scaleDown($width, null)->crop($width, $height)->save();
                    } else {
                        $this->image->scaleDown(null, $height)->crop($width, $height)->save();
                    }
                    $this->image_size = ['width' => $this->image->width(), 'height' => $this->image->height()];
                } else {
                    if ($width) {
                        if ($original_with > $width) {
                            $this->image->scaleDown($width, null)->save();
                            $this->image_size = ['width' => $this->image->width(), 'height' => $this->image->height()];
                        }
                    } elseif ($height) {
                        if ($original_height > $height) {
                            $this->image->scaleDown(null, $height)->save();
                            $this->image_size = ['width' => $this->image->width(), 'height' => $this->image->height()];
                        }
                    }
                }
                $this->image = null;
            }
        }
        return $this;
    }
    public function createThumb($size, $suffix, $crop = 0): static
    {
        if ($size && $this->status && in_array($this->file_ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            if (!$this->image) {
                $this->image = Image::read(Storage::disk()->path($this->file_path . "/" . $this->full_name));
            }
            if ($this->image) {
                $original_with = $this->image->width();
                $original_height = $this->image->height();
                $original_ratio = floatval($original_with / $original_height);
                if ($crop == 0) {
                    $this->image
                        ->scaleDown(($size['width'] ? (int)$size['width'] : null), ($size['height'] ? (int)$size['height'] : null))
                        ->save(Storage::disk()->path($this->file_path . '/' . $this->file_name . $suffix . '.' . $this->file_ext));
                } else if ($crop == 1) {
                    $width = $size['width'] ? (int)$size['width'] : null;
                    $height = $size['height'] ? (int)$size['height'] : null;
                    if ($width !== null) {
                        if ($height !== null) {
                            $ratio = floatval($width / $height);
                            if ($ratio > $original_ratio) {
                                $this->image
                                    ->scaleDown($width)
                                    ->crop($width, $height, 0, 0, '#ffffff', 'center-center')
                                    ->save(Storage::disk()->path($this->file_path . '/' . $this->file_name . $suffix . '.' . $this->file_ext));
                            } else {
                                $this->image
                                    ->scaleDown(null, $height)
                                    ->crop($width, $height, 0, 0, '#ffffff', 'center-center')
                                    ->save(Storage::disk()->path($this->file_path . '/' . $this->file_name . $suffix . '.' . $this->file_ext));
                            }
                        } else {
                            $this->image
                                ->scaleDown($width, null)
                                ->save(Storage::disk()->path($this->file_path . '/' . $this->file_name . $suffix . '.' . $this->file_ext));
                        }
                    } else {
                        if ($height !== null) {
                            $this->image
                                ->scaleDown(null, $height)
                                ->save(Storage::disk()->path($this->file_path . '/' . $this->file_name . $suffix . '.' . $this->file_ext));
                        }
                    }
                } else {
                    $width = $size['width'] ? (int)$size['width'] : null;
                    $height = $size['height'] ? (int)$size['height'] : null;
                    if ($width !== null) {
                        if ($height !== null) {
                            $ratio = floatval($width / $height);
                            if ($ratio > $original_ratio) {
                                $this->image
                                    ->scaleDown(null, $height)
                                    ->crop($width, $height, 0, 0, '#ffffff', 'center-center')
                                    ->save(Storage::disk()->path($this->file_path . '/' . $this->file_name . $suffix . '.' . $this->file_ext));
                            } else {
                                $this->image
                                    ->scaleDown($width)
                                    ->crop($width, $height, 0, 0, '#ffffff', 'center-center')
                                    ->save(Storage::disk()->path($this->file_path . '/' . $this->file_name . $suffix . '.' . $this->file_ext));
                            }
                        } else {
                            $this->image
                                ->scaleDown($width)
                                ->save(Storage::disk()->path($this->file_path . '/' . $this->file_name . $suffix . '.' . $this->file_ext));
                        }
                    } else {
                        if ($height !== null) {
                            $this->image
                                ->scaleDown(null, $height)
                                ->save(Storage::disk()->path($this->file_path . '/' . $this->file_name . $suffix . '.' . $this->file_ext));
                        }
                    }
                }
            }
        }
        return $this;
    }
    public function destroyImage(): static
    {
        $this->image = null;
        return $this;
    }
    public function uploader($file, bool $checkPermission = true): static
    {
        if ($checkPermission && !Helpers::canUploadFileByTaxonomy($this->taxonomy)) {
            throw new \Exception('Bạn không có quyền upload file loại này.');
        }

        if (!empty($file)) {
            $this->setFileName($file->getClientOriginalName());
            $this->file_size = $file->getSize();
            $this->setFileMime();
            if ($this->file_mime) {
                if (Storage::disk()->putFileAs($this->file_path, $file, $this->full_name)) {
                    $this->file_url = Storage::disk()->url($this->file_path);
                    $this->status = true;
                }
            }
        }
        return $this;
    }
    public function uploaderByFile($file, $prefix = ''): static
    {
        if (!empty($file)) {
            $this->setFileName($file['name'], $prefix);
            $this->file_size = $file['size'];
            $this->setFileMime();
            if ($this->file_mime) {
                if (Storage::disk()->putFileAs($this->file_path, $file['tmp_name'], $this->full_name)) {
                    $this->file_url = Storage::disk()->url($this->file_path);
                    $this->status = true;
                }
            }
        }
        return $this;
    }
    public function uploaderByFileBySuffix($file, $suffix = ''): static
    {
        if (!empty($file)) {
            $this->setFileNameBySuffix($file['name'], $suffix);
            $this->file_size = $file['size'];
            $this->setFileMime();
            if ($this->file_mime) {
                if (Storage::disk()->putFileAs($this->file_path, $file['tmp_name'], $this->full_name)) {
                    $this->file_url = Storage::disk()->url($this->file_path);
                    $this->status = true;
                }
            }
        }
        return $this;
    }
    public function uploaderByFile2($file, $suffix = ''): static
    {
        if (!empty($file)) {
            $this->setFileName($file['name'], $suffix);
            $this->file_size = $file['size'];
            $this->setFileMime();
            if ($this->file_mime) {
                if (Storage::disk()->putFileAs($this->file_path, $file['tmp_name'], $this->full_name)) {
                    $this->file_url = Storage::disk()->url($this->file_path);
                    $this->status = true;
                }
            }
        }
        return $this;
    }
    public function vgcaSignFile($file, $oldFile): bool
    {
        if (!empty($file)) {
            if (Storage::disk()->putFileAs($oldFile->file_path, $file['tmp_name'], $oldFile->full_name)) {
                return true;
            }
        }
        return false;
    }
    public function signature($pdfFile, $signFile, $position, $offset = []): bool|array
    {
        if (Storage::disk()->exists($pdfFile->file_path . '/' . $pdfFile->full_name) && Storage::disk()->exists($signFile->file_path . '/' . $signFile->full_name)) {
            $fpdf = new Fpdi();
            $pageCount = $fpdf->setSourceFile(Storage::path($pdfFile->file_path . '/' . $pdfFile->full_name));
            for ($i = 1; $i <= $pageCount; $i++) {
                $tplIdx = $fpdf->importPage($i);
                $fpdf->AddPage();
                $fpdf->useTemplate($tplIdx);
            }
            $pageWidth = $fpdf->GetPageWidth();
            $pageHeight = $fpdf->GetPageHeight();
            $signFileWidth = 3;
            $signFileHeight = 2;
            if (!empty($signFile->image_size)) {
                $size = (int)$offset['size'];
                if (!empty($signFile->image_size->width)) {
                    $signFileWidth = (($signFile->image_size->width * $size) / 100) / 72 * 2.54; //Centimeters = Pixel / PPI * 2.54
                }
                if (!empty($signFile->image_size->height)) {
                    $signFileHeight = (($signFile->image_size->height * $size) / 100) / 72 * 2.54; //Centimeters = Pixel / PPI * 2.54
                }
            }
            $params = [
                'src' => Storage::path($signFile->file_path . '/' . $signFile->full_name),
                'x' => null,
                'y' => null,
                'w' => $signFileWidth,
                'h' => $signFileHeight
            ];

            if (is_array($position)) {
                if (!empty($position['x']))
                    $params['x'] = $position['x'];
                if (!empty($position['y']))
                    $params['y'] = $position['y'];
            } else {
                if (!empty($offset['x']))
                    $params['x'] = $offset['x'];
                else
                    $params['x'] = 0;
                if (!empty($offset['y']))
                    $params['y'] = $offset['y'];
                else
                    $params['y'] = 0;

                switch ($position) {
                    case 'top-left':
                        $params['y'] = 2 + $params['y'];
                        $params['x'] = 3 + $params['x'];
                        break;
                    case 'top-center':
                        $params['y'] = 2 + $params['y'];
                        $params['x'] = (($pageWidth / 2) - ($signFileWidth / 2))  + $params['x'];
                        break;
                    case 'top-right':
                        $params['y'] = 2 + $params['y'];
                        $params['x'] = ($pageWidth - 2 - $signFileWidth) + $params['x'];
                        break;
                    case 'middle-left':
                        $params['y'] = (($pageHeight / 2) - ($signFileHeight / 2))  + $params['y'];
                        $params['x'] = 3 + $params['x'];
                        break;
                    case 'middle-center':
                        $params['y'] = (($pageHeight / 2) - ($signFileHeight / 2))  + $params['y'];
                        $params['x'] = (($pageWidth / 2) - ($signFileWidth / 2))  + $params['x'];
                        break;
                    case 'middle-right':
                        $params['y'] = (($pageHeight / 2) - ($signFileHeight / 2))  + $params['y'];
                        $params['x'] = ($pageWidth - 2 - $signFileWidth) + $params['x'];
                        break;
                    case 'bottom-left':
                        $params['y'] = ($pageHeight - 2 - $signFileHeight) + $params['y'];
                        $params['x'] = 3 + $params['x'];
                        break;
                    case 'bottom-center':
                        $params['y'] = ($pageHeight - 2 - $signFileHeight) + $params['y'];
                        $params['x'] = (($pageWidth / 2) - ($signFileWidth / 2))  + $params['x'];

                        break;
                    case 'bottom-right':
                        $params['y'] = ($pageHeight - 2 - $signFileHeight) + $params['y'];
                        $params['x'] = ($pageWidth - 2 - $signFileWidth) + $params['x'];
                        break;
                }
            }
            $fpdf->Image($params['src'], $params['x'], $params['y'], $params['w'], $params['h']);
            $fpdf->Output('F', Storage::path($pdfFile->file_path . '/' . $pdfFile->full_name));
            return $params;
        }
        return false;
    }
    public function signature2($pdfFile, $signFile, $options, $portrait = 1): bool|array
    {
        if (Storage::disk()->exists($pdfFile->file_path . '/' . $pdfFile->full_name) && Storage::disk()->exists($signFile->file_path . '/' . $signFile->full_name)) {
            $fpdf = new Fpdi();
            $pageCount = $fpdf->setSourceFile(Storage::path($pdfFile->file_path . '/' . $pdfFile->full_name));
            $params = [];
            if (!empty($options['pages'])) {
                $pages = Helpers::ArrInteger($options['pages']);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $tplIdx = $fpdf->importPage($i);
                    if ($portrait == 1) {
                        $fpdf->AddPage();
                    } else {
                        $fpdf->AddPage('L');
                    }
                    $fpdf->useTemplate($tplIdx);
                    if (in_array($i, $pages)) {
                        if (!$params) {
                            $pageWidth = $fpdf->GetPageWidth();
                            $ratio = $options['canvas_width'] / $pageWidth; // (px / mm)
                            $params = [
                                'src' => Storage::path($signFile->file_path . '/' . $signFile->full_name),
                                'x' => $options['sign_x'] / $ratio,
                                'y' => $options['sign_y'] / $ratio,
                                'w' => $options['sign_width'] / $ratio,
                                'h' => $options['sign_height'] / $ratio
                            ];
                        }
                        $fpdf->Image($params['src'], $params['x'], $params['y'], $params['w'], $params['h']);
                    }
                }
            } else {
                for ($i = 1; $i <= $pageCount; $i++) {
                    $tplIdx = $fpdf->importPage($i);
                    if ($portrait == 1) {
                        $fpdf->AddPage();
                    } else {
                        $fpdf->AddPage('L');
                    }
                    $fpdf->useTemplate($tplIdx);
                }
                $pageWidth = $fpdf->GetPageWidth();
                $ratio = $options['canvas_width'] / $pageWidth; // (px / mm)
                $params = [
                    'src' => Storage::path($signFile->file_path . '/' . $signFile->full_name),
                    'x' => $options['sign_x'] / $ratio,
                    'y' => $options['sign_y'] / $ratio,
                    'w' => $options['sign_width'] / $ratio,
                    'h' => $options['sign_height'] / $ratio
                ];
                $fpdf->Image($params['src'], $params['x'], $params['y'], $params['w'], $params['h']);
            }
            $fpdf->Output('F', Storage::path($pdfFile->file_path . '/' . $pdfFile->full_name));
            return $params;
        }
        return false;
    }
    public function destroyFileByPath($parts): bool
    {
        if ($parts) {
            $user = Auth::user();
            if (!$user) {
                return false;
            }
            if (!$user->hasAnyRole(['ADMIN', 'LIBRARIAN', 'SUPER_ADMIN', 'SUPPORTER'])) {
                $filePath = $parts['dirname'] . '/' . $parts['basename'];
                $file = DB::table(FileUpload::$tableName)
                    ->where('file_path', 'like', '%' . $filePath)
                    ->where('user_id', $user->id)
                    ->first();

                if (!$file) {
                    return false;
                }
            }

            if (Storage::disk()->exists($parts['dirname'] . '/' . $parts['basename'])) {
                Storage::disk()->delete($parts['dirname'] . '/' . $parts['basename']);
                if (Storage::disk()->exists($parts['dirname'] . '/' . $parts['filename'] . '_xs.' . $parts['extension'])) {
                    Storage::disk()->delete($parts['dirname'] . '/' . $parts['filename'] . '_xs.' . $parts['extension']);
                }
                if (Storage::disk()->exists($parts['dirname'] . '/' . $parts['filename'] . '_md.' . $parts['extension'])) {
                    Storage::disk()->delete($parts['dirname'] . '/' . $parts['filename'] . '_md.' . $parts['extension']);
                }
            }
            return true;
        }
        return false;
    }
    public static function destroy($file): bool
    {
        if ($file) {
            $user = Auth::user();
            if (!$user) {
                return false;
            }

            if (!$user->hasAnyRole(['ADMIN', 'LIBRARIAN', 'SUPER_ADMIN', 'SUPPORTER'])) {
                if ($file->user_id != $user->id) {
                    return false;
                }
            }

            if (Storage::disk()->exists($file->file_path . '/' . $file->file_name . '.' . $file->file_ext)) {
                Storage::disk()->delete($file->file_path . '/' . $file->file_name . '.' . $file->file_ext);
                if (Storage::disk()->exists($file->file_path . '/' . $file->file_name . '_xs.' . $file->file_ext)) {
                    Storage::disk()->delete($file->file_path . '/' . $file->file_name . '_xs.' . $file->file_ext);
                }
                if (Storage::disk()->exists($file->file_path . '/' . $file->file_name . '_md.' . $file->file_ext)) {
                    Storage::disk()->delete($file->file_path . '/' . $file->file_name . '_md.' . $file->file_ext);
                }
            }
            return true;
        }
        return false;
    }
    public static function deleteFile($file): bool
    {
        if ($file) {
            $user = Auth::user();
            if (!$user) {
                return false;
            }

            if (!$user->hasAnyRole(['ADMIN', 'LIBRARIAN', 'SUPER_ADMIN', 'SUPPORTER'])) {
                if ($file->user_id != $user->id) {
                    return false;
                }
            }

            if (Storage::disk()->exists($file->file_path . '/' . $file->file_name . '.' . $file->file_ext)) {
                Storage::disk()->delete($file->file_path . '/' . $file->file_name . '.' . $file->file_ext);
                if ($file->file_ext == 'jpg' || $file->file_ext == 'jpeg' || $file->file_ext == 'png') {
                    if (Storage::disk()->exists($file->file_path . '/' . $file->file_name . '_thumb.' . $file->file_ext)) {
                        Storage::disk()->delete($file->file_path . '/' . $file->file_name . '_thumb.' . $file->file_ext);
                    }
                    if (Storage::disk()->exists($file->file_path . '/' . $file->file_name . '_xs.' . $file->file_ext)) {
                        Storage::disk()->delete($file->file_path . '/' . $file->file_name . '_xs.' . $file->file_ext);
                    }
                    if (Storage::disk()->exists($file->file_path . '/' . $file->file_name . '_sm.' . $file->file_ext)) {
                        Storage::disk()->delete($file->file_path . '/' . $file->file_name . '_sm.' . $file->file_ext);
                    }
                    if (Storage::disk()->exists($file->file_path . '/' . $file->file_name . '_md.' . $file->file_ext)) {
                        Storage::disk()->delete($file->file_path . '/' . $file->file_name . '_md.' . $file->file_ext);
                    }
                } else if ($file->file_ext == 'docx') {
                    if (Storage::disk()->exists($file->file_path . '/' . $file->file_name . '.html')) {
                        Storage::disk()->delete($file->file_path . '/' . $file->file_name . '.html');
                    }
                    if (Storage::disk()->exists($file->file_path . '/' . $file->file_name . '.pdf')) {
                        Storage::disk()->delete($file->file_path . '/' . $file->file_name . '.pdf');
                    }
                }
            }
            return true;
        }
        return false;
    }
    public static function deleteAttachment($file): bool
    {
        if (!empty($file)) {
            if (Storage::disk()->exists($file->file_path . '/' . $file->full_name)) {
                Storage::disk()->delete($file->file_path . '/' . $file->full_name);
            }
            return true;
        }
        return false;
    }
    public static function deleteImage($file, $exs = []): bool
    {
        if ($file) {
            $user = Auth::user();
            if (!$user) {
                return false;
            }

            if (!$user->hasAnyRole(['ADMIN', 'LIBRARIAN', 'SUPER_ADMIN', 'SUPPORTER'])) {
                if ($file->user_id != $user->id) {
                    return false;
                }
            }

            if (Storage::disk()->exists($file->file_path . '/' . $file->file_name . '.' . $file->file_ext)) {
                Storage::disk()->delete($file->file_path . '/' . $file->file_name . '.' . $file->file_ext);
                if ($exs) {
                    foreach ($exs as $ex) {
                        if (Storage::disk()->exists($file->file_path . '/' . $file->file_name . $ex . '.' . $file->file_ext)) {
                            Storage::disk()->delete($file->file_path . '/' . $file->file_name . $ex . '.' . $file->file_ext);
                        }
                    }
                } else {
                    if (Storage::disk()->exists($file->file_path . '/' . $file->file_name . '_thumb.' . $file->file_ext)) {
                        Storage::disk()->delete($file->file_path . '/' . $file->file_name . '_thumb.' . $file->file_ext);
                    }
                    if (Storage::disk()->exists($file->file_path . '/' . $file->file_name . '_xs.' . $file->file_ext)) {
                        Storage::disk()->delete($file->file_path . '/' . $file->file_name . '_xs.' . $file->file_ext);
                    }
                    if (Storage::disk()->exists($file->file_path . '/' . $file->file_name . '_md.' . $file->file_ext)) {
                        Storage::disk()->delete($file->file_path . '/' . $file->file_name . '_md.' . $file->file_ext);
                    }
                    if (Storage::disk()->exists($file->file_path . '/' . $file->file_name . '_lg.' . $file->file_ext)) {
                        Storage::disk()->delete($file->file_path . '/' . $file->file_name . '_lg.' . $file->file_ext);
                    }
                }
            }
            return true;
        }
        return false;
    }
    public static function fileDelete($filePath): bool
    {
        if (Storage::disk()->exists($filePath)) {
            if (!Storage::directoryExists($filePath) && Storage::fileExists($filePath)) {
                return Storage::disk()->delete($filePath);
            } else {
                return false;
            }
        }
        return false;
    }
    public static function directoryDelete($filePath): bool
    {
        if (Storage::disk()->exists($filePath)) {
            if (Storage::directoryExists($filePath) && !Storage::fileExists($filePath)) {
                return Storage::disk()->delete($filePath);
            } else {
                return false;
            }
        }
        return false;
    }

    public static function copyFile($system, $file, $fromCode, $toCode): bool
    {
        if ($file) {
            if (Storage::disk()->exists($file->file_path . '/' . $file->full_name)) {
                $toFile = $file;
                $toFile->file_path = str_replace($fromCode, $toCode, $toFile->file_path);
                $toFile->file_url = str_replace($fromCode, $toCode, $toFile->file_url);
                Storage::disk()->copy($file->file_path . '/' . $file->full_name, $toFile->file_path . '/' . $toFile->full_name);
                return true;
            }
        }
        return false;
    }
    public function getListFolders($recursive = false): array
    {
        return Storage::disk()->directories('', $recursive);
    }

    public function getAllFiles($subPath = '/'): array
    {
        return Storage::disk()->allFiles($subPath);
    }
    public static function pdfConverter($pdf_path, $pdf_name, $ext)
    {
        $fullPath = $pdf_path . '/' . $pdf_name . '.' . $ext;
        if (Storage::disk()->exists($fullPath)) {
            if (!Storage::directoryExists($fullPath) && Storage::fileExists($fullPath)) {
                if ($ext == 'pdf') {
                    if (Storage::move($fullPath, $pdf_path . '/' . $pdf_name . '_g.' . $ext)) {
                        $pdfVersion = "1.4";
                        $gsCmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=$pdfVersion -dNOPAUSE -dBATCH -sOutputFile=" . Storage::path($pdf_path . '/' . $pdf_name . '.pdf') . " " . Storage::path($pdf_path . '/' . $pdf_name . '_g.pdf');
                        exec($gsCmd);
                        Storage::disk()->delete($pdf_path . '/' . $pdf_name . '_g.pdf');
                        return true;
                    }
                } else {
                    $converter = new OfficeConverter(Storage::path($fullPath));
                    if ($converter->convertTo($pdf_name . '_g.pdf')) {
                        if (Storage::disk()->delete($fullPath)) {
                            $pdfVersion = "1.4";
                            $gsCmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=$pdfVersion -dNOPAUSE -dBATCH -sOutputFile=" . Storage::path($pdf_path . '/' . $pdf_name . '.pdf') . " " . Storage::path($pdf_path . '/' . $pdf_name . '_g.pdf');
                            exec($gsCmd);
                            Storage::disk()->delete($pdf_path . '/' . $pdf_name . '_g.pdf');
                            return true;
                        }
                    }
                }
            } else {
                return false;
            }
        }
    }
    public static function docToPdf($pdf_path, $pdf_name, $ext)
    {
        $fullPath = $pdf_path . '/' . $pdf_name . '.' . $ext;
        if (Storage::disk()->exists($fullPath)) {
            if (!Storage::directoryExists($fullPath) && Storage::fileExists($fullPath)) {
                if ($ext == 'pdf') {
                    if (Storage::move($fullPath, $pdf_path . '/' . $pdf_name . '_g.' . $ext)) {
                        $pdfVersion = "1.4";
                        $gsCmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=$pdfVersion -dNOPAUSE -dBATCH -sOutputFile=" . Storage::path($pdf_path . '/' . $pdf_name . '.pdf') . " " . Storage::path($pdf_path . '/' . $pdf_name . '_g.pdf');
                        exec($gsCmd);
                        Storage::disk()->delete($pdf_path . '/' . $pdf_name . '_g.pdf');
                        return true;
                    }
                } else {
                    $converter = new OfficeConverter(Storage::path($fullPath));
                    if ($converter->convertTo($pdf_name . '_g.pdf')) {
                        if (Storage::disk()->delete($fullPath)) {
                            $pdfVersion = "1.4";
                            $gsCmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=$pdfVersion -dNOPAUSE -dBATCH -sOutputFile=" . Storage::path($pdf_path . '/' . $pdf_name . '.pdf') . " " . Storage::path($pdf_path . '/' . $pdf_name . '_g.pdf');
                            exec($gsCmd);
                            Storage::disk()->delete($pdf_path . '/' . $pdf_name . '_g.pdf');
                            return true;
                        }
                    }
                }
            } else {
                return false;
            }
        }
    }
}
