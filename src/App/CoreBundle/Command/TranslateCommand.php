<?php

namespace App\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\ArrayInput;

class TranslateCommand extends ContainerAwareCommand
{
    protected $tmpEntities;

    protected function configure()
    {
        $this
            ->setName('app:translate')
            ->setDescription('Process translations to create translation files');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit','1G');

        $locales   = $this->getContainer()->getParameter('option_locales');
        $command   = $this->getApplication()->find('translation:extract');
        $arguments = array(
            'command'       => 'translation:extract',
            '--env'         => 'prod',
            '--config'      => 'app',
            '--keep'        => true,
            'locales'       => $locales,
            '--ignore-domain' => [ 'FOSUserBundle']
        );
        $input     = new ArrayInput($arguments);
        $command->run($input, $output);
    }

}