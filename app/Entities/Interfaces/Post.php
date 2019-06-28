<?php


namespace App\Entities\Interfaces;



use App\Entities\User;

interface Post extends Titles, Commentable
{
    public function getId();

    public function getAuthor(): ?User;

    public function setAuthor(?User $author);
}