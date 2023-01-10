<?php

namespace App\Entity;

use App\Repository\NoticiaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NoticiaRepository::class)]
class Noticia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("Noticias:read")]
    private ?int $id = null;

    #[Groups("Noticias:read")]
    #[ORM\Column(length: 255)]
    private ?string $foto = null;

    #[Groups("Noticias:read")]
    #[ORM\Column(length: 255)]
    private ?string $titulo = null;

    #[ORM\ManyToOne(inversedBy: 'noticias')]
    private ?User $autor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(string $foto): self
    {
        $this->foto = $foto;

        return $this;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getAutor(): ?USer
    {
        return $this->autor;
    }

    public function setAutor(?USer $autor): self
    {
        $this->autor = $autor;

        return $this;
    }
}
