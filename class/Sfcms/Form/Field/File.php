<?php
/**
 * File upload field
 * @author Nikolay Ermin <nikolay@ermin.ru>
 * @link   http://siteforever.ru
 */
namespace Sfcms\Form\Field;

use Sfcms\Form\Field;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class File extends Field
{
    protected $_type = 'file';

    /**
     * @inheritdoc
     */
    public function setValue($value)
    {
        if (!$value instanceof UploadedFile) {
            throw new \InvalidArgumentException('Value must be `Upload File` type');
        }
        $this->_value = $value;
        return $this;
    }

}
