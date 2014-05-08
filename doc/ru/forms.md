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
