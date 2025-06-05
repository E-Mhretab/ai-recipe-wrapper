<?php
class Recipe
{
    public string $naam;
    public array $ingrediënten;
    public string $bereidingstijd;
    public array $stappen;
    public string $moeilijkheidsgraad;

    public function __construct(
        string $naam,
        array $ingrediënten,
        string $bereidingstijd,
        array $stappen,
        string $moeilijkheidsgraad
    ) {
        $this->naam = $naam;
        $this->ingrediënten = $ingrediënten;
        $this->bereidingstijd = $bereidingstijd;
        $this->stappen = $stappen;
        $this->moeilijkheidsgraad = $moeilijkheidsgraad;
    }
}
