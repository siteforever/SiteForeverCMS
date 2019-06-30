<?php

namespace Sfcms\Form\Field;

use App;
use Sfcms\Form\FormFieldAbstract;
use Sfcms\Request;

/**
 * Поле каптчи
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
class Captcha extends FormFieldAbstract
{
    protected $type = 'captcha';
    protected $class = '';
    protected $datable = false;

    /**
     * Проверит значение на валидность типа
     * @param $value
     * @return boolean
     */
    protected function checkValue($value)
    {
        $request = $this->request;
        $key = App::cms()->getContainer()->getParameter('captcha.backend_key');
        $captchaCode = $request->request->get('g-recaptcha-response');

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query([
                    'secret' => $key,
                    'response' => $captchaCode,
                ]),
            ],
        ]);

        $result = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        App::cms()->getLogger()->debug('captcha result', ['context' => $context, 'result' => $result]);
        $decodedResult = json_decode($result, true);
        if (isset($decodedResult['success']) && $decodedResult['success']) {
            return true;
        }

        $this->msg = 'Code is not valid';
        return false;
    }
}
