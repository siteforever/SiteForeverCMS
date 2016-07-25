<?php
/**
 * Created by PhpStorm.
 * User: keltanas
 * Date: 23.07.16
 * Time: 2:23
 */

namespace Sfcms\Form\Exception;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ValidationException extends UnprocessableEntityHttpException
{
    private $errors;

    public function __construct($message, \Exception $previous = null, $code = Response::HTTP_UNPROCESSABLE_ENTITY)
    {
        if (is_array($message)) {
            $this->errors = $message;
            $message = 'Validation error';
        }
        parent::__construct($message, $previous, $code);
    }

    /**
     * @return array|null
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
