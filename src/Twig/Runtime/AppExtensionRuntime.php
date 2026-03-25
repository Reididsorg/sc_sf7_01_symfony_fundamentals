<?php

namespace App\Twig\Runtime;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Extension\RuntimeExtensionInterface;

class AppExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly CacheInterface $issLocationPool,
    ) {
    }

    public function getIssLocationData()
    {
        return $this->issLocationPool->get('iss_location_data', function (): array {
            // La requete http n'est effectuée que si le cache ('iss_location_data' n'existe pas ou est expiré.
            $response = $this->client->request('GET', 'https://api.wheretheiss.at/v1/satellites/25544');

            // Si un cache est récupéré, la réponse en cache est directement renvoyée.
            return $response->toArray();
        });
    }
}
