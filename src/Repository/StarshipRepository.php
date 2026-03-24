<?php

namespace App\Repository;

use App\Model\Starship;
use App\Model\StarshipStatusEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Command\DebugCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class StarshipRepository extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger,
//        #[Autowire(param: 'kernel.project_dir')]
//        private $projectDir,
//        #[Autowire(param: 'iss_location_cache_ttl')]
        private readonly int $issLocationCacheTtl,
        #[Autowire(service: 'twig.command.debug')]
        private readonly DebugCommand $twigDebugCommand,
    ) {
    }

    public function findAll(): array
    {
//        dump($this->projectDir);

        dump($this->issLocationCacheTtl); // Affiche : 10

        dump($this->twigDebugCommand);
        $output = new BufferedOutput();
        $this->twigDebugCommand->run(new ArrayInput([]), $output);
        dump($output);

        $this->logger->info('Starship collection retrieved');

        return [
            new Starship(
                1,
                'USS LeafyCruiser (NCC-0001)',
                'Garden',
                'Jean-Luc Pickles',
                StarshipStatusEnum::IN_PROGRESS,
                new \DateTimeImmutable('-1 day'),
            ),
            new Starship(
                2,
                'USS Espresso (NCC-1234-C)',
                'Latte',
                'James T. Quick!',
                StarshipStatusEnum::COMPLETED,
                new \DateTimeImmutable('-1 week'),
            ),
            new Starship(
                3,
                'USS Wanderlust (NCC-2024-W)',
                'Delta Tourist',
                'Kathryn Journeyway',
                StarshipStatusEnum::WAITING,
                new \DateTimeImmutable('-1 month'),
            ),
        ];
    }

    public function find(int $id): ?Starship
    {
        foreach ($this->findAll() as $starship) {
            if ($starship->getId() === $id) {
                return $starship;
            }
        }

        return null;
    }
}
