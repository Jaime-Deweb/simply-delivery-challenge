<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Item;

class ApiControllerTest extends WebTestCase
{
    protected $client;
    
    protected $entityManager;
    
    protected $apiKeyParameter = '?key=2y10zjW6f.KEuwpeRKpVPPDzHOXGaVfDUPE/WnPQjGbnhFP99JfBbCaDi';
    
    protected  $data = [
        [
            'id' => 5,
            'name' => 'Pizza',
            'price' => 1000,
        ],
        [
            'id' => 6,
            'name' => 'Hamburguer',
            'price' => 900,
        ]
    ];
    
    protected function setUp(): void
    {          
        $this->client = static::createClient();
        
        $kernel = $this->client->getKernel();
        
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }
    
    public function testAuthenticationFailure()
    {
        $this->client->request('GET', '/api/items');
        $this->assertResponseStatusCodeSame(401);   
    }
    
    public function testListItems(): void
    {                 
        $this->client->request('GET', '/api/items'.$this->apiKeyParameter);

        $this->assertResponseIsSuccessful();
        $this->assertEquals(json_encode($this->data), $this->client->getResponse()->getContent());
    }
    
    public function testAddItem()
    {
        $this->client->request('POST', '/api/item'.$this->apiKeyParameter,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode(['name' => 'Coca-Cola']),
        );
        
        $this->assertResponseStatusCodeSame(422);
        
        $this->client->request('POST', '/api/item'.$this->apiKeyParameter,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode(['name' => 'Coca-Cola', 'price' => 150]),            
        );
        
        $this->assertResponseIsSuccessful();
       
        $newItem = $this->entityManager->getRepository(Item::class)->findOneBy(
            ['name' => 'Coca-Cola']
        );
             
        $this->assertEquals('Coca-Cola', $newItem->getName());        
    }
      
    public function testUpdateItem()
    {
        $this->client->request('PUT', '/api/item/5'.$this->apiKeyParameter,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode([
                'name' => 'Wine',               
            ])
        );
        
        $this->assertResponseStatusCodeSame(422);
        
        $this->client->request('PUT', '/api/item/5'.$this->apiKeyParameter,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode([               
               'name' => 'Wine',
               'price' => '1200'
           ])
        );
        
        $this->assertResponseIsSuccessful();
        
        $item = $this->entityManager->getRepository(Item::class)->findOneBy(
            ['name' => 'Wine']
        );
        
        $this->assertEquals('Wine', $item->getName());
    }
    
    public function testDeleteItem()
    {
        $this->client->request('DELETE', '/api/item/99'.$this->apiKeyParameter);
        
        $this->assertResponseStatusCodeSame(404);
        
        $this->client->request('DELETE', '/api/item/6'.$this->apiKeyParameter);
        
        $this->assertResponseIsSuccessful();
        $this->assertEquals(json_encode([
            'status' => 200,
            'success' => 'Item deleted successfully'
        ]), $this->client->getResponse()->getContent());
        
        $itemDeleted = $this->entityManager->getRepository(Item::class)->find(6);
        
        $this->assertNull($itemDeleted);        
    }
    
    public function testAddPropertiesToItem()
    {
        $expectedData = [
            'properties' => [
                'vegetarian',
                'glutenfree',
                'spicy'
            ]
        ];
        
        $this->client->request('POST', '/api/item/5/properties'.$this->apiKeyParameter,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode($expectedData),
        );
        
        $this->assertResponseIsSuccessful();
        
        $item = $this->entityManager->getRepository(Item::class)->find(5);
        $properties = $item->getProperties();
        
        foreach ($properties as $property) {
            $actualData['properties'][] = $property->getDesignation();
        }
                
        $this->assertEquals($expectedData, $actualData);
    }
        
    protected function tearDown(): void
    {
        parent::tearDown();
        
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
