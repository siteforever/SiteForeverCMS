#Формы

## Создание формы

    use Sfcms\Form\Form;

    $form = new Form([
        'name'      => 'login',
        'action'    => 'user/login',
        'fields'    => [
            'login'     => ['type'=>'text',    'label'=>'Login',   'required'],
            'password'  => ['type'=>'password','label'=>'Password',  'required'],
            'submit'    => ['type'=>'submit', 'value'=>'Enter'],
        ],
    ]);

## Та же форма в виде отдельного класса

    use Sfcms\Form\Form;

    class LoginForm extends Form
    {
        public function __construct()
        {
            return parent::__construct([
                   'name'      => 'login',
                   'action'    => 'user/login',
                   'fields'    => [
                       'login'     => ['type'=>'text',    'label'=>'Login',   'required'],
                       'password'  => ['type'=>'password','label'=>'Password',  'required'],
                       'submit'    => ['type'=>'submit', 'value'=>'Enter'],
                   ],
               ]);
        }
    }

## Обработка формы

    if ($form->handleRequest($this->request)) {
        if ($form->validate()) {
            $formData = $form->getData();
            $loginValue = $form->getChild('login')->getValue(); // long object style
            $loginValue = $form->login; // short object style
            $loginValue = $form['login']; // array style
        }
    }

## Отрисовка формы

Вызвать отрисовку формы можно следующим способом:

    $form->createView()->html();

В метод `html()` можно передать массив параметров:

    $form->createView()->html([hint=>true, buttons=>true, domain=>"messages", class=>"form-horizontal"]);

### Вывод формы в шаблоне

Для отрисовки формы из контроллера, нужно передать представление формы в шаблон:

    $this->render('controller.action', ['form'=>$form->createView()]);

А в шаблоне вызвать:

    {$form->html()}

### Кастомизация шаблона формы

Любую форму можно выводить не сразу полностью, а по отдельными полям:

    {form form=$form}
        {$form->htmlFieldWrapped('id')}
        {$form->htmlFieldWrapped('name')}
        {$form->htmlFieldWrapped('email')}
    {/form}

Либо, кастомизовать по своему вкусу:

    {include file="smarty/form_bs2.tpl"}

    {call form_start form=$form}
    <div class="control-group">
        {call form_label form=$form.login}
        {call form_input form=$form.login}
        {call form_errors form=$form.login}
    </div>
    <div class="control-group">
        {call form_label form=$form.password}
        {call form_input form=$form.password}
        {call form_errors form=$form.password}
    </div>
    <div class="control-group">
    {call form_input form=$form.submit}
    </div>
    {call form_end}

Если используется twitter bootstrap3, то можно указать шаблон:

    {include file="smarty/form_bs3.tpl"}

### Работа с полем файла

Определение формы:

``` php
use Sfcms\Form\Form;

class FeedbackForm extends Form
{
    public function __construct()
    {
        return parent::__construct([
               'name'      => 'feedback',
               'action'    => 'feedback',
               'enctype'   => 'multipart/form-data',
               'fields'    => [
                   'name'     => ['type'=>'text', 'label'=>'Name', 'required'],
                   'email'    => ['type'=>'text', 'label'=>'Email', 'filter' => 'email', 'required'],
                   'message'  => ['type'=>'textarea','label'=>'Message', 'required'],
                   'image' => [
                       'type'      => 'file',
                       'mime'      => ['image/png','image/jpeg','image/gif'],
                       'size'      => ['max' => 1024 * 1024],
                       'multiple'  => true,
                       'label'     => 'Attach image',
                   ],
                   'submit'    => ['type'=>'submit', 'value'=>'Enter'],
               ],
           ]);
    }
}
```

Обработка формы:

``` php
class FeedbackController

    $form = new FeedbackForm();

    if ($form->handleRequest($this->request)) {
        if ($form->validate()) {
            $location = $this->container->getParameter('root') . '/files/attachment';
            /** @var Sfcms\Form\Field\File $fileFiled */
            $fileFiled = $form->getChild('image');
            $fileFiled->moveTo($location);
            $message = $this->createMessage(
                $form->email,
                $this->container->getParameter('admin'),
                $this->t('Message from site') . ' :' . $form->title,
                $form->message
            );
            $message->attach(new \Swift_Attachment($location . '/' . $fileFiled->getOriginalName()));
            $this->sendMessage($message);

            $form->message->clear();
            $form->title->clear();
            $this->request->addFeedback('Your message was sent');
        }
    }

```
