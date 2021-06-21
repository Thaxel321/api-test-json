<?php

namespace App\Controller;

use App\Entity\Eleves;
use App\Form\ElevesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

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
     *              @OA\Items(ref=@Model(type=Eleves::class, groups={"readAllEleve"}))
     *          )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Modification de la note impossible les champs sont invalides"
     *      )
     * )
     */

    public function showEleve(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $eleves = $em->getRepository(Eleves::class)->findAll();
        return $this->json($eleves, 200, [], [
            'groups' => 'readAllEleve'
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
     *               @OA\Property(type="string", property="prenom"),
     *               @OA\Property(type="string", property="nom"),
     *               @OA\Property(type="string", property="dateDeNaissance", format="date"),
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Ajout de l'élève réussit",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref=@Model(type=Eleves::class, groups={"readEleve"}))
     *          )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Le(s) champ(s) ne sont pas valide"
     *      )
     * )
     */

    public function createEleve(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $eleve = new Eleves();

        return $this->saveEleve($eleve, $data);

    }

    /**
     * @Route ("/api/eleve/{id}", name="api_eleve_edit", methods={"PUT"})
     * * @OA\Put(
     *     tags={"Eleve"},
     *     summary="Editer un eleve",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(title="contentEleve" , ref=@Model(type=Eleves::class, groups={"createEleve"}))
     *          ),
     *      ),
     *     @OA\Response(
     *          response="200",
     *          description="Modification de l'élève réussit",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Eleves2")
     *          )
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Le(s) champ(s) ne sont pas valide"
     *      )
     * )
     */

    public function editEleve(int $id, Request $request){

        $em = $this->getDoctrine()->getManager();
        $eleve = $em->getRepository(Eleves::class)->find($id);

        if (!$eleve) {
            throw new ResourceNotFoundException("Resource $id not found");
        }

        $data=json_decode($request->getContent(), true);

        return $this->saveEleve($eleve, $data[0]);

    }

    /**
     * @Route ("/api/eleve/{id}", name="api_eleve_remove", methods={"DELETE"})
     * @OA\Delete(
     *     tags={"Eleve"},
     *     summary="Supprimer un eleve",
     *     @OA\Response (response="200", description="La suppression de l'élève
     *                      a été faite avec succès",
     *
     *      ),
     *     @OA\Response (response="400", description="Id inexistant")
     * )
     */

    public function supprimerEleve(int $id)
    {

        $em = $this->getDoctrine()->getManager();
        $eleve = $em->getRepository(Eleves::class)->find($id);

        if (!$eleve) {
            throw new ResourceNotFoundException("Resource $id not found");
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

    public function averageEleve(Eleves $eleve)
    {

        return $this->json(['average' => $eleve->getAverageNote()], 201, [], ['groups' => 'readEleve']);
    }

    private function saveEleve($eleve, $data)
    {

        $requestBody = $data;

        $form = $this->createForm(ElevesType::class, $eleve);

        $form->submit($requestBody);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $em->persist($eleve);
            $em->flush();

            return $this->json($eleve, 201, [], ['groups' => 'readEleve']);

        } else{
            return $this->json('erreur', 201, []);

        }

    }

}

