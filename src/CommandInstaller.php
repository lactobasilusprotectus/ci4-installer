<?php


namespace Codeigniter\Installer\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class CommandInstaller extends Command
{
    protected function configure()
    {
        $this->setName('install')
            ->setDescription('CI 4 install')
            ->addArgument('name', InputArgument::OPTIONAL);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
       $output->write('<info>Codeigniter 4</info>');

       sleep(2);

       $output->writeln('<info>Creating Codeigniter 4 App Starter</info>');

       sleep(2);

       $name = $input->getArgument('name');

       $directory = $name && $name !== '.' ? getcwd().'/'.$name : '.';

       $composer = $this->findComposer();

       $commands = [
           $composer . " create-project codeigniter4/appstarter  \"$directory\""
       ];

        if (PHP_OS_FAMILY != 'Windows'){
            $commands[] = "chmod 755 $directory";
        }

        if ((($process = $this->runCommands($commands, $input, $output))->isSuccessful())){
            $output->writeln(PHP_EOL.'<comment>CI 4 Framework Ready.</comment>');
        }

        return $process->getExitCode();
    }

    protected function findComposer(){
        $composerPath = getcwd() . '/composer.phar';

        if (file_exists($composerPath))
        {
            return '"'.PHP_BINARY.'" '.$composerPath;
        }

        return 'composer';
    }

    protected function runCommands($commands, InputInterface $input, OutputInterface $output){
        $process = Process::fromShellCommandline(implode(' && ', $commands), null, null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $output->writeln('Warning: '.$e->getMessage());
            }
        }

        $process->run(function ($type, $line) use ($output) {
            $output->write('    '.$line);
        });

        return $process;
    }
}
