<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ElevesTest extends WebTestCase
{

    public function testCreateEleves()
    {

        $client = static::createClient();

        $response = $client->request(
            'POST',
            '/api/eleve',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"nom":"testNom", 
                      "prenom":"testPrenom", 
                      "dateDeNaissance":"2021-06-17"}
            ');
        $this->assertResponseStatusCodeSame(201);

        $elevesId = json_decode($client->getResponse()->getContent(), true);

        return $elevesId['id'];

    }

    /**
     * @depends testCreateEleves
     */

    public function testEditEleves($elevesId)
    {

        $client = static::createClient();

        $response = $client->request(
            'PUT',
            '/api/eleve/'.$elevesId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '[{"nom":"UpdateNom", 
                      "prenom":"UpdatePrenom", 
                      "dateDeNaissance":"2021-06-21"}]
            ');

        $this->assertResponseStatusCodeSame(201);

        return $elevesId;

    }

    /**
     * @depends testEditEleves
     */

    public function testDeleteEleves($elevesId)
    {

        $client = static::createClient();

        $response = $client->request(
            'DELETE',
            '/api/eleve/'.$elevesId);

        $this->assertResponseStatusCodeSame(200);

    }



}
