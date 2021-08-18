<?php

namespace App\Controller;

use App\Repository\PetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PetController
 * @package App\Controller
 * 
 * @Route(path="/api/")
 */
class PetController extends AbstractController
{
    private $petRepository;
    public function __construct(PetRepository $petRepository){
        $this->petRepository = $petRepository;
    }
    /**
     * @Route("pet", name="add_pet", methods={"POST"})
     */
    public function add_pet(Request $request): Response{
        $data = json_decode($request->getContent(),true);
        $name = $data['name'];
        $type = $data['type'];
        $photoUrls = $data['photoUrls'];
        if (empty($data['name'])|| empty($data['type'])) {
            throw new NotFoundHttpException('Expecting Mandatory Parameters!');
        }
        $pet = $this->petRepository->savePet($name,$type,$photoUrls);
        return $this->json(['Message'=>'ok', 'Pet_data' => $data]);
    }
    /**
     * @Route("pet", name="get_pets", methods={"GET"})
     */
    public function get_pets(): JsonResponse{
        $pets = $this->petRepository->findAll();
        foreach ($pets as $pet) {
             $data[]=[
                'id' => $pet->getId(),
                'name' => $pet->getName(),
                'type' => $pet->getType(),
                'photoUrls'=> $pet->getPhotoUrls()
             ];
         }
         return new JsonResponse(['Message'=> 'ok', 'Pet_Data'=>$data]);
    }
    /**
     * @Route("pet/{id}", name="get_pet", methods={"GET"})
     */
    public function get_pet($id): JsonResponse{
        $pet = $this->petRepository->findOneBy(['id'=>$id]);
        $data[]=[
                'id' => $pet->getId(),
                'name' => $pet->getName(),
                'type' => $pet->getType(),
                'photoUrls'=> $pet->getPhotoUrls()
             ];
         return new JsonResponse(['Message'=> 'ok', 'Pet_Data'=>$data]);
    }
    /**
     * @Route("pet/{id}", name="update_pet", methods={"PUT"})
     */
    public function update_pet($id, Request $request): JsonResponse{
        $pet = $this->petRepository->findOneBy(['id'=>$id]);
        $data= json_decode($request->getContent(), true);

    if (empty($data['name']) || empty($data['type']) || empty($data['photoUrls'])    ) {
        throw new NotFoundHttpException('Expecting Mandatory Parameters!');
        }
        $pet->setName($data['name']);
        $pet->setType($data['type']);
        $pet->setPhotoUrls($data['photoUrls']);
        $pet->setUpdatedAt(new \DateTimeImmutable());

        $updatedPet = $this->petRepository->updatePet($pet); 
         return new JsonResponse(['Message'=> 'ok', 'Pet_Data'=>$data]);
    }
    /**
     * @Route("pet/{id}", name="delete_pet", methods={"DELETE"})
     */
    public function delete_pet($id): JsonResponse{
        $pet = $this->petRepository->findOneBy(['id'=>$id]);
        $this->petRepository->removePet($pet);
        return new JsonResponse(['Message'=>'Pet deleted']);
    }

}
