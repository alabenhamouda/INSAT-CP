<?php

namespace App\Controller;

use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagsController extends AbstractController
{
    /**
     * @Route("/tags", name="tags")
     */
    public function index(EntityManagerInterface $em): Response
    {
        $tags = $em->getRepository(Tag::class)->findAll();
        return $this->render('tags/index.html.twig', [
            'tags' => $tags
        ]);
    }

    /**
     * @Route("/tags/{id}/edit", name="edit_tag")
     */
    public function editTag(Request $request, Tag $tag, EntityManagerInterface $entity)
    {
        if ($request->getMethod() == "POST") {
            $name = $request->get("name");
            $tag->setName($name);
            $entity->persist($tag);
            $entity->flush();
            return $this->redirectToRoute("tags");
        } else {
            return $this->render("tags/editTag.html.twig", [
                'tag' => $tag
            ]);
        }
    }

    /**
     * @Route("/tags/{id}/remove", name="remove_tag", methods={"POST"})
     */
    public function removeTag(Tag $tag, EntityManagerInterface $entity)
    {
        $entity->remove($tag);
        $entity->flush();
        return $this->redirectToRoute("tags");
    }

    /**
     * @Route("/tags/add", name="add_tag")
     */
    public function addTag(EntityManagerInterface $entity)
    {
        $tag = new Tag();
        $tag->setName("");
        $entity->persist($tag);
        $entity->flush();
        return $this->redirectToRoute("edit_tag", [
            'id' => $tag->getId()
        ]);
    }
}
