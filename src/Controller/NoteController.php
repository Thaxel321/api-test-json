<?php

namespace App\Controller;

use App\Entity\Eleves;
use App\Entity\Notes;
use App\Repository\NotesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class NoteController extends AbstractController
{
    /**
     * @Route ("/api/note/new/{id}", name="note", methods={"POST"})
     * @OA\Post (
     *     tags={"Note"},
     *      @OA\Parameter (
     *              name="id",
     *              in="path",
     *              @OA\Schema (type="integer"),
     *          ),
     *     @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"nom", "prenom"},
     *              @OA\Property (type="string", property="matiere"),
     *              @OA\Property (type="integer", property="valeur")
     *          )
     *      )
     * )
     *
     */
    public function showNote(int $id ,Request $request, EntityManagerInterface $em){

        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());

        $eleve = $em->getRepository(Eleves::class)->find($id);

        try {

            #if ($data['nom'] == "" || $data['prenom'] == "" || $data['dateDeNaissance'] == "") {
            #    return $this->json('Les champs ne doivent pas être vide', 400);
            #}

            $note = new Notes();
            $note->setMatiere($data['matiere'])
                 ->setValeur($data['valeur'])
                 ->setEleve($eleve);

            $em->persist($note);
            $em->flush();

            return $this->json($eleve, 201, [], ['groups' => 'eleve']);
        } catch (NotEncodableValueException $e){
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }


    }
}
