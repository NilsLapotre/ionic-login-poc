<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

class SimplonprodImportDbCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'simplonprod:import-db';

    /** @var string */
    private $login = 'simplonprod';

    /** @var string */
    private $localFolder = 'var/';

    /** @var SymfonyStyle */
    private $io;

    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var string */
    private $stagingUrl = 'https://backups.staging.simplon.space';

    /** @var string */
    private $marvinUrl = 'https://marvin.simplon.space/backups';

    /** @var string */
    private $server;

    /** @var string */
    private $projectName;

    /** @var string */
    private $env;

    /** @var string */
    private $url;

    protected function configure()
    {
        $this
            ->setDescription('Downloads a database on a server and import it into your local project.')
            ->addArgument('env', InputArgument::OPTIONAL, 'Environment')
        ;
    }

    /**
     * Initializes import settings.
     */
    private function init()
    {
        $yesterday = \date('l', \strtotime('-1 days'));

        $this->server = \strtok(\file_get_contents('.deploy/inventory_staging'), '.');
        $this->io->note('Found server: '.$this->server);

        $this->projectName = Yaml::parse(\file_get_contents('.deploy/vars/project.yml'))['project_name'];
        $this->io->note('Project name: '.$this->projectName);

        $this->env = $this->input->getArgument('env') ?? 'staging';
        $this->io->note('Environment: '.$this->env);

        $path = '/mysql/'.$yesterday.'/'.$this->projectName.'-'.$this->env;
        $this->url = $this->stagingUrl.$path.'.sql';

        if ('marvin' === $this->server) {
            $this->url = $this->marvinUrl.$path.'.gz';
        }

        $this->io->note('URL: '.$this->url);
    }

    /**
     * Checks if online file exists.
     */
    private function checkOnlineFile()
    {
        $helper = $this->getHelper('question');
        $question = new Question('What is the basic auth password?');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $password = $helper->ask($this->input, $this->output, $question);

        $ch = \curl_init();
        \curl_setopt($ch, CURLOPT_URL, $this->url);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        \curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        \curl_setopt($ch, CURLOPT_USERPWD, "$this->login:$password");
        $result = \curl_exec($ch);
        $status = \curl_getinfo($ch, CURLINFO_HTTP_CODE);
        \curl_close($ch);

        if (404 === $status) {
            $this->io->error('No backup found at this URL: '.$this->url);
            exit();
        }
        if (403 === $status || 401 === $status) {
            $this->io->error('Access error: username or password may be wrong.');
            exit();
        }

        return $result;
    }

    /**
     * Downloads file.
     *
     * @param string $url
     * @param string $result
     */
    private function downloadFile($url, $result)
    {
        $file = \fopen($url, 'w+');
        \fputs($file, $result);
        \fclose($file);
    }

    /**
     * Extracts gz file in folder.
     */
    private function extractGz()
    {
        $filename = $this->localFolder.$this->projectName.'-'.$this->env.'.gz';
        $buffer = 4096; // read 4kb at a time
        $outFilename = \str_replace('.gz', '.sql', $filename);
        $file = \gzopen($filename, 'rb');

        $sqlFile = \fopen($outFilename, 'wb');
        while (!\gzeof($file)) {
            \fwrite($sqlFile, \gzread($file, $buffer));
        }
        \fclose($sqlFile);
        \gzclose($file);

        return $outFilename;
    }

    /**
     * Import file into the database.
     *
     * @param string $file
     */
    private function importDb($file)
    {
        $command = $this->getApplication()->find('doctrine:database:import');
        $arguments = [
            'file' => $file,
        ];
        $dbInput = new ArrayInput($arguments);

        return $command->run($dbInput, $this->output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;

        $this->io = new SymfonyStyle($this->input, $this->output);

        $this->init();
        $result = $this->checkOnlineFile();

        $this->io->note('Download backup from: '.$this->url);

        $destination = $this->localFolder.$this->projectName.'-'.$this->env.('marvin' === $this->server ? '.gz' : '.sql');

        $this->downloadFile($destination, $result);

        if ('marvin' === $this->server) {
            $destination = $this->extractGz();
        }

        return $this->importDb($destination);
    }
}
