<?php
namespace App\Controller;

trait FileTrait
{

    /**
     * @return mixed
     */
    public function getUploadDir($path, $create = false)
    {
        $path = $this->getParameter('upload_dir') . '/' . $path;
        if ($create && !is_dir($path)) {
            $path = mkdir($path, 0777, true);
        }
        return $path;
    }
    
}