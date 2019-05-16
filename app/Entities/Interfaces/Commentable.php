<?php


namespace App\Entities\Interfaces;


use App\Entities\Comment;
use Doctrine\Common\Collections\ArrayCollection;

interface Commentable
{
    /**
     * @return Comment[]|ArrayCollection
     */
    public function getComments();

    /**
     * @param Comment[]|ArrayCollection $comments
     */
    public function setComments($comments): void;
}