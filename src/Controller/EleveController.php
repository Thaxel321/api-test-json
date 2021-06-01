<?php

namespace App\Controller;

use App\Entity\Eleves;
use App\Entity\Notes;
use App\Repository\ElevesRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class EleveController extends AbstractController
{

    /**
     * @Route ("/", name="home")
     */

    public function index(){
        return $this->render('eleve/index.html.twig');
    }

    /**
     * @Route("/api/eleve", name="api_eleve", methods={"GET"})
     * @OA\Get(
     *     tags={"Eleve"},
     *     path="/api/eleve",
     *     summary="Affiche tout les élèves",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref=@Model(type=Eleves::class))
     *          )
     *      ),
     *     @OA\Response (response="200", description="Succès"),
     *     @OA\Response (response="400", description="Erreur requête"),
     *     @OA\Response (response="500", description="Erreur Serveur"),
     * )
     */

    public function showEleve(ElevesRepository $eleveRepository): Response
    {
        $eleves = $eleveRepository->findAll();
        return $this->json($eleves, 200, [], [
            'groups' => 'eleve'
        ]);
    }

    /**
     * @Route("/api/eleve/new", name="api_eleve_new", methods={"POST"})
     * @OA\Post(
     *     tags={"Eleve"},
     *     path="/api/eleve/new",
     *     summary="Ajoute un nouvel élève",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"nom", "prenom"},
     *              @OA\Property (type="string", property="nom"),
     *              @OA\Property (type="string", property="prenom"),
     *              @OA\Property ( type="string", property="dateDeNaissance",
     *                             format="date")
     *          )
     *      )
     * )
     */

    public function createEleve(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,
                                ValidatorInterface $validator)
    {
        $jsonRecu = $request->getContent();
        try {

            $eleve = $serializer->deserialize($jsonRecu, Eleves::class, 'json');

            $errors = $validator->validate($eleve);

            if (count($errors) > 0){
                return $this->json($errors, 400);
            }

            $em->persist($eleve);
            $em->flush();

            return $this->json($eleve, 201, [], ['groups' => 'eleve']);
        } catch (NotEncodableValueException $e){
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }




    /**
     * @Route ("/api/eleve/edit/{id}", name="api_eleve_edit", methods={"PUT"})
     * * @OA\Put(
     *     tags={"Eleve"},
     *     path="/api/eleve/edit/{id}",
     *     summary="Editer un eleve",
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
     *              @OA\Property (type="string", property="dateDeNaissance", format="date"),
     *          )
     *      ),
     *     @OA\Response (response="200", description="Succès"),
     *     @OA\Response (response="400", description="Erreur requête"),
     *     @OA\Response (response="500", description="Erreur Serveur"),
     * )
     */

    public function editEleve(int $id, Request $request, EntityManagerInterface $em){

        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());

        $eleve = $em->getRepository(Eleves::class)->find($id);

        try {

            if ($data['nom'] == "" || $data['prenom'] == "" || $data['dateDeNaissance'] == "") {
                return $this->json('Les champs ne doivent pas être vide', 400);
            }

            $eleve->setNom($data['nom']);
            $eleve->setPrenom($data['prenom']);


            $em->flush();

            return $this->json($eleve, 201, [], ['groups' => 'eleve']);
        } catch (NotEncodableValueException $e){
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @Route ("/api/eleve/remove/{id}", name="api_eleve_remove", methods={"DELETE"})
     * @OA\Delete(
     *     tags={"Eleve"},
     *     path="/api_eleve/remove/{id}",
     *     summary="Supprimer un eleve",
     *
     *     @OA\Response (response="200", description="Succès"),
     *     @OA\Response (response="400", description="Erreur requête"),
     *     @OA\Response (response="500", description="Erreur Serveur"),
     * )
     */

    public function supprimerEleve(int $id, EntityManagerInterface $em){

        try{
            $eleve = $em->getRepository(Eleves::class)->find($id);

            if ($eleve == null){
                return $this->json("Aucun élève ne possède l'id (" .$id.")" , 400);
            }

            $em->remove($eleve);
            $em->flush();

            return $this->json("L'élève possèdant l'id ".$id." a été supprimé",
                200, [], []);
        } catch (NotEncodableValueException $e){
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }

    }

    /**
     * @Route("/api/eleve/{id}/note", name="moyenneEleve", methods={"GET"})
     * @OA\Get(
     *     tags={"Eleve"},
     *     summary="Moyenne élève",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          @OA\Schema(type="integer"),
     *      )
     * )
     */

    public function moyenneEleve(int $id, EntityManagerInterface $em){

        $notes = $em->getRepository(Notes::class)
            ->findBy(array('eleves' => $id));

        $total = 0;
        foreach ($notes as $note){

            echo $note->getValeur(). "\n";

        }

        return $this->json('', 201, [], ['groups' => 'eleve']);
    }

}

