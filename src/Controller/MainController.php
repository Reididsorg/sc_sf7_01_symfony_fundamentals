<?php

namespace App\Controller;

use App\Repository\StarshipRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function homepage(StarshipRepository $starshipRepository, HttpClientInterface $client, CacheInterface $issLocationPool): Response
    {
        $ships = $starshipRepository->findAll();
        $myShip = $ships[array_rand($ships)];

        $issData = $issLocationPool->get('iss_location_data', function (ItemInterface $item) use ($client): array {
            // Le cache 'iss_location_data' situé par défaut dans var/cache/dev/pools/app expire après 200 secondes.
//            $item->expiresAfter(200);

            // La requete http n'est effectuée que si le cache ('iss_location_data' n'existe pas ou est expiré.
            $response = $client->request('GET', 'https://api.wheretheiss.at/v1/satellites/25544');

            // Si un cache est récupéré, la réponse en cache est directement renvoyée.
            return $response->toArray();
        });

        return $this->render('main/homepage.html.twig', [
            'myShip' => $myShip,
            'ships' => $ships,
            'issData' => $issData,
        ]);
    }
}
