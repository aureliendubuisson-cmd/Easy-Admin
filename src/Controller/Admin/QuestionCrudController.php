<?php

namespace App\Controller\Admin;

use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class QuestionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Question::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setPageTitle(Crud::PAGE_DETAIL, fn(Question $entityInstance) => $entityInstance->getName());
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);
        $customAction = Action::new('Test', 'Vote', 'fas fa-circle fa-check')->linkToCrudAction('upVote');

        return $actions->add(Crud::PAGE_DETAIL, $customAction);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom')
            ->formatValue(fn ($value): ?string => 'Titre: '.$value),
            TextField::new('question')
            ->hideOnDetail(),
            NumberField::new('votes')
        ];
    }
    public function upVote(AdminContext $context, EntityManagerInterface $em, AdminUrlGenerator $adminUrlGenerator): Response
    {
        $question = $context->getEntity()->getInstance();
        $question->upVote();
        parent::updateEntity($em, $question);

        return $this->redirect(
            $adminUrlGenerator
                ->setController(QuestionCrudController::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($question->getId())
            ->generateUrl()
        );
    }

}
