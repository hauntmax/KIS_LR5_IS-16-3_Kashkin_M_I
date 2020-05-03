<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Plan;
use App\Entity\Category;
use App\Entity\Announcement;

class AnnouncementsController extends AbstractController
{
    public function GetAnnouncements()
    {
        $announcements = $this->getDoctrine()
            ->getRepository(Announcement::class)
            ->findAll();
        if (!$announcements){
            return new Response("Объявлений нет");
        }
        $arrayCollection = array();

        foreach($announcements as $announcement) {
            $arrayCollection[] = array(
                'id' => $announcement->getId(),
                'name' => $announcement->getName(),
                'description' => $announcement->getDescription(),
                'price' => $announcement->getPrice(),
                'locality' => $announcement->getLocality(),
                'isActive' => $announcement->getIsActive(),
                'user' => $announcement->getUser(),
                'category' => $announcement->getCategory(),
                'plan' => $announcement->getPlan(),
                'created_at' => $announcement->getCreatedTime(),
            );
        }

        return new JsonResponse($arrayCollection);
    }

    public function GetAnnouncementsOfUser($userId)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($userId);
        if(!$user) return new Response('Пользователь с идентификатором '.$userId.' не найден');
        $announcements = $user->getAnnouncements();
        if(count($announcements)===0) return new Response('Пользователь с идентификатором '.$userId.' не опубликовал ни одного объявления');
        $arrayCollection = array();

        foreach($announcements as $announcement) {
            $arrayCollection[] = array(
                'id' => $announcement->getId(),
                'name' => $announcement->getName(),
                'description' => $announcement->getDescription(),
                'price' => $announcement->getPrice(),
                'locality' => $announcement->getLocality(),
                'isActive' => $announcement->getIsActive(),
                'user' => $userId,
                'category' => $announcement->getCategory(),
                'plan' => $announcement->getPlan(),
                'created_at' => $announcement->getCreatedTime(),
            );
        }

