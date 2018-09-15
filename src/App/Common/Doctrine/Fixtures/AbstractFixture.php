<?php

namespace App\Common\Doctrine\Fixtures;

use Kf\KitBundle\Doctrine\Fixtures\AbstractFixture as BaseFixture;
use Kf\KitBundle\Doctrine\Fixtures\ArrayFixturesProcessor;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Marino Di Clemente <kernelfolla@gmail.com>
 */
abstract class AbstractFixture extends BaseFixture
{
    const EXTENSION = '';

    abstract protected function parseFile($fileName);

    protected function getFixturesDir()
    {
        return parent::getFixturesDir().(static::EXTENSION == 'yml' ? '' : '/'.static::EXTENSION);
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        if ($this->isSupported()) {
            parent::load($manager);
        }
    }

    protected function isSupported()
    {
        $modes = $this->container->getParameter('app.fixtures.modes');
        if (empty($modes)) {
            $modes = ['yml', 'csv'];
        }

        return in_array(static::EXTENSION, $modes) && file_exists($this->getFileName());
    }

    protected function processFixtures($class, $fileName)
    {
        if (!file_exists($fileName)) {
            throw new \Exception($fileName.' fixtures file not found');
        }
        $data = $this->parseFile($fileName);
        if (!isset($data['class'])) {
            $data['class'] = $class;
        }
        $obj = new ArrayFixturesProcessor($this);

        return $obj->execute($data);
    }

    protected function getYamlFileName()
    {
        return $this->getFileName();
    }

    protected function getFileName()
    {
        $x = $this->getEntityClass();
        $x = trim(strtolower(str_replace("Bundle\\Entity\\", '_', substr($x, strpos($x, "\\")))), "\\");

        return $this->getFixturesDir()."/$x.".static::EXTENSION;
    }

    public function getFile($name)
    {
        $filename = $name;
        $path = $this->getFixturesDir().'/media/';
        $fullname = $path.$filename;

        return file_get_contents($fullname);
    }

    public function makeOrDownloadFile($name, $url)
    {
        $filename = $name;
        //here i check if file exists, if don't exists i try to download
        $path = realpath($this->getFixturesDir().'/..'.'/media').'/downloaded/';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $fullname = $path.$name;

        if (file_exists($fullname)) {
            // pass
        } else {
            echo 'Download: '.$url."\n";
            file_put_contents($fullname, file_get_contents($url));
        }

        return new \Symfony\Component\HttpFoundation\File\UploadedFile(
            $fullname, $filename,
            finfo_file(finfo_open(FILEINFO_MIME_TYPE), $fullname),
            filesize($fullname),
            null
        );
    }
}