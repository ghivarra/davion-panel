<?php

namespace App\Controllers\Assets;

use App\Controllers\BaseController;
use Config\Services;

class ImageController extends BaseController
{
    protected $allowedSize = [];

    //================================================================================================

    public function __construct()
    {
        // check if session is running and close it
        if (session_status() === PHP_SESSION_ACTIVE)
        {
            session_write_close();
        }

        // set allowed sizes
        $this->allowedSize = explode(',', $_ENV['IMAGE_ALLOWED_SIZE']);
    }
    
    //================================================================================================

    protected function fallback(string $etags): string
    {
        $fallbackImagePath = IMAGEPATH . 'no-image.png';

        // return fallback image
        return $this->render($fallbackImagePath, "{$etags}_ImageNotFound");
    }

    //================================================================================================
    
    protected function render(string $path, string $etags): string
    {
        // set header and cache
        $this->response->setCache(['max-age' => 300, 's-maxage' => 900, 'etag' => $etags])
                       ->setHeader('Content-Length', filesize($path))
                       ->setHeader('Vary', 'Accept-Encoding')
                       ->setContentType(mime_content_type($path));
        
        // get and return image
        return file_get_contents($path);
    }

    //================================================================================================

    public function serve(string ...$params): string
    {
        $realImagePath = IMAGEPATH . implode(DIRECTORY_SEPARATOR, $params);

        // get full requested path for etag purposes
        $etags = md5($this->request->getServer('REQUEST_URI'));
        
        // get query
        $priority      = $this->request->getGet('priority') ?? 'width';
        $requestedSize = [
            'width'  => $this->request->getGet('width') ?? $this->allowedSize[0],
            'height' => $this->request->getGet('height') ?? $this->allowedSize[0],
        ];

        // check if file not exist or wrong use of query
        // send image not available png
        if (!file_exists($realImagePath) OR !is_really_writable($realImagePath))
        {
            return $this->fallback($etags);
        }

        // original
        $original = $this->request->getGet('original');

        if (!empty($original))
        {
            return $this->render($realImagePath, $etags);
        }

        // check if resized image exist
        $imageExt  = strrchr($realImagePath, '.');
        $imageName = substr(strrchr($realImagePath, DIRECTORY_SEPARATOR), 1);

        // create resized path
        $resizedImageName = str_replace($imageExt, '', $imageName) . "@{$priority}_{$requestedSize[$priority]}" . $imageExt;
        $resizedImagePath = str_replace($imageName, $resizedImageName, $realImagePath);

        // return if already resized
        if (file_exists($resizedImagePath))
        {
            return $this->render($resizedImagePath, $etags);
        }

        // start resizing
        Services::image()->withFile($realImagePath)
                         ->resize($requestedSize['width'], $requestedSize['height'], true, $priority)
                         ->save($resizedImagePath);

        // return
        return $this->render($resizedImagePath, $etags);
    }

    //================================================================================================
}
