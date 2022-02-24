<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ItemRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Item;
use App\Entity\ItemProperty;

class ApiController extends AbstractController
{
    public function index(Request $request, ItemRepository $repository): Response
    {        
        $items = $repository->findAllAssoc();
                
        return $this->json($items);
    }
    
    public function addItem(Request $request, EntityManagerInterface $em)
    {
        try {
            $data = $request->toArray();
            
            if (empty($data['name']) || empty($data['price'])) {
                throw new \Exception();
            }
            
            $item = new Item();
            $item->setName($data['name']);
            $item->setPrice($data['price']);
            
            $em->persist($item);
            $em->flush();
            
            $data = [
                'status' => 200,
                'success' => 'Item added successfully'
            ];           
            
        } catch (\Exception $e) {
            $data = [
                'status' => 422,
                'errors' => 'Data no valid'
            ];
        }        
        
        return $this->json($data, $data['status']);
    }
    
    public function updateItem(Request $request, EntityManagerInterface $em, $id)
    {
        try {
            $item = $em->getRepository(Item::class)->find($id);
            
            if (!$item) {
                throw new \Exception('Item not found', 404);                
            }
            
            $data = $request->toArray();
            
            if (empty($data['name']) || empty($data['price'])) {
                throw new \Exception('Data no valid', 422);
            }
            
            $item->setName($data['name']);
            $item->setPrice($data['price']);            
            $em->flush();        
            
            $data = [
                'status' => 200,
                'success' => 'Item updated successfully'
            ];
            
        } catch (\Exception $e) {
            $data = [
                'status' => $e->getCode(),
                'errors' => $e->getMessage()
            ];
        }
        
        return $this->json($data, $data['status']);
    }
    
    public function deleteItem(EntityManagerInterface $em, $id)
    {   
        $item = $em->getRepository(Item::class)->find($id);
        
        if (!$item) {
            $data = [
                'status' => 404,
                'errors' => 'Item not found'
            ];          
            
            return $this->json($data, 404);
        }
        
        $em->remove($item);
        $em->flush();
        
        $data = [
            'status' => 200,
            'success' => 'Item deleted successfully'            
        ];
        
        return $this->json($data);                
    }
    
    public function updateItemProperties(Request $request, EntityManagerInterface $em, $id)
    {
        try {
            $item = $em->getRepository(Item::class)->find($id);
            
            if (!$item) {                
                throw new \Exception('Item not found', 404);
            }
            
            $data = $request->toArray();
            
            if (empty($data['properties'])) {
                throw new \Exception('Data no valid', 422);
            }
            
            foreach ($data['properties'] as $propertyDesignation) {
                $property = new ItemProperty();
                $property->setDesignation($propertyDesignation);
                $item->addProperty($property);
                $em->persist($property);
            }
            
            $em->flush();
            
            $data = [
                'status' => 200,
                'success' => 'The item properties were updated successfully'
            ];
            
        } catch (\Exception $e) {
            $data = [
                'status' => $e->getCode(),
                'errors' => $e->getMessage()
            ];
        }
        
        return $this->json($data, $data['status']);
    }
}
