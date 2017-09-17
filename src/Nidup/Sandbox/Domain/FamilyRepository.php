<?php

namespace Nidup\Sandbox\Domain;

interface FamilyRepository
{
    public function get(string $code): Family;
    public function add(Family $family);
    public function count(): int;
    public function all(): array;
}
