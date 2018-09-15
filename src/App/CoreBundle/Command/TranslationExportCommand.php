<?php

namespace App\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Util\XmlUtils;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class TranslationExportCommand extends ContainerAwareCommand
{
    protected $tmpEntities;

    private $translationsPath = 'app/Resources/translations/';

    private $exportPath = 'tmp';

    protected function configure()
    {
        $this
            ->setName('app:translation:export')
            ->setDescription('Export translation to file.')
            ->addArgument(
                'source_locale',
                InputArgument::OPTIONAL,
                "Which locale should we export the translation from? (Default 'en')"
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->getContainer()->get( 'kernel' )->getEnvironment() != 'dev') {
            exit('Cant run this script from production');
        }

        ini_set('memory_limit','1G');

        $sourceLocale = $input->getArgument('source_locale');
        if (!$sourceLocale) {
            $sourceLocale = 'en';
        }

        $finder = new Finder();
        $finder->files()->in($this->translationsPath);

        $uniqueDomains = [];
        foreach ($finder as $file) {

            $fileNameParts = explode('.', $file->getRelativePathname());
            if (!in_array($fileNameParts[0], $uniqueDomains)) {
                $uniqueDomains[] = $fileNameParts[0];
            }

        }

        $translations = [];
        foreach ($uniqueDomains as $uniqueDomain) {
            $fullPath = $this->translationsPath . $uniqueDomain . '.' . $sourceLocale . '.xliff';

            $dom = XmlUtils::loadFile($fullPath);

            foreach ($dom->getElementsByTagName('trans-unit') as $unit) {

                $target = $unit->getElementsByTagName('target')[0]->nodeValue;
                $source = $unit->getElementsByTagName('source')[0]->nodeValue;

                $translations[$uniqueDomain][] = [
                    'source' => $source,
                    'target' => $target,
                    'new_target' => ''
                ];

            }
        }

        $export = Yaml::dump($translations);
        file_put_contents($this->exportPath . '/translations_' . $sourceLocale . '.yml', $export);
    }

}