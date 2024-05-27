<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Company\CustomerImport\Console\Command;

use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Company\CustomerImport\Model\Import\ImportFactory;

/**
 * Company Custoer Import Command
 */
class ImportCommand extends Command
{
    /**
     *  constant for profile argumet
     */
    const PROFILE_ARGUMENT = 'profile';

    /**
     *  constant for source argumet
     */
    const SOURCE_ARGUMENT = 'source';

    /**
     * @var ImportFactory $importFactory
     */
    private $importFactory;

    /**
     * Importcommand constructor
     * @param ImportFactory $importFactory
     */
    public function __construct(ImportFactory $importFactory)
    {
        $this->importFactory = $importFactory;
        parent::__construct();
    }

    /**
     * Configure method or customer import
     */
    protected function configure()
    {
        //set command name
        $this->setName('customer:import')
            ->setDescription('Import customers from a specified profile')
            ->addArgument(self::PROFILE_ARGUMENT, InputArgument::REQUIRED, 'Profile Name')
            ->addArgument(self::SOURCE_ARGUMENT, InputArgument::REQUIRED, 'Source File');
        parent::configure();
    }

    /**
     * Execute method for command
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $profile = $input->getArgument(self::PROFILE_ARGUMENT);
        $source = $input->getArgument(self::SOURCE_ARGUMENT);

        try {
            $import = $this->importFactory->create($profile);
            $import->import($source);
            $output->writeln('<info>Customers imported successfully.</info>');
            return Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Cli::RETURN_FAILURE;
        }
    }
}
