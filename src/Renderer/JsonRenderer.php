<?php

declare(strict_types=1);

namespace GabrielDeTassigny\Blog\Renderer;

class JsonRenderer
{
    public function render(array $values)
    {
        echo json_encode($values);
    }
}