<?php

namespace App\Controller;

use App\Entity\Eleves;
use App\Repository\ElevesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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
     *     summary="Affiche tout les élèves",
     *     @OA\Response(
     *          response="200",
     *          description="Affichage de la liste de tout les élèves réussit",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref=@Model(type=Eleves::class, groups={"eleve"}))
     *
     *          )
     *
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Modification de la note impossible les champs sont invalides"
     *      )
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
     * @Route("/api/eleve", name="api_eleve_new", methods={"POST"})
     * @OA\Post(
     *     tags={"Eleve"},
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
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Ajout de l'élève réussit",
     *          @OA\JsonContent(
     *              @OA\Property (type="integer", property="id"),
     *              @OA\Property (type="string", property="nom"),
     *              @OA\Property (type="string", property="prenom"),
     *              @OA\Property ( type="string", property="dateDeNaissance",
     *                             format="date")
     *          )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Le(s) champ(s) ne sont pas valide"
     *      )
     * )
     */

    public function createEleve(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,
                                ValidatorInterface $validator)
    {
        $jsonRecu = $request->getContent();

            $eleve = $serializer->deserialize($jsonRecu, Eleves::class, 'json');

            $errors = $validator->validate($eleve);

            if (count($errors) > 0){
                return $this->json($errors, 400);
            }

            $em->persist($eleve);
            $em->flush();

            return $this->json($eleve, 201, [], ['groups' => 'eleve']);

    }

    /**
     * @Route ("/api/eleve", name="api_eleve_edit", methods={"PUT"})
     * * @OA\Put(
     *     tags={"Eleve"},
     *     summary="Editer un eleve",
     *     @OA\Parameter (
     *              name="id",
     *              in="path",
     *              description="L'id le l'élève correspondant",
     *              @OA\Schema (type="integer"),
     *          ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"nom", "prenom"},
     *              @OA\Property (type="string", property="nom"),
     *              @OA\Property (type="string", property="prenom"),
     *              @OA\Property (type="string", property="dateDeNaissance", format="date"),
     *          )
     *      ),
     *     @OA\Response(
     *          response="200",
     *          description="Modification de l'élève réussit",
     *          @OA\JsonContent(
     *              required={"nom", "prenom"},
     *              @OA\Property (type="integer", property="id"),
     *              @OA\Property (type="string", property="nom"),
     *              @OA\Property (type="string", property="prenom"),
     *              @OA\Property (type="string", property="dateDeNaissance", format="date"),
     *          )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Le(s) champ(s) ne sont pas valide"
     *      )
     * )
     */

    public function editEleve(int $id, Request $request, EntityManagerInterface $em){

        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());

        $eleve = $em->getRepository(Eleves::class)->find($id);


            if ($data['nom'] == "" || $data['prenom'] == "" || $data['dateDeNaissance'] == "")
            {
                return $this->json('Les champs ne doivent pas être vide', 400);
            }

            $eleve->setNom($data['nom']);
            $eleve->setPrenom($data['prenom']);

            $em->flush();

            return $this->json($eleve, 201, [], ['groups' => 'eleve']);

    }

    /**
     * @Route ("/api/eleve/{id}", name="api_eleve_remove", methods={"DELETE"})
     * @OA\Delete(
     *     tags={"Eleve"},
     *     summary="Supprimer un eleve",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="L'id de l'élève correspondant",
     *          @OA\Property(type="integer"),
     *      ),
     *     @OA\Response (response="200", description="La suppression de l'élève
     *                      a été faite avec succès",
     *
     *      ),
     *     @OA\Response (response="400", description="Id inexistant")
     * )
     */

    public function supprimerEleve(int $id, EntityManagerInterface $em){

            $eleve = $em->getRepository(Eleves::class)->find($id);

            if ($eleve == null)
            {
                return $this->json("Aucun élève ne possède l'id (" .$id.")" , 400);
            }

            $em->remove($eleve);
            $em->flush();

            return $this->json("L'élève possèdant l'id ".$id." a été supprimé",
                200, [], []);

    }

    /**
     * @Route("/api/eleve/{id}", name="averageEleve", methods={"GET"})
     * @OA\Get(
     *     tags={"Eleve"},
     *     summary="Moyenne d'un élève",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="L'id de l'élève correspondant",
     *          @OA\Schema(type="integer"),
     *      ),
     *      @OA\Response (
     *          response="200",
     *          description="Affichage de la moyenne de l'élève avec succès",
     *          @OA\JsonContent(
     *              @OA\Property(type="integer", property="Moyenne de l'Eleve"),
     *          ),
     *      ),
     *     @OA\Response (response="400", description="Id inexistant")
     * )
     */

    public function averageEleve(Eleves $eleve){

        return $this->json(['average' => $eleve->getAverageNote()], 201, [], ['groups' => 'eleve']);
    }

}

