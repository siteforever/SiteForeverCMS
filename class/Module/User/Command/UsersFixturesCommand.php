<?php
/**
 * This file is part of the @package@.
 *
 * @author : Nikolay Ermin <nikolay.ermin@sperasoft.com>
 * @version: @version@
 */


namespace Module\User\Command;

use Module\User\Exception\UserException;
use Module\User\Model\UserModel;
use Module\User\Object\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UsersFixturesCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('fixture:users')
            ->setDescription('Load users into database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->createUser('user1@example.com', 'user1', '123456789', USER_USER, $output);
        $this->createUser('admin1@example.com', 'admin1', '123456789', USER_ADMIN, $output);
    }

    /**
     * Will create new user
     *
     * @param                 $email
     * @param                 $login
     * @param                 $password
     * @param                 $permission
     * @param OutputInterface $output
     *
     * @throws \Throwable
     */
    private function createUser($email, $login, $password, $permission, OutputInterface $output)
    {
        /** @var UserModel $userModel */
        $userModel = $this->getContainer()->get('data.manager')->getModel('User');

        /** @var User $user */
        $user = $userModel->createObject();
        $user->email = $email;
        $user->login = $login;
        $user->password = $password;
        try {
            if ($userModel->register($user, $permission, true)) {
                $output->writeln(sprintf('<info>%s registered successful</info>', $login));
            }
        } catch (UserException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }
}
