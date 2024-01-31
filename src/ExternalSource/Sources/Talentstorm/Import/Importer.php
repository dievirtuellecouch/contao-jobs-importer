<?php

namespace DVC\JobsImporter\ExternalSource\Sources\Talentstorm\Import;

use DVC\JobsImporter\ExternalSource\Sources\Talentstorm\Import\HttpClientFactory;

class Importer
{
    const ROUTE_LIST_ALL = 'https://api.talentstorm.de/api/v1/joboffers/basic';

    private $client;

    public function __construct(
        private HttpClientFactory $clientFactory,
    ) {
        $this->client = $clientFactory->getClient();
    }

    public function importJobsList(): ?array
    {
        try {
            $response = $this->client->request(
                'GET',
                self::ROUTE_LIST_ALL,
            );

            if ($response->getStatusCode() == 403) {
                return null;
            }

            if ($response->getStatusCode() != 200) {
                return null;
            }

            return $response->toArray();
        }
        catch (\Exception $e) {
        }

        return null;
    }
}
