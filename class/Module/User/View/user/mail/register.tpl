Здравствуйте, {$user->fname|default:$user->lname|default:$user->login}

От Вашего имени была запрошена регистрация на сайте {$sitename}

Для того, чтобы подтвердить регистрацию, перейдите по слдующей ссылке:
{$siteurl}/user?userid={$user->id}&confirm={$user->confirm}

Если Вы не проходили регистрацию на сайте, удалите это письмо.
