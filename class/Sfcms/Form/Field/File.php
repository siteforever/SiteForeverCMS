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
    protected $fileName;
    protected $originalName;
    protected $uploadError;
    /** @var UploadedFile */
    protected $value;

    /**
     * @inheritdoc
     */
    public function setValue($value)
    {
        $this->supportValue($value);
        /** @var UploadedFile $value */
        $this->value = $value;
        $this->fileName = $value->getFilename();
        $this->originalName = $value->getClientOriginalName();
        $this->mime = $value->getClientMimeType();
        $this->size = $value->getClientSize();
        $this->uploadError = $value->getError();

        return $this;
    }

    protected function supportValue($value)
    {
        if (!$value instanceof UploadedFile) {
            throw new \InvalidArgumentException('Value must be `Upload File` type. Check that enctype equals multipart/form-data');
        }
    }

    protected function getDefaultOptions()
    {
        $options = parent::getDefaultOptions();
        $options['mime'] = array();
        $options['size'] = array();
        $options['multiple'] = false;
        $options['formenctype'] = 'multipart/form-data';

        return $options;
    }

    protected function checkValue($value)
    {
        $this->supportValue($value);
        /** @var UploadedFile $value */
        $mime = $this->options['mime'];
        $size = $this->options['size'];

        if (!in_array($this->getMime(), $mime)) {
            $this->msg = array('Unsupported type %mime%', 'mime'=>$this->getMime());
            return false;
        }

        if (isset($size['max']) && $size['max'] < $this->getSize()) {
            $this->msg = array('File size should be no more than %size% Kb', '%size%'=>round($size['max'] / 1024));
            return false;
        }

        if (isset($size['min']) && $size['min'] > $this->getSize()) {
            $this->msg = array('File size should not be less than %size% Kb', '%size%'=>round($size['min'] / 1024));
            return false;
        }

        if ($this->value->isValid()) {
            return true;
        }

        return false;
    }

    /**
     * @param $dir
     * @param $name
     *
     * @return \Symfony\Component\HttpFoundation\File\File
     */
    public function moveTo($dir, $name = null)
    {
        return $this->value->move($dir, $name);
    }

    /**
     * @return mixed
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * @param mixed $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
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
