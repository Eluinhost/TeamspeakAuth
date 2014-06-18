<?php
namespace PublicUHC\TeamspeakAuth\Commands;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class CleanCommand extends Command {

    private $base_path;

    public function __construct($base_path)
    {
        parent::__construct(null);
        $this->base_path = $base_path;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $base_folder = $this->base_path . $this->getFolder();

        $output->writeln("<info>Removing files at $base_folder</info>");
        $directoryIterator = new RecursiveDirectoryIterator($base_folder, FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::CHILD_FIRST);
        foreach($iterator as $path) {
            $path->isDir() ? rmdir($path->getPathname()) : unlink($path->getPathname());
        }
        rmdir($base_folder);

        $output->writeln('<comment>Files cleared!</comment>');
    }

    /**
     * @return String path to the folder to clean
     */
    protected abstract function getFolder();

} 