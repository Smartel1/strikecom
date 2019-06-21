<?php


namespace App\Entities\Interfaces;



interface Names
{
    public function getNameRu();

    public function setNameRu($name_ru): void;

    public function getNameEn();

    public function setNameEn($name_en): void;

    public function getNameEs();

    public function setNameEs($name_es): void;

    public function getNameByLocale(string $locale) : ?string;
}