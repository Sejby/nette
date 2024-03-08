<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

final class EditPresenter extends Nette\Application\UI\Presenter
{
    public function __construct(private Nette\Database\Explorer $database)
    {
    }

    public function startup(): void{
        parent::startup();

        if(!$this->user->isLoggedIn()) {
            $this->redirect("Log:in");
        }
    }

    public function createComponentEditForm(): Form
    {
        $form = new Form;
        $form->addText('title', 'Titulek:')
            ->setRequired("");
        $form->addTextArea('content', 'Obsah:')
            ->setRequired("");

        $form->addSubmit('send', 'Uložit a publikovat');
        $form->onSuccess[] = $this->editFormSucceeded(...);
        return $form;
    }

    private function editFormSucceeded(array $data): void
    {
        $postId = $this->getParameter('postId');

        if ($postId) {
            $post = $this->database->table('posts')->get($postId);
            $post->update($data);
        } else {
            $posts = $this->database->table('posts');
            $posts->insert($data);
        }

        $this->flashMessage('Příspěvek úspěšně přidán!', 'success');
        $this->redirect('Post:show', $post->id);
    }

    public function renderEdit(int $postId): void
    {
        $post = $this->database->table('posts')->get($postId);

        if (!$post) {
            $this->error('Post nebyl nalezen');
        }

        $this['editForm']->getForm()->setDefaults($post->toArray());
    }
}
