<?php


namespace App\Entities\Interfaces;



interface Titles
{
    public function getTitleRu();

    public function setTitleRu($title_ru): void;

    public function getTitleEn();

    public function setTitleEn($title_en): void;

    public function getTitleEs();

    public function setTitleEs($title_es): void;

    public function getTitleByLocale(string $locale) : ?string;
}