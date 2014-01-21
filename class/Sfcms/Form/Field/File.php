<?php
/**
 * File upload field
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
namespace Sfcms\Form\Field;

use Sfcms\Form\FormFieldAbstract;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class File extends FormFieldAbstract
{
    protected $type = 'file';
    protected $mime;
    protected $size;
    protected $originalName;
    protected $uploadError;

    /**
     * @inheritdoc
     */
    public function setValue($value)
    {
        if (!$value instanceof UploadedFile) {
            throw new \InvalidArgumentException('Value must be `Upload File` type');
        }
        $this->value = $value;
        $this->originalName = $value->getClientOriginalName();
        $this->mime = $value->getClientMimeType();
        $this->size = $value->getClientSize();
        $this->uploadError = $value->getError();
        return $this;
    }

    protected function checkValue($value)
    {
        return true;
    }


    /**
     * @return mixed
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * @return mixed
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return mixed
     */
    public function getUploadError()
    {
        return $this->uploadError;
    }
}
