<?php
namespace PublicUHC\TeamspeakAuth\Commands;


use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanContainerCommand extends Command {

    protected function configure()
    {
        $this->setName('clean:container')
            ->setDescription('Cleans the cached container (to be ran after config changes)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $base_folder = 'cache/container';

        $output->writeln("<info>Removing folder $base_folder</info>");
        $directoryIterator = new RecursiveDirectoryIterator($base_folder, FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::CHILD_FIRST);
        foreach($iterator as $path) {
            $path->isDir() ? rmdir($path->getPathname()) : unlink($path->getPathname());
        }
        rmdir($base_folder);

        $output->writeln('<comment>Container cleared!</comment>');
    }
} 