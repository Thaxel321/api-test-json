<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Repository\EleveRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class PersonneController extends AbstractController
{

    /**
     * @Route ("/", name="home")
     */

    public function index(){
        return $this->render('eleve/index.html.twig');
    }


    /**
     * @Route("/api_eleve", name="api_eleve", methods={"GET"})
     *@OA\Get(
     *     path="/api_personne",
     *     summary="Affiche tout les élèves"
     * )
     * @OA\Response(
     *     response=200,
     *     description="Ok",
     *     @OA\JsonContent(
     *          type="array",
     *          @OA\Items(ref=@Model(type=Eleve::class))
     *      )
     * )
     */

    public function showEleve(EleveRepository $eleveRepository): Response
    {
        $eleves = $eleveRepository->findAll();
        return $this->json($eleves, 200, [], [
            'groups' => 'test'
        ]);
    }

    /**
     * @Route("/api_eleve/new", name="api_eleve_new", methods={"POST"})
     * @OA\Post(
     *     path="/api_eleve/new",
     *     summary="Ajoute un nouvel élève",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"nom", "prenom"},
     *              @OA\Property (type="string", property="nom"),
     *              @OA\Property (type="string", property="prenom"),
     *              @OA\Property (type="string", property="dateDeNaissance", format="date"),
     *          )
     *      )
     *
     * )
     */

    public function createPersonne(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,
                                   ValidatorInterface $validator)
    {
        $jsonRecu = $request->getContent();
        try {

            $personne = $serializer->deserialize($jsonRecu, Eleve::class, 'json');

            $errors = $validator->validate($personne);

            if (count($errors) > 0){
                return $this->json($errors, 400);
            }

            $em->persist($personne);
            $em->flush();

            return $this->json($personne, 201, [], ['groups' => 'eleve']);
        } catch (NotEncodableValueException $e){
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @Route ("/api_personne/edit/{id}", name="api_personne_edit", methods={"PUT"})
     * * @OA\Put(
     *     path="/api_personne/edit/{id}",
     *     summary="Editer une eleve",
     *     @OA\Parameter (
     *              name="id",
     *              in="path",
     *              @OA\Schema (type="integer"),
     *          ),
     *     @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"nom", "prenom"},
     *              @OA\Property (type="string", property="nom"),
     *              @OA\Property (type="string", property="prenom"),
     *          )
     *      ),
     *     @OA\Response (response="200", description="Succès"),
     *     @OA\Response (response="400", description="Erreur requête"),
     *     @OA\Response (response="500", description="Erreur Serveur"),
     * )
     */

    public function editPersonne(int $id, Request $request, EntityManagerInterface $em){

        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());

        $personne = $em->getRepository(Personne::class)->find($id);

        try {

            if ($data['nom'] == "" || $data['prenom'] == "") {
                return $this->json('Les champs ne doivent pas être vide', 400);
            }

            $personne->setNom($data['nom']);
            $personne->setPrenom($data['prenom']);

            $em->flush();

            return $this->json($personne, 201, [], ['groups' => 'eleve']);
        } catch (NotEncodableValueException $e){
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @Route ("/api_personne/remove/{id}", name="api_personne_remove", methods={"DELETE"})
     * * @OA\Delete(
     *     path="/api_personne/remove/{id}",
     *     summary="Supprimer une eleve",
     *
     *     @OA\Response (response="200", description="Succès"),
     *     @OA\Response (response="400", description="Erreur requête"),
     *     @OA\Response (response="500", description="Erreur Serveur"),
     * )
     */

    public function supprimerPersonne(int $id, EntityManagerInterface $em,
                                      ValidatorInterface $validator){

        try{
            $personne = $em->getRepository(Personne::class)->find($id);

            if ($personne == null){
                return $this->json("Aucune eleve ne possède l'id (" .$id.")" , 400);
            }

            $em->remove($personne);
            $em->flush();

            return $this->json("La eleve possèdant l'id ".$id." a été supprimé",
                200, [], []);
        } catch (NotEncodableValueException $e){
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }

    }

}
