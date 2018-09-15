<?php

namespace App\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Util\XmlUtils;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Yaml\Yaml;

class TranslationImportCommand extends ContainerAwareCommand
{
    protected $tmpEntities;

    private $translationsPath = 'app/Resources/translations/';

    private $importPath = 'tmp';

    private $translatedArray;

    protected function configure()
    {
        // php app/console app:translation:import fr fr
        $this
            ->setName('app:translation:import')
            ->setDescription('Import translation to files.')
            ->addArgument(
                'target_locale',
                InputArgument::REQUIRED,
                "Which locale should we import the translation as?"
            )
            ->addArgument(
                'file_name',
                InputArgument::REQUIRED,
                "Name of the file to import."
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->getContainer()->get( 'kernel' )->getEnvironment() != 'dev') {
            exit('Cant run this script from production');
        }

        ini_set('memory_limit','1G');

        $targetLocale = $input->getArgument('target_locale');
        $fileName = $input->getArgument('file_name');

        $file = file_get_contents($this->importPath . '/' . $fileName . '.yml');

        $translations = Yaml::parse($file);

        if (!$translations) {
            $output->writeln('Error translating.');
            exit;
        }

        $this->translatedArray = $translations;

        $finder = new Finder();
        $finder->files()->in($this->translationsPath);
        $uniqueDomains = [];
        foreach ($finder as $file) {

            $fileNameParts = explode('.', $file->getRelativePathname());
            if (!in_array($fileNameParts[0], $uniqueDomains)) {
                $uniqueDomains[] = $fileNameParts[0];
            }

        }

        foreach ($uniqueDomains as $uniqueDomain) {
            $fullPath = $this->translationsPath . $uniqueDomain . '.' . $targetLocale . '.xliff';

            $dom = XmlUtils::loadFile($fullPath);

            foreach ($dom->getElementsByTagName('trans-unit') as $unit) {

                $source = $unit->getElementsByTagName('source')[0]->nodeValue;
                $translatedText = $this->getTranslationFromDomainAndSource($uniqueDomain, $source);
                if (!$translatedText)
                    continue;

                $output->writeln($unit->getElementsByTagName('target')[0]->nodeValue . ' translated to ' . $translatedText);

                $unit->getElementsByTagName('target')[0]->nodeValue = $translatedText;
                $output->writeln($source);
            }

            $dom->save($fullPath);

        }
    }

    private function getTranslationFromDomainAndSource($domain, $source) {
        foreach ($this->translatedArray[$domain] as $translation) {

            if (!array_key_exists('new_target', $this->translatedArray))
                continue;

            if ($translation['source'] == $source) {
                return $translation['new_target'];
            }
        }
    }

}