        return new JsonResponse($arrayCollection);
    }

    public function GetAnnouncementsOfCategory($categoryId)
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($categoryId);
        if(!$category) return new Response('Категория с идентификатором '.$categoryId.' не найдена');
        $announcements = $category->getAnnouncements();
        if(count($announcements)===0) return new Response('Категория с идентификатором '.$categoryId.' не содержит ни одного объявления');
        $arrayCollection = array();

        foreach($announcements as $announcement) {
            $arrayCollection[] = array(
                'id' => $announcement->getId(),
                'name' => $announcement->getName(),
                'description' => $announcement->getDescription(),
                'price' => $announcement->getPrice(),
                'locality' => $announcement->getLocality(),
                'isActive' => $announcement->getIsActive(),
                'user' => $announcement->getUser(),
                'category' => $categoryId,
                'plan' => $announcement->getPlan(),
                'created_at' => $announcement->getCreatedTime(),
            );
        }

        return new JsonResponse($arrayCollection);
    }



    public function GetAnnouncement($id)
    {
        $announcement = $this->getDoctrine()
            ->getRepository(Announcement::class)
            ->find($id);
        if (!$announcement){
            return new Response('Объявление не найдено');
        }
        $fileJSON = [
            'id' => $announcement->getId(),
            'name' => $announcement->getName(),
            'description' => $announcement->getDescription(),
            'price' => $announcement->getPrice(),
            'locality' => $announcement->getLocality(),
            'isActive' => $announcement->getIsActive(),
            'user' => $announcement->getUser(),
            'category' => $announcement->getCategory(),
            'plan' => $announcement->getPlan(),
            'created_at' => $announcement->getCreatedTime(),
        ];
        return new JsonResponse($fileJSON);
    }

    public function PostAnnouncement(Request $request):Response{
        $entityManager = $this->getDoctrine()->getManager();
        $announcement = new Announcement();
        $userId = $request->request->get('userId');
        $planId = $request->request->get('planId');
        $categoryId = $request->request->get('categoryId');
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($userId);
        if(!$user){
            return new Response('Пользователь не найден');
        }
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($categoryId);
        if(!$category){
            return new Response('Категория не найдена');
        }
        $plan = $this->getDoctrine()
            ->getRepository(Plan::class)
            ->find($planId);
        if(!$plan){
            return new Response('План не найден');
        }

        $announcement->setName($request->request->get('name'));
        $announcement->setDescription($request->request->get('description'));
        $announcement->setPrice($request->request->get('price'));
        $announcement->setIsActive($request->request->get('isActive'));
        $announcement->setLocality($request->request->get('locality'));
        $announcement->setCreatedTime(new \DateTime('now'));
        $announcement->setUser($user);
        $announcement->setCategory($category);
        $announcement->setPlan($plan);

        $entityManager->persist($announcement);
        $entityManager->flush();
        return new Response('Объявление с идентификатором '.$announcement->getId().' было успешно создано');
    }

    public function PutAnnouncement($id, Request $request):Response{
        $entityManager = $this->getDoctrine()->getManager();
        $announcement = $this->getDoctrine()
            ->getRepository(Announcement::class)
            ->find($id);
        if (!$announcement){
            $announcement = new Announcement();
            $userId = $request->request->get('userId');
            $planId = $request->request->get('planId');
            $categoryId = $request->request->get('categoryId');
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->find($userId);
            if(!$user){
                return new Response('Пользователь не найден');
            }
            $category = $this->getDoctrine()
                ->getRepository(Category::class)
                ->find($categoryId);
            if(!$category){
                return new Response('Категория не найдена');
            }
            $plan = $this->getDoctrine()
                ->getRepository(Plan::class)
                ->find($planId);
            if(!$plan){
                return new Response('План не найден');
            }

            $announcement->setName($request->request->get('name'));
            $announcement->setDescription($request->request->get('description'));
            $announcement->setPrice($request->request->get('price'));
            $announcement->setIsActive($request->request->get('isActive'));
            $announcement->setLocality($request->request->get('locality'));
            $announcement->setCreatedTime(new \DateTime('now'));
            $announcement->setUser($user);
            $announcement->setCategory($category);
            $announcement->setPlan($plan);

            $entityManager->persist($announcement);
            $entityManager->flush();
            return new Response('Объявление с идентификатором '.$announcement->getId().' было успешно создано');
        }
        else{
            $userId = $request->request->get('userId');
            $planId = $request->request->get('planId');
            $categoryId = $request->request->get('categoryId');
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->find($userId);
            if(!$user){
                return new Response('Пользователь не найден');
            }
            $category = $this->getDoctrine()
                ->getRepository(Category::class)
                ->find($categoryId);
            if(!$category){
                return new Response('Категория не найдена');
            }
            $plan = $this->getDoctrine()
                ->getRepository(Plan::class)
                ->find($planId);
            if(!$plan){
                return new Response('План не найден');
            }

            $announcement->setName($request->request->get('name'));
            $announcement->setDescription($request->request->get('description'));
            $announcement->setPrice($request->request->get('price'));
            $announcement->setIsActive($request->request->get('isActive'));
            $announcement->setLocality($request->request->get('locality'));
            $announcement->setCreatedTime(new \DateTime('now'));
            $announcement->setUser($user);
            $announcement->setCategory($category);
            $announcement->setPlan($plan);

            $entityManager->persist($announcement);
            $entityManager->flush();
            return new Response('Объявление с идентификатором '.$announcement->getId().' было успешно изменено');
        }
    }

    public function DeleteAnnouncement($id){
        $entityManager = $this->getDoctrine()->getManager();
        $announcement = $entityManager->getRepository(Announcement::class)->find($id);
        if (!$announcement) return new Response('Объявление не найдено');
        $entityManager->remove($announcement);
        $entityManager->flush();
        return new Response('Объявление с идентификатором '.$id.' было удалено');
    }
